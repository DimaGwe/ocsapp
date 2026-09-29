<?php
/**
 * Beta Notice Component
 * Shows modal on first visit + persistent banner
 * Set $betaModalOnly = true before including to skip the banner (page has its own bar).
 */

// Get translations
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
?>

<!-- Beta Notice CSS -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap">
<link rel="stylesheet" href="<?= asset('css/beta-notice.css') ?>?v=20260929">

<?php if (!empty($betaModalOnly)): ?>
<?php /* Page draws its own beta bar (e.g. public/landing.php eco-beta): modal only */ ?>
<?php elseif (!empty($useMarcheHeader)): ?>
<!-- Marché Central beta bar (opt-in via $useMarcheHeader, staging redesign 2026-09-05) -->
<div class="mc-beta">
    <span class="mc-beta-badge"><?= $currentLang === 'fr' ? 'Bêta' : 'Beta' ?></span>
    <span class="mc-beta-full"><?= $currentLang === 'fr'
        ? 'Plateforme en cours de développement. Certaines fonctionnalités ne sont pas encore disponibles.'
        : 'Platform under development. Some features are not yet available.'
    ?></span>
    <a href="<?= url('founding') ?>"><?= $currentLang === 'fr' ? 'Devenir fondateur' : 'Become a founder' ?></a>
</div>
<?php else: ?>
<!-- Persistent Beta Banner -->
<div class="beta-banner">
    <div class="beta-banner-content">
        <span class="beta-banner-icon">⚠️</span>
        <div class="beta-banner-text">
            <strong>BETA VERSION</strong> -
            <?= $currentLang === 'fr'
                ? 'Site en test - Veuillez ne pas effectuer d\'achats réels pour le moment'
                : 'Site Under Testing - Please do not make real purchases at this time'
            ?>
            |
            <a href="mailto:info@ocsapp.ca" class="beta-banner-link">
                <?= $currentLang === 'fr' ? 'Signaler un problème' : 'Report Issues' ?>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- First Visit Modal (ecosystem theme, 2026-09-29) -->
<?php $bmFr = ($currentLang === 'fr'); ?>
<div id="betaModalOverlay" class="beta-modal-overlay hidden">
    <div class="beta-modal" role="dialog" aria-modal="true" aria-labelledby="betaModalTitle">
        <div class="beta-modal-header">
            <span class="beta-modal-badge"><?= $bmFr ? 'Bêta' : 'Beta' ?></span>
            <h2 class="beta-modal-title" id="betaModalTitle"><?= $bmFr ? 'Bienvenue sur OCSAPP' : 'Welcome to OCSAPP' ?></h2>
            <p class="beta-modal-subtitle"><?= $bmFr
                ? 'La plateforme est en version bêta. Voici ce qu’il faut savoir avant de continuer.'
                : 'The platform is in beta. Here is what to know before you continue.' ?></p>
        </div>

        <div class="beta-modal-body">
            <div class="beta-modal-notice">
                <span class="beta-modal-notice-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </span>
                <div>
                    <h3 class="beta-modal-notice-title"><?= $bmFr ? 'Avis important' : 'Important notice' ?></h3>
                    <p class="beta-modal-notice-text"><?= $bmFr
                        ? 'Le site est en <strong>phase de test bêta</strong>. Certaines fonctionnalités peuvent ne pas fonctionner correctement et ne sont pas encore prêtes pour une utilisation publique.'
                        : 'The site is in its <strong>beta testing phase</strong>. Some features may not work correctly and are not yet ready for public use.' ?></p>
                </div>
            </div>

            <h4 class="beta-modal-list-title"><?= $bmFr ? 'Ce que cela signifie' : 'What this means' ?></h4>
            <ul class="beta-modal-list">
                <li>
                    <span class="beta-modal-li-icon is-stop" aria-hidden="true"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><line x1="6" y1="6" x2="18" y2="18"/><line x1="18" y1="6" x2="6" y2="18"/></svg></span>
                    <span><strong><?= $bmFr ? 'Aucun achat réel pour l’instant.' : 'No real purchases for now.' ?></strong> <?= $bmFr ? 'Le traitement des paiements est en cours de test.' : 'Payment processing is being tested.' ?></span>
                </li>
                <li>
                    <span class="beta-modal-li-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9a6 6 0 0 1 7.9-7.9l-3.8 3.8z"/></svg></span>
                    <span><strong><?= $bmFr ? 'Fonctionnalités en test.' : 'Features in testing.' ?></strong> <?= $bmFr ? 'Vous pourriez rencontrer des bogues.' : 'You may run into bugs.' ?></span>
                </li>
                <li>
                    <span class="beta-modal-li-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
                    <span><strong><?= $bmFr ? 'Explorez librement.' : 'Explore freely.' ?></strong> <?= $bmFr ? 'Naviguez et essayez les fonctionnalités.' : 'Browse around and try the features.' ?></span>
                </li>
            </ul>

            <div class="beta-modal-info">
                <strong><?= $bmFr ? 'Lancement officiel bientôt' : 'Official launch coming soon' ?></strong>
                <span><?= $bmFr
                    ? 'Vous voulez en faire partie dès le départ? Devenez fondateur.'
                    : 'Want to be part of it from day one? Become a founder.' ?></span>
                <a href="<?= url('founding') ?>"><?= $bmFr ? 'Programmes fondateurs' : 'Founding programs' ?> &rarr;</a>
            </div>

            <p class="beta-modal-contact"><?= $bmFr ? 'Un problème? Écrivez-nous :' : 'Found an issue? Write to us:' ?>
                <a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a></p>
        </div>

        <div class="beta-modal-footer">
            <button type="button" id="betaAcknowledgeBtn" class="beta-modal-button"><?= $bmFr ? 'J’ai compris, continuer' : 'I understand, continue' ?></button>
            <p class="beta-modal-disclaimer"><?= $bmFr
                ? 'En cliquant, vous reconnaissez que le site est en version bêta.'
                : 'By clicking, you acknowledge that the site is in beta.' ?></p>
        </div>
    </div>
</div>

<!-- Beta Notice JavaScript -->
<script>
(function() {
    'use strict';

    function updateBannerOffset() {
        const banner = document.querySelector('.beta-banner');
        if (banner) {
            document.documentElement.style.setProperty('--beta-banner-h', banner.offsetHeight + 'px');
        }
    }
    updateBannerOffset();
    window.addEventListener('resize', updateBannerOffset);

    const STORAGE_KEY = 'ocs_beta_acknowledged';
    const overlay = document.getElementById('betaModalOverlay');
    const acknowledgeBtn = document.getElementById('betaAcknowledgeBtn');

    // Check if user has already acknowledged
    function hasAcknowledged() {
        return localStorage.getItem(STORAGE_KEY) === 'true';
    }

    // Show modal if not acknowledged
    function checkAndShowModal() {
        if (!hasAcknowledged()) {
            // Small delay for better UX
            setTimeout(() => {
                overlay.classList.remove('hidden');
                // Prevent body scroll when modal is open
                document.body.style.overflow = 'hidden';
            }, 500);
        }
    }

    // Handle acknowledge button click
    if (acknowledgeBtn) {
        acknowledgeBtn.addEventListener('click', function() {
            // Store acknowledgment
            localStorage.setItem(STORAGE_KEY, 'true');

            // Hide modal with animation
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        });
    }

    // Prevent closing modal by clicking overlay (force acknowledgment)
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            // Only allow closing via the button
            if (e.target === overlay) {
                // Optional: Add shake animation to draw attention to button
                const modal = overlay.querySelector('.beta-modal');
                modal.style.animation = 'shake 0.5s ease';
                setTimeout(() => {
                    modal.style.animation = '';
                }, 500);
            }
        });
    }

    // Show modal on page load if needed
    checkAndShowModal();

    // Add shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
})();
</script>
