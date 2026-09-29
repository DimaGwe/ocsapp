<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/HomeProfileHelper.php';

use App\Helpers\HomeProfileHelper;

/**
 * Home Profile: a parent or guardian adds teens (13 to 17) to their account.
 * Guardian side: /account/home (members, invites, card for member orders, member orders).
 * Member side:   /home-profile/join (accept the emailed invite and create the member account).
 */
class HomeProfileController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    private function fr(): bool
    {
        return ($_SESSION['language'] ?? 'fr') === 'fr';
    }

    private function requireLogin(string $back): void
    {
        if (!isLoggedIn()) {
            redirect('/login?redirect=' . $back);
            exit;
        }
    }

    private function requireCsrf(bool $json = false): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            if ($json) {
                jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            } else {
                setFlash('error', $this->fr() ? 'Session expirée, veuillez réessayer.' : 'Session expired, please try again.');
                redirect(url('account/home'));
            }
            exit;
        }
    }

    // GET /account/home
    public function index(): void
    {
        $this->requireLogin('/account/home');
        $userId = (int) userId();

        if (HomeProfileHelper::isMember($userId)) {
            view('buyer/account/home', ['isMember' => true, 'guardian' => HomeProfileHelper::guardianFor($userId)]);
            return;
        }

        $stmt = $this->db->prepare("SELECT card_brand, card_last4 FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $card = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        $orders = $this->db->prepare("
            SELECT o.id, o.order_number, o.status, o.total, o.created_at, s.name AS shop_name, u.first_name AS member_first_name
            FROM orders o
            JOIN users u ON u.id = o.user_id
            LEFT JOIN shops s ON s.id = o.shop_id
            WHERE o.guardian_user_id = ?
            ORDER BY o.created_at DESC
            LIMIT 25
        ");
        $orders->execute([$userId]);

        $config = getStripeConfig();
        view('buyer/account/home', [
            'isMember' => false,
            'canBeGuardian' => HomeProfileHelper::canBeGuardian($userId),
            'members' => HomeProfileHelper::membersOf($userId),
            'card' => $card,
            'memberOrders' => $orders->fetchAll(\PDO::FETCH_ASSOC),
            'stripeKey' => $config['publishable_key'] ?? '',
        ]);
    }

    // POST /account/home/invite
    public function invite(): void
    {
        $this->requireLogin('/account/home');
        $this->requireCsrf();
        $fr = $this->fr();
        if (HomeProfileHelper::isMember((int) userId())) {
            redirect(url('account/home'));
            return;
        }
        $result = HomeProfileHelper::invite((int) userId(), (string) post('first_name', ''), (string) post('email', ''), (string) post('birth_date', ''), $fr);
        if ($result['success']) {
            setFlash('success', $fr ? 'Invitation envoyée. Elle est valide 7 jours.' : 'Invite sent. It is valid for 7 days.');
        } else {
            setFlash('error', $result['error']);
        }
        redirect(url('account/home'));
    }

    // POST /account/home/remove
    public function remove(): void
    {
        $this->requireLogin('/account/home');
        $this->requireCsrf();
        $ok = HomeProfileHelper::remove((int) userId(), (int) post('link_id', 0));
        $fr = $this->fr();
        setFlash($ok ? 'success' : 'error', $ok
            ? ($fr ? 'Le membre a été retiré de votre Profil Maison.' : 'The member was removed from your Home Profile.')
            : ($fr ? 'Membre introuvable.' : 'Member not found.'));
        redirect(url('account/home'));
    }

    // POST /account/home/card/intent (JSON)
    public function cardIntent(): void
    {
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please log in'], 401);
            return;
        }
        $this->requireCsrf(true);
        if (!HomeProfileHelper::canBeGuardian((int) userId())) {
            jsonResponse(['success' => false, 'message' => 'Not allowed'], 403);
            return;
        }
        try {
            jsonResponse(['success' => true, 'client_secret' => HomeProfileHelper::createSetupIntentClientSecret((int) userId())]);
        } catch (\Exception $e) {
            logger('Home Profile card intent failed: ' . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => $this->fr() ? "Impossible d'ajouter une carte pour le moment." : 'Unable to add a card right now.']);
        }
    }

    // POST /account/home/card/save (JSON)
    public function cardSave(): void
    {
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please log in'], 401);
            return;
        }
        $this->requireCsrf(true);
        if (!HomeProfileHelper::canBeGuardian((int) userId())) {
            jsonResponse(['success' => false, 'message' => 'Not allowed'], 403);
            return;
        }
        try {
            HomeProfileHelper::saveCardFromSetupIntent((int) userId(), sanitize(post('setup_intent_id', '')));
            jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            logger('Home Profile card save failed: ' . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => $this->fr() ? "La carte n'a pas pu être enregistrée." : 'The card could not be saved.']);
        }
    }

    // GET /home-profile/join?token=
    public function joinForm(): void
    {
        $token = (string) get('token', '');
        view('auth/home-profile-join', [
            'token' => $token,
            'invite' => $token !== '' ? HomeProfileHelper::findInvite($token) : null,
        ]);
    }

    // POST /home-profile/join
    public function join(): void
    {
        $fr = $this->fr();
        $token = (string) post('token', '');
        $back = url('home-profile/join?token=' . urlencode($token));
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', $fr ? 'Session expirée, veuillez réessayer.' : 'Session expired, please try again.');
            redirect($back);
            return;
        }
        if (!rateLimit('home_profile_join', 10, 600)) {
            setFlash('error', $fr ? 'Trop de tentatives. Réessayez dans quelques minutes.' : 'Too many attempts. Please try again in a few minutes.');
            redirect($back);
            return;
        }

        $lastName = trim(sanitize(post('last_name', '')));
        $password = (string) post('password', '');
        if ($lastName === '') {
            setFlash('error', $fr ? 'Veuillez entrer votre nom de famille.' : 'Please enter your last name.');
            redirect($back);
            return;
        }
        $pwErrors = validatePasswordStrength($password);
        if (!empty($pwErrors)) {
            setFlash('error', passwordStrengthMessage($pwErrors));
            redirect($back);
            return;
        }
        if ($password !== (string) post('password_confirmation', '')) {
            setFlash('error', passwordMismatchMessage());
            redirect($back);
            return;
        }
        if (post('consent', '') !== 'on') {
            setFlash('error', $fr ? 'Veuillez accepter les conditions pour continuer.' : 'Please accept the terms to continue.');
            redirect($back);
            return;
        }

        $result = HomeProfileHelper::acceptInvite($token, $lastName, $password, $fr);
        if (!$result['success']) {
            setFlash('error', $result['error']);
            redirect($back);
            return;
        }

        setFlash('success', $fr
            ? 'Votre compte du Profil Maison est prêt. Connectez-vous pour commencer.'
            : 'Your Home Profile account is ready. Log in to get started.');
        redirect(url('login'));
    }
}
