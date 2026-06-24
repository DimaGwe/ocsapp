<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr          = ($currentLang === 'fr');

$ast = [
    'en' => [
        'page_title'     => 'Application Status - OCSAPP Driver',
        'hero_badge'     => 'Driver Application Portal',
        'hero_h1'        => 'Hi <span>%s</span>, here\'s your application status',
        'hero_sub'       => 'We\'re reviewing your application to join the OCSAPP delivery team. Track your progress and message us with any questions.',
        'app_id'         => 'Application #',
        'submitted_on'   => 'Submitted',
        'progress_title' => 'Application Progress',
        'stages'         => [
            'submitted'           => 'Submitted',
            'under_review'        => 'Under Review',
            'interview_requested' => 'Interview Offered',
            'interview_scheduled' => 'Interview Booked',
            'approved'            => 'Approved',
            'rejected'            => 'Not Successful',
        ],
        'status_msgs'    => [
            'submitted'           => 'Your application has been received. Our team will review it within 1-3 business days.',
            'under_review'        => 'Your application is being reviewed by our recruitment team. We may reach out with questions.',
            'interview_requested' => "Great news! We'd like to interview you. Please select an available time slot below.",
            'interview_scheduled' => 'Your interview is booked! We look forward to speaking with you. Check the details below.',
            'approved'            => 'Congratulations! Your application has been approved. Check your email for next steps.',
            'rejected'            => "Thank you for your interest. Unfortunately we're unable to move forward at this time.",
        ],
        'interview_title'   => 'Choose Your Interview Time',
        'interview_desc'    => 'Select one of the time slots below. Once confirmed, our team will send you the meeting details.',
        'btn_confirm_time'  => 'Confirm Interview Time',
        'confirmed_title'   => 'Interview Confirmed',
        'confirmed_label'   => 'Your interview is scheduled for:',
        'messages_title'    => 'Messages',
        'no_messages'       => 'No messages yet. Feel free to send us a question!',
        'sender_team'       => 'OCSAPP Team',
        'sender_you'        => 'You',
        'msg_placeholder'   => 'Write a message to our team…',
        'ctrl_enter'        => 'Ctrl+Enter',
        'ctrl_enter_hint'   => 'to send',
        'locale'            => 'en-CA',
        'js_no_slot'        => 'Please select a time slot.',
        'js_confirming'     => 'Confirming…',
        'js_confirm_btn'    => 'Confirm Interview Time',
        'js_fail_confirm'   => 'Failed to confirm. Please try again.',
        'js_fail_send'      => 'Failed to send message.',
        'js_network'        => 'Network error. Please try again.',
    ],
    'fr' => [
        'page_title'     => 'Statut de candidature - OCSAPP Livreur',
        'hero_badge'     => 'Portail de candidature livreur',
        'hero_h1'        => 'Bonjour <span>%s</span>, voici l\'état de votre candidature',
        'hero_sub'       => "Nous examinons votre candidature pour rejoindre l'équipe de livraison OCSAPP. Suivez votre progression et envoyez-nous vos questions.",
        'app_id'         => 'Candidature n°',
        'submitted_on'   => 'Soumise le',
        'progress_title' => 'Progression de la candidature',
        'stages'         => [
            'submitted'           => 'Soumise',
            'under_review'        => 'En révision',
            'interview_requested' => 'Entretien proposé',
            'interview_scheduled' => 'Entretien réservé',
            'approved'            => 'Approuvée',
            'rejected'            => 'Non retenue',
        ],
        'status_msgs'    => [
            'submitted'           => "Votre candidature a été reçue. Notre équipe l'examinera dans les 1 à 3 jours ouvrables.",
            'under_review'        => "Votre candidature est en cours d'examen par notre équipe de recrutement. Nous pourrions vous contacter avec des questions.",
            'interview_requested' => "Bonne nouvelle ! Nous aimerions vous rencontrer en entretien. Veuillez sélectionner un créneau horaire ci-dessous.",
            'interview_scheduled' => "Votre entretien est réservé ! Nous avons hâte de vous parler. Consultez les détails ci-dessous.",
            'approved'            => "Félicitations ! Votre candidature a été approuvée. Consultez votre courriel pour les prochaines étapes.",
            'rejected'            => "Merci de votre intérêt. Malheureusement, nous ne pouvons pas aller de l'avant pour le moment.",
        ],
        'interview_title'   => "Choisissez votre heure d'entretien",
        'interview_desc'    => "Sélectionnez l'un des créneaux ci-dessous. Une fois confirmé, notre équipe vous enverra les détails de la réunion.",
        'btn_confirm_time'  => "Confirmer l'heure d'entretien",
        'confirmed_title'   => 'Entretien confirmé',
        'confirmed_label'   => 'Votre entretien est prévu pour :',
        'messages_title'    => 'Messages',
        'no_messages'       => "Aucun message pour l'instant. N'hésitez pas à nous envoyer une question !",
        'sender_team'       => 'Équipe OCSAPP',
        'sender_you'        => 'Vous',
        'msg_placeholder'   => 'Écrivez un message à notre équipe…',
        'ctrl_enter'        => 'Ctrl+Entrée',
        'ctrl_enter_hint'   => 'pour envoyer',
        'locale'            => 'fr-CA',
        'js_no_slot'        => 'Veuillez sélectionner un créneau horaire.',
        'js_confirming'     => 'Confirmation…',
        'js_confirm_btn'    => "Confirmer l'heure d'entretien",
        'js_fail_confirm'   => 'Échec de la confirmation. Veuillez réessayer.',
        'js_fail_send'      => "Échec de l'envoi du message.",
        'js_network'        => 'Erreur réseau. Veuillez réessayer.',
    ],
];
$ast = $ast[$currentLang] ?? $ast['en'];

// French date formatting helper
function fmtDate(string $dateStr, bool $fr, string $format = 'short'): string {
    $ts = strtotime($dateStr);
    if (!$ts) return $dateStr;
    if (!$fr) {
        return $format === 'long'
            ? date('l, F j, Y \a\t g:i A', $ts)
            : date('M j, Y', $ts);
    }
    $frMonths = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $frDays   = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
    $m = (int)date('n', $ts);
    $d = (int)date('j', $ts);
    $y = date('Y', $ts);
    if ($format === 'long') {
        $dow = (int)date('w', $ts);
        $h   = date('G', $ts);
        $min = date('i', $ts);
        return $frDays[$dow] . ' ' . $d . ' ' . $frMonths[$m] . ' ' . $y . ' à ' . $h . 'h' . $min;
    }
    return $d . ' ' . $frMonths[$m] . ' ' . $y;
}

$stages = [
    'submitted'           => ['label' => $ast['stages']['submitted'],           'icon' => 'fa-paper-plane'],
    'under_review'        => ['label' => $ast['stages']['under_review'],        'icon' => 'fa-magnifying-glass'],
    'interview_requested' => ['label' => $ast['stages']['interview_requested'], 'icon' => 'fa-calendar'],
    'interview_scheduled' => ['label' => $ast['stages']['interview_scheduled'], 'icon' => 'fa-calendar-check'],
    'approved'            => ['label' => $ast['stages']['approved'],            'icon' => 'fa-circle-check'],
];
$stageOrder   = ['submitted','under_review','interview_requested','interview_scheduled','approved'];
$currentStage = $application['pipeline_stage'] ?? 'submitted';
$isRejected   = ($currentStage === 'rejected');

$statusMessages = [
    'submitted'           => [$ast['status_msgs']['submitted'],           'fa-clock',           false],
    'under_review'        => [$ast['status_msgs']['under_review'],        'fa-magnifying-glass', false],
    'interview_requested' => [$ast['status_msgs']['interview_requested'], 'fa-calendar-star',   false],
    'interview_scheduled' => [$ast['status_msgs']['interview_scheduled'], 'fa-calendar-check',  false],
    'approved'            => [$ast['status_msgs']['approved'],            'fa-party-horn',      false],
    'rejected'            => [$ast['status_msgs']['rejected'],            'fa-circle-info',     true],
];
[$statusMsg, $statusIcon, $isRejectedInfo] = $statusMessages[$currentStage] ?? ['', 'fa-circle-info', false];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= $ast['page_title'] ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #00b207;
            --green-dark: #009206;
            --green-light: #4ade80;
            --orange: #f59e0b;
            --red: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --radius-md: 10px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
        }

        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #fff; }
        main.page { background: #fff; }

        .status-hero-wrap { max-width: 860px; margin: 0 auto; padding: 20px 20px 0; }
        .status-hero {
            background: linear-gradient(135deg, #0a1628 0%, #0d2137 50%, #071220 100%);
            color: white; padding: 48px 32px 40px;
            position: relative; overflow: hidden; border-radius: 16px;
        }
        .status-hero::before {
            content: ''; position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .status-hero-inner { max-width: 820px; margin: 0 auto; position: relative; z-index: 1; }
        .status-hero-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(0,178,7,0.18); color: var(--green-light);
            border: 1px solid rgba(0,178,7,0.35); padding: 6px 16px;
            border-radius: 50px; font-size: 12px; font-weight: 600;
            letter-spacing: 0.5px; margin-bottom: 18px;
        }
        .status-hero h1 { font-size: clamp(22px, 3.5vw, 30px); font-weight: 800; margin-bottom: 8px; line-height: 1.25; }
        .status-hero h1 span { color: var(--green-light); }
        .status-hero p { font-size: 15px; color: rgba(255,255,255,0.68); max-width: 540px; line-height: 1.6; margin-bottom: 16px; }
        .app-id-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.85); border-radius: 20px;
            padding: 5px 14px; font-size: 12px; font-weight: 600;
        }

        .status-page { max-width: 820px; margin: 0 auto; padding: 32px 20px 72px; }

        .flash { padding: 13px 18px; border-radius: var(--radius-md); margin-bottom: 18px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .flash.success { background: #d1fae5; color: #065f46; border-left: 4px solid var(--green); }
        .flash.error   { background: #fee2e2; color: #991b1b; border-left: 4px solid var(--red); }

        .card { background: #fff; border-radius: var(--radius-xl); border: 1px solid var(--gray-200); border-top: 3px solid var(--green); box-shadow: var(--shadow-sm); padding: 28px 32px; margin-bottom: 20px; }
        .card-title { font-size: 15px; font-weight: 700; color: var(--gray-800); margin-bottom: 22px; display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--green); }

        .pipeline { display: flex; align-items: flex-start; gap: 0; position: relative; overflow-x: auto; padding: 0 4px; }
        .pipeline-step { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; min-width: 90px; }
        .pipeline-step:not(:last-child)::after { content: ''; position: absolute; top: 18px; left: 50%; width: 100%; height: 3px; background: var(--gray-200); z-index: 0; }
        .pipeline-step.done:not(:last-child)::after { background: var(--green); }
        .step-dot { width: 36px; height: 36px; border-radius: 50%; background: var(--gray-200); display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--gray-500); position: relative; z-index: 1; font-weight: 700; border: 3px solid var(--gray-200); }
        .pipeline-step.done   .step-dot { background: var(--green); color: #fff; border-color: var(--green); }
        .pipeline-step.active .step-dot { background: var(--green); color: #fff; border-color: var(--green); animation: pulse-dot 1.8s ease-in-out infinite; }
        .pipeline-step.rejected .step-dot { background: var(--red); color: #fff; border-color: var(--red); }
        @keyframes pulse-dot { 0%,100%{box-shadow:0 0 0 0 rgba(0,178,7,0.4);}50%{box-shadow:0 0 0 7px rgba(0,178,7,0);} }
        .step-label { margin-top: 8px; font-size: 11px; color: var(--gray-400); text-align: center; line-height: 1.3; font-weight: 500; }
        .pipeline-step.done   .step-label { color: var(--green); font-weight: 600; }
        .pipeline-step.active .step-label { color: var(--green); font-weight: 700; }
        .pipeline-step.rejected .step-label { color: var(--red); font-weight: 600; }

        .status-info { display: flex; align-items: flex-start; gap: 14px; padding: 16px 18px; border-radius: var(--radius-md); background: #f0fdf4; border: 1.5px solid #bbf7d0; margin-top: 24px; }
        .status-info i { color: var(--green); font-size: 1.2rem; margin-top: 3px; flex-shrink: 0; }
        .status-info p { font-size: 14px; color: var(--gray-700); line-height: 1.6; }
        .status-badge { display: inline-flex; align-items: center; gap: 5px; background: var(--green); color: #fff; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 6px; letter-spacing: 0.3px; text-transform: uppercase; }
        .status-info.rejected .status-badge { background: var(--red); }
        .status-info.rejected { background: #fef2f2; border-color: #fecaca; }
        .status-info.rejected i { color: var(--red); }

        .time-slots { display: flex; flex-direction: column; gap: 10px; }
        .time-slot { display: flex; align-items: center; gap: 12px; padding: 13px 16px; border: 2px solid var(--gray-200); border-radius: var(--radius-md); cursor: pointer; transition: all 0.18s; background: #fff; }
        .time-slot:hover { border-color: var(--green); background: #f0fdf4; }
        .time-slot input[type=radio] { accent-color: var(--green); width: 18px; height: 18px; flex-shrink: 0; }
        .time-slot-label { font-size: 14px; font-weight: 500; color: var(--gray-700); }
        .btn-confirm-time { margin-top: 16px; background: var(--green); color: #fff; border: none; padding: 12px 28px; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; transition: background 0.18s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-confirm-time:hover { background: var(--green-dark); }
        .time-confirmed-banner { background: #d1fae5; color: #065f46; border-radius: var(--radius-md); padding: 16px 18px; font-size: 14px; display: flex; align-items: flex-start; gap: 12px; }
        .time-confirmed-banner i { color: var(--green); font-size: 1.3rem; flex-shrink: 0; margin-top: 1px; }

        .messages-list { display: flex; flex-direction: column; gap: 12px; max-height: 380px; overflow-y: auto; padding-right: 4px; }
        .msg-bubble { max-width: 85%; padding: 11px 15px; border-radius: 14px; font-size: 13.5px; line-height: 1.5; }
        .msg-bubble.admin { background: var(--gray-100); color: var(--gray-800); align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid var(--gray-200); }
        .msg-bubble.applicant { background: var(--green); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }
        .msg-sender { font-size: 11px; font-weight: 600; margin-bottom: 3px; opacity: 0.75; }
        .msg-time { font-size: 10.5px; opacity: 0.55; margin-top: 4px; text-align: right; }
        .msg-bubble.admin .msg-time { text-align: left; }
        .no-messages { text-align: center; color: var(--gray-400); font-size: 13px; padding: 24px 0; }
        .msg-compose { display: flex; gap: 10px; margin-top: 16px; }
        .msg-compose textarea { flex: 1; border: 1.5px solid var(--gray-200); border-radius: var(--radius-md); padding: 10px 14px; font-family: inherit; font-size: 14px; resize: vertical; min-height: 64px; outline: none; transition: border-color 0.18s; color: var(--gray-800); }
        .msg-compose textarea:focus { border-color: var(--green); }
        .btn-send { background: var(--green); color: #fff; border: none; padding: 0 20px; border-radius: var(--radius-md); font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer; align-self: flex-end; height: 44px; transition: background 0.18s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-send:hover { background: var(--green-dark); }

        @media (max-width: 580px) {
            .card { padding: 20px 16px; }
            .pipeline-step { min-width: 72px; }
            .step-label { font-size: 10px; }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/applicant-header.php'; ?>

<main class="page">

    <!-- Hero -->
    <div class="status-hero-wrap">
    <section class="status-hero">
        <div class="status-hero-inner">
            <div class="status-hero-badge">
                <i class="fa-solid fa-truck-fast"></i>
                <?= $ast['hero_badge'] ?>
            </div>
            <h1><?= sprintf($ast['hero_h1'], htmlspecialchars($application['first_name'])) ?></h1>
            <p><?= $ast['hero_sub'] ?></p>
            <?php if (!empty($application['created_at'])): ?>
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;">
                <span class="app-id-badge">
                    <i class="fa-regular fa-calendar" style="font-size:10px;"></i>
                    <?= $ast['submitted_on'] ?> <?= fmtDate($application['created_at'], $fr) ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </section>
    </div>

    <!-- Content -->
    <div class="status-page">

        <?php if ($flashMsg = getFlash('success')): ?>
        <div class="flash success" data-auto-dismiss style="transition:opacity 0.6s ease;"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flashMsg) ?></div>
        <?php elseif ($flashMsg = getFlash('error')): ?>
        <div class="flash error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flashMsg) ?></div>
        <?php endif; ?>

        <!-- Pipeline tracker -->
        <div class="card">
            <div class="card-title"><i class="fa-solid fa-route"></i> <?= $ast['progress_title'] ?></div>

            <div class="pipeline">
                <?php foreach ($stageOrder as $idx => $stageKey):
                    $stageIdx   = array_search($stageKey, $stageOrder);
                    $currentIdx = array_search($currentStage, $stageOrder);
                    if ($currentIdx === false) $currentIdx = 0;
                    if ($isRejected) {
                        $stepClass = ($stageKey === 'submitted') ? 'done' : 'pending';
                    } else {
                        if ($stageIdx < $currentIdx)       $stepClass = 'done';
                        elseif ($stageIdx === $currentIdx) $stepClass = 'active';
                        else                               $stepClass = 'pending';
                    }
                ?>
                <div class="pipeline-step <?= $stepClass ?>">
                    <div class="step-dot">
                        <?php if ($stepClass === 'done'): ?>
                            <i class="fa-solid fa-check" style="font-size:13px;"></i>
                        <?php elseif ($stepClass === 'active'): ?>
                            <i class="fa-solid <?= $stages[$stageKey]['icon'] ?>" style="font-size:12px;"></i>
                        <?php else: ?>
                            <?= $idx + 1 ?>
                        <?php endif; ?>
                    </div>
                    <div class="step-label"><?= $stages[$stageKey]['label'] ?></div>
                </div>
                <?php endforeach; ?>

                <?php if ($isRejected): ?>
                <div class="pipeline-step rejected">
                    <div class="step-dot"><i class="fa-solid fa-xmark" style="font-size:13px;"></i></div>
                    <div class="step-label"><?= $ast['stages']['rejected'] ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="status-info <?= $isRejectedInfo ? 'rejected' : '' ?>">
                <i class="fa-solid <?= $statusIcon ?>"></i>
                <div>
                    <span class="status-badge"><?= $stages[$currentStage]['label'] ?? ucfirst(str_replace('_',' ',$currentStage)) ?></span>
                    <p><?= $statusMsg ?></p>
                </div>
            </div>

            <?php if ($currentStage === 'approved'): ?>
            <div style="margin-top:20px;text-align:center;">
                <a href="<?= url('delivery/dashboard') ?>" style="display:inline-flex;align-items:center;gap:8px;background:#00b207;color:#fff;text-decoration:none;padding:13px 28px;border-radius:10px;font-size:15px;font-weight:700;">
                    <i class="fa-solid fa-gauge-high"></i>
                    <?= $fr ? 'Accéder à mon tableau de bord' : 'Go to my dashboard' ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($currentStage === 'interview_requested' && !empty($proposedTimes)): ?>
        <!-- Interview time selection -->
        <div class="card">
            <div class="card-title"><i class="fa-solid fa-calendar-days"></i> <?= $ast['interview_title'] ?></div>
            <p style="font-size:14px;color:var(--gray-500);margin-bottom:18px;"><?= $ast['interview_desc'] ?></p>

            <form id="interviewForm">
                <div class="time-slots">
                    <?php foreach ($proposedTimes as $slot): ?>
                    <label class="time-slot">
                        <input type="radio" name="selected_time" value="<?= htmlspecialchars($slot) ?>" required>
                        <span class="time-slot-label">
                            <i class="fa-regular fa-clock" style="margin-right:6px;color:var(--green);"></i>
                            <?= fmtDate($slot, $fr, 'long') ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="application_id" value="<?= $application['id'] ?>">
                <button type="submit" class="btn-confirm-time">
                    <i class="fa-solid fa-calendar-check"></i> <?= $ast['btn_confirm_time'] ?>
                </button>
            </form>
        </div>

        <?php elseif ($currentStage === 'interview_scheduled' && $application['interview_selected_time']): ?>
        <!-- Confirmed interview -->
        <div class="card">
            <div class="card-title"><i class="fa-solid fa-calendar-check"></i> <?= $ast['confirmed_title'] ?></div>
            <div class="time-confirmed-banner">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <strong style="display:block;font-weight:600;margin-bottom:4px;"><?= $ast['confirmed_label'] ?></strong>
                    <?= fmtDate($application['interview_selected_time'], $fr, 'long') ?>
                    <?php if (!empty($application['interview_notes'])): ?>
                        <div style="margin-top:6px;font-size:13px;"><?= nl2br(htmlspecialchars($application['interview_notes'])) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Messages -->
        <div class="card">
            <div class="card-title"><i class="fa-solid fa-comments"></i> <?= $ast['messages_title'] ?></div>

            <div class="messages-list" id="messagesList">
                <?php if (empty($messages)): ?>
                    <p class="no-messages"><i class="fa-regular fa-comment-dots" style="display:block;font-size:24px;margin-bottom:8px;opacity:.4;"></i><?= $ast['no_messages'] ?></p>
                <?php else: foreach ($messages as $msg):
                    $isAdmin     = ($msg['sender_type'] === 'admin');
                    $bubbleClass = $isAdmin ? 'admin' : 'applicant';
                    $senderName  = $isAdmin ? $ast['sender_team'] : $ast['sender_you'];
                    ?>
                    <div style="display:flex;flex-direction:column;">
                        <div class="msg-bubble <?= $bubbleClass ?>">
                            <div class="msg-sender"><?= $senderName ?></div>
                            <?= nl2br(htmlspecialchars(($fr && !empty($msg['message_fr'])) ? $msg['message_fr'] : $msg['message'])) ?>
                            <div class="msg-time"><?php
                        $ts = strtotime($msg['created_at']);
                        if ($fr) {
                            $frMo = ['','jan.','fév.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.'];
                            echo date('j', $ts) . ' ' . $frMo[(int)date('n', $ts)] . ', ' . date('G\hi', $ts);
                        } else {
                            echo date('M j, g:i a', $ts);
                        }
                    ?></div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <div class="msg-compose">
                <textarea id="msgText" placeholder="<?= htmlspecialchars($ast['msg_placeholder']) ?>" rows="3"></textarea>
                <button class="btn-send" id="sendMsgBtn" onclick="sendMessage()">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
            <p style="font-size:11px;color:var(--gray-400);margin-top:6px;">
                <?= $fr ? 'Appuyez sur' : 'Press' ?>
                <kbd style="background:var(--gray-100);border:1px solid var(--gray-200);border-radius:4px;padding:1px 5px;font-size:10px;font-family:inherit;"><?= $ast['ctrl_enter'] ?></kbd>
                <?= $ast['ctrl_enter_hint'] ?>
            </p>
            <input type="hidden" id="appId" value="<?= $application['id'] ?>">
        </div>

    </div>
</main>

<?php
if (!isset($t) || !is_array($t)) {
    $t = getTranslations($currentLang);
}
include __DIR__ . '/../components/footer.php';
?>

<script>
const csrfToken     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const csrfTokenName = '<?= env("CSRF_TOKEN_NAME", "_csrf_token") ?>';
const _L = {
    lang:        <?= json_encode($currentLang) ?>,
    locale:      <?= json_encode($ast['locale']) ?>,
    senderTeam:  <?= json_encode($ast['sender_team']) ?>,
    senderYou:   <?= json_encode($ast['sender_you']) ?>,
    failSend:    <?= json_encode($ast['js_fail_send']) ?>,
    network:     <?= json_encode($ast['js_network']) ?>,
    noSlot:      <?= json_encode($ast['js_no_slot']) ?>,
    confirming:  <?= json_encode($ast['js_confirming']) ?>,
    confirmBtn:  <?= json_encode($ast['js_confirm_btn']) ?>,
    failConfirm: <?= json_encode($ast['js_fail_confirm']) ?>,
};

async function sendMessage() {
    const text = document.getElementById('msgText').value.trim();
    if (!text) return;

    const btn = document.getElementById('sendMsgBtn');
    btn.disabled = true;

    const fd = new FormData();
    fd.append('application_id', document.getElementById('appId').value);
    fd.append('message', text);
    fd.append(csrfTokenName, csrfToken);

    try {
        const res  = await fetch('<?= url("delivery/send-application-message") ?>', { method:'POST', body:fd });
        const data = await res.json();
        if (data.success) {
            document.getElementById('msgText').value = '';
            const list  = document.getElementById('messagesList');
            const noMsg = list.querySelector('.no-messages');
            if (noMsg) noMsg.remove();

            const now     = new Date();
            const timeStr = now.toLocaleString(_L.locale, {month:'short', day:'numeric', hour:'numeric', minute:'2-digit'});
            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.flexDirection = 'column';
            div.innerHTML = `<div class="msg-bubble applicant">
                <div class="msg-sender">${_L.senderYou}</div>
                ${text.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>')}
                <div class="msg-time">${timeStr}</div>
            </div>`;
            list.appendChild(div);
            list.scrollTop = list.scrollHeight;
            // Advance poll cursor so next poll won't re-fetch this message
            lastPollTime = now.toISOString().slice(0, 19).replace('T', ' ');
        } else {
            alert(data.error ?? _L.failSend);
        }
    } catch(e) {
        alert(_L.network);
    }
    btn.disabled = false;
}

document.getElementById('msgText').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) sendMessage();
});

const interviewForm = document.getElementById('interviewForm');
if (interviewForm) {
    interviewForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const selected = interviewForm.querySelector('input[name="selected_time"]:checked');
        if (!selected) { alert(_L.noSlot); return; }

        const btn = interviewForm.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + _L.confirming;

        const fd = new FormData(interviewForm);
        fd.append(csrfTokenName, csrfToken);

        try {
            const res  = await fetch('<?= url("delivery/select-interview-time") ?>', { method:'POST', body:fd });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.error ?? _L.failConfirm);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-calendar-check"></i> ' + _L.confirmBtn;
            }
        } catch(e) {
            alert(_L.network);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-calendar-check"></i> ' + _L.confirmBtn;
        }
    });
}

const ml = document.getElementById('messagesList');
if (ml) ml.scrollTop = ml.scrollHeight;

// Shared poll cursor — also written by sendMessage() to prevent duplicates
let lastPollTime = '<?= date('Y-m-d H:i:s') ?>';

// Real-time polling
(function() {
    const APP_ID      = <?= (int)$application['id'] ?>;
    const POLL_URL    = '<?= url('delivery/application-status/poll') ?>';
    const INTERVAL    = 10000;
    let currentStatus = '<?= $application['pipeline_stage'] ?? 'submitted' ?>';
    let pollTimer;

    function buildBubble(msg) {
        const isAdmin = msg.sender_type === 'admin';
        const cls     = isAdmin ? 'admin' : 'applicant';
        const sender  = isAdmin ? _L.senderTeam : _L.senderYou;
        const d       = new Date(msg.created_at.replace(' ', 'T'));
        const timeStr = d.toLocaleString(_L.locale, {month:'short', day:'numeric', hour:'numeric', minute:'2-digit'});
        const wrap    = document.createElement('div');
        wrap.style.display = 'flex';
        wrap.style.flexDirection = 'column';
        const text = (_L.lang === 'fr' && msg.message_fr) ? msg.message_fr : msg.message;
        const safe = text.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
        wrap.innerHTML = `<div class="msg-bubble ${cls}">
            <div class="msg-sender">${sender}</div>
            ${safe}
            <div class="msg-time">${timeStr}</div>
        </div>`;
        return wrap;
    }

    async function poll() {
        try {
            const url = `${POLL_URL}?app_id=${APP_ID}&since=${encodeURIComponent(lastPollTime)}`;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();

            if (data.messages && data.messages.length > 0) {
                const list  = document.getElementById('messagesList');
                const noMsg = list?.querySelector('.no-messages');
                if (noMsg) noMsg.remove();
                data.messages.forEach(m => list.appendChild(buildBubble(m)));
                list.scrollTop = list.scrollHeight;
            }

            if (data.server_time) lastPollTime = data.server_time;
            if (data.status && data.status !== currentStatus) window.location.reload();
        } catch(e) { /* silent */ }
    }

    document.addEventListener('visibilitychange', () => {
        document.hidden ? clearInterval(pollTimer) : (pollTimer = setInterval(poll, INTERVAL));
    });

    pollTimer = setInterval(poll, INTERVAL);
})();
</script>
</body>
</html>
