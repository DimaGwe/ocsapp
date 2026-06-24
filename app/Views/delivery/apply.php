<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

$at = [
    'en' => [
        'page_title'      => 'Become a Delivery Driver - OCSAPP',
        'back'            => 'Back to Driver Central',
        'hero_badge'      => 'Driver Portal',
        'hero_h1'         => 'Become a <span>Driver</span>',
        'hero_p'          => 'Join our zero-emission delivery team and earn on your own schedule. Fill out the form below to apply.',
        // Sections
        's1_title'        => 'Personal Information',
        's1_desc'         => 'Tell us a bit about yourself.',
        's2_title'        => 'Address Information',
        's2_desc'         => 'Your current residential address.',
        's3_title'        => 'Vehicle &amp; License',
        's3_desc'         => 'Tell us about your vehicle and driving credentials.',
        's4_title'        => 'Availability',
        's4_desc'         => "Let us know when you're available to deliver.",
        's5_title'        => 'Additional Information',
        's5_desc'         => 'Optional details that help our team review your application.',
        's6_title'        => 'Background Check Declaration',
        's6_desc'         => 'All drivers must obtain a criminal background check before going active.',
        // Fields
        'lbl_first'       => 'First Name',
        'lbl_last'        => 'Last Name',
        'lbl_email'       => 'Email',
        'lbl_phone'       => 'Phone Number',
        'lbl_dob'         => 'Date of Birth',
        'ph_month'        => 'Month',
        'ph_day'          => 'Day',
        'ph_year'         => 'Year',
        'dob_hint'        => 'You must be at least 18 years old to apply.',
        'dob_invalid'     => 'You must be at least 18 years old to apply.',
        'dob_required'    => 'Please select your full date of birth.',
        'lbl_street'      => 'Street Address',
        'lbl_city'        => 'City',
        'lbl_province'    => 'Province',
        'ph_province'     => 'Select Province',
        'lbl_postal'      => 'Postal Code',
        'postal_hint'     => 'Format: A1A 1A1',
        'lbl_vehicle'     => 'Vehicle Type',
        'ph_vehicle'      => 'Select Vehicle Type',
        'lbl_license_num' => "Driver's License Number",
        'license_hint'    => 'Optional for bicycle, e-bike, and e-scooter deliveries.',
        'lbl_license_exp' => 'License Expiry Date',
        'lbl_days'        => 'Available Days',
        'days_hint'       => 'Select at least one day.',
        'days'            => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
        'day_ids'         => ['day_mon','day_tue','day_wed','day_thu','day_fri','day_sat','day_sun'],
        'day_values'      => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
        'lbl_shift'       => 'Preferred Shift',
        'ph_shift'        => 'Select Preferred Shift',
        'shifts'          => [
            'Morning 6am-12pm'   => 'Morning (6am - 12pm)',
            'Afternoon 12pm-6pm' => 'Afternoon (12pm - 6pm)',
            'Evening 6pm-11pm'   => 'Evening (6pm - 11pm)',
            'Flexible'           => 'Flexible - Any Time',
        ],
        'lbl_motivation'  => 'Why do you want to deliver with OCSAPP?',
        'ph_motivation'   => "Tell us why you'd like to join our delivery team...",
        'lbl_exp'         => 'Have you delivered for other platforms?',
        'ph_exp'          => 'Select Option',
        'opt_yes'         => 'Yes',
        'opt_no'          => 'No',
        'bgcheck_info'    => '<strong><i class="fas fa-info-circle"></i> What this means:</strong> OCSAPP requires all drivers to obtain and submit a criminal background check before going active. You will upload it yourself through your driver portal after approval - it typically costs $25–$75 CAD and takes 1–5 business days through your local police station or the RCMP online service.',
        'lbl_criminal'    => 'Have you ever been convicted of a criminal offence in Canada or abroad?',
        'cr_no'           => 'No - I have no criminal convictions',
        'cr_yes'          => "Yes - I have a conviction(s) to declare",
        'lbl_cr_details'  => 'Please briefly describe the conviction(s)',
        'ph_cr_details'   => 'Type of offence, approximate date, and any relevant context...',
        'cr_hint'         => 'This information is confidential and reviewed by OCSAPP staff only. Disclosure does not automatically disqualify you.',
        'bgcheck_ack'     => 'I understand that a criminal background check is required before I can go active as an OCSAPP driver. I agree to self-obtain and upload it through my driver portal after my application is approved.',
        'btn_submit'      => 'Submit Application',
        'already_driver'  => 'Already a driver?',
        'sign_in'         => 'Sign In',
        'js_no_days'      => 'Please select at least one available day.',
        'vehicles'        => [
            'Bicycle' => 'Bicycle',
            'E-Bike'  => 'E-Bike',
            'E-Scooter' => 'E-Scooter',
            'Car'     => 'Car',
            'Van'     => 'Van',
        ],
        'months'          => ['01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June',
                              '07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December'],
    ],
    'fr' => [
        'page_title'      => 'Devenez livreur - OCSAPP',
        'back'            => 'Retour au portail livreur',
        'hero_badge'      => 'Portail livreur',
        'hero_h1'         => 'Devenez <span>livreur</span>',
        'hero_p'          => "Rejoignez notre équipe de livraison zéro émission et gagnez selon votre propre horaire. Remplissez le formulaire ci-dessous pour postuler.",
        // Sections
        's1_title'        => 'Informations personnelles',
        's1_desc'         => 'Parlez-nous un peu de vous.',
        's2_title'        => 'Adresse',
        's2_desc'         => 'Votre adresse résidentielle actuelle.',
        's3_title'        => 'Véhicule et permis',
        's3_desc'         => 'Parlez-nous de votre véhicule et de vos attestations de conduite.',
        's4_title'        => 'Disponibilité',
        's4_desc'         => 'Dites-nous quand vous êtes disponible pour livrer.',
        's5_title'        => 'Informations supplémentaires',
        's5_desc'         => 'Détails optionnels qui aident notre équipe à évaluer votre candidature.',
        's6_title'        => 'Déclaration de vérification des antécédents',
        's6_desc'         => "Tous les livreurs doivent obtenir une vérification des antécédents judiciaires avant d'être actifs.",
        // Fields
        'lbl_first'       => 'Prénom',
        'lbl_last'        => 'Nom de famille',
        'lbl_email'       => 'Courriel',
        'lbl_phone'       => 'Numéro de téléphone',
        'lbl_dob'         => 'Date de naissance',
        'ph_month'        => 'Mois',
        'ph_day'          => 'Jour',
        'ph_year'         => 'Année',
        'dob_hint'        => 'Vous devez avoir au moins 18 ans pour postuler.',
        'dob_invalid'     => 'Vous devez avoir au moins 18 ans pour postuler.',
        'dob_required'    => 'Veuillez sélectionner votre date de naissance complète.',
        'lbl_street'      => 'Adresse',
        'lbl_city'        => 'Ville',
        'lbl_province'    => 'Province',
        'ph_province'     => 'Choisir une province',
        'lbl_postal'      => 'Code postal',
        'postal_hint'     => 'Format : A1A 1A1',
        'lbl_vehicle'     => 'Type de véhicule',
        'ph_vehicle'      => 'Choisir un type de véhicule',
        'lbl_license_num' => 'Numéro de permis de conduire',
        'license_hint'    => 'Optionnel pour les livraisons à vélo, vélo électrique et trottinette électrique.',
        'lbl_license_exp' => "Date d'expiration du permis",
        'lbl_days'        => 'Jours disponibles',
        'days_hint'       => 'Sélectionnez au moins un jour.',
        'days'            => ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'],
        'day_ids'         => ['day_mon','day_tue','day_wed','day_thu','day_fri','day_sat','day_sun'],
        'day_values'      => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
        'lbl_shift'       => 'Quart de travail préféré',
        'ph_shift'        => 'Choisir un quart de travail',
        'shifts'          => [
            'Morning 6am-12pm'   => 'Matin (6h - 12h)',
            'Afternoon 12pm-6pm' => 'Après-midi (12h - 18h)',
            'Evening 6pm-11pm'   => 'Soir (18h - 23h)',
            'Flexible'           => 'Flexible - N\'importe quand',
        ],
        'lbl_motivation'  => 'Pourquoi souhaitez-vous livrer avec OCSAPP ?',
        'ph_motivation'   => 'Dites-nous pourquoi vous souhaitez rejoindre notre équipe de livraison...',
        'lbl_exp'         => 'Avez-vous livré pour d\'autres plateformes ?',
        'ph_exp'          => 'Sélectionner',
        'opt_yes'         => 'Oui',
        'opt_no'          => 'Non',
        'bgcheck_info'    => "<strong><i class=\"fas fa-info-circle\"></i> Ce que cela signifie :</strong> OCSAPP exige que tous les livreurs obtiennent et soumettent une vérification des antécédents judiciaires avant d'être actifs. Vous la téléchargerez vous-même via votre portail livreur après approbation - cela coûte généralement entre 25 $ et 75 $ CAD et prend 1 à 5 jours ouvrables auprès de votre poste de police local ou du service en ligne de la GRC.",
        'lbl_criminal'    => 'Avez-vous déjà été reconnu coupable d\'une infraction criminelle au Canada ou à l\'étranger ?',
        'cr_no'           => "Non - Je n'ai aucune condamnation criminelle",
        'cr_yes'          => "Oui - J'ai une ou des condamnations à déclarer",
        'lbl_cr_details'  => 'Veuillez décrire brièvement la ou les condamnation(s)',
        'ph_cr_details'   => "Type d'infraction, date approximative et tout contexte pertinent...",
        'cr_hint'         => "Ces informations sont confidentielles et examinées uniquement par le personnel d'OCSAPP. La divulgation ne vous disqualifie pas automatiquement.",
        'bgcheck_ack'     => "Je comprends qu'une vérification des antécédents judiciaires est requise avant que je puisse être actif en tant que livreur OCSAPP. Je m'engage à l'obtenir moi-même et à la télécharger via mon portail livreur après approbation de ma candidature.",
        'btn_submit'      => 'Soumettre la candidature',
        'already_driver'  => 'Déjà livreur ?',
        'sign_in'         => 'Connectez-vous',
        'js_no_days'      => 'Veuillez sélectionner au moins un jour disponible.',
        'vehicles'        => [
            'Bicycle' => 'Vélo',
            'E-Bike'  => 'Vélo électrique',
            'E-Scooter' => 'Trottinette Électrique',
            'Car'     => 'Voiture',
            'Van'     => 'Fourgonnette',
        ],
        'months'          => ['01'=>'Janvier','02'=>'Février','03'=>'Mars','04'=>'Avril','05'=>'Mai','06'=>'Juin',
                              '07'=>'Juillet','08'=>'Août','09'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre'],
    ],
];
$at = $at[$currentLang] ?? $at['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= $at['page_title'] ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/pages/delivery-apply.css') ?>">
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<!-- Dark hero banner -->
<div class="apply-hero">
    <a href="<?= url('driver-central') ?>" class="apply-hero-back">
        <i class="fas fa-arrow-left"></i>
        <?= $at['back'] ?>
    </a>
    <div class="apply-hero-badge">
        <i class="fas fa-truck"></i> <?= $at['hero_badge'] ?>
    </div>
    <h1><?= $at['hero_h1'] ?></h1>
    <p><?= $at['hero_p'] ?></p>
</div>

<main class="page">
<div class="apply-page">

    <?php if ($flash = getFlash('success')): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <?php if ($flash = getFlash('error')): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <?php
        $old = $_SESSION['_apply_old_input'] ?? [];
        unset($_SESSION['_apply_old_input']);
        $oldDays = $old['available_days'] ?? [];
    ?>

    <form method="POST" action="<?= url('delivery/apply') ?>" id="driverApplicationForm">
        <?= csrfField() ?>

        <!-- Personal Information -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa-solid fa-user"></i>
                <?= $at['s1_title'] ?>
            </div>
            <p class="form-section-desc"><?= $at['s1_desc'] ?></p>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name"><?= $at['lbl_first'] ?> <span class="required">*</span></label>
                    <input type="text" id="first_name" name="first_name" placeholder="<?= $fr ? 'Jean' : 'John' ?>"
                        value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="last_name"><?= $at['lbl_last'] ?> <span class="required">*</span></label>
                    <input type="text" id="last_name" name="last_name" placeholder="<?= $fr ? 'Tremblay' : 'Doe' ?>"
                        value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email"><?= $at['lbl_email'] ?> <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="<?= $fr ? 'jean.tremblay@courriel.com' : 'john.doe@email.com' ?>"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone"><?= $at['lbl_phone'] ?> <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" placeholder="(514) 123-4567"
                        value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label><?= $at['lbl_dob'] ?> <span class="required">*</span></label>
                <?php
                    $dobVal   = $old['date_of_birth'] ?? '';
                    $dobParts = $dobVal ? explode('-', $dobVal) : ['', '', ''];
                    $dobYear  = $dobParts[0] ?? '';
                    $dobMonth = $dobParts[1] ?? '';
                    $dobDay   = $dobParts[2] ?? '';
                    $maxYear  = (int)date('Y') - 18;
                    $minYear  = $maxYear - 82;
                ?>
                <input type="hidden" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($dobVal) ?>">
                <div class="dob-selects" style="display:flex;gap:8px;">
                    <select id="dob_month" aria-label="<?= $at['ph_month'] ?>" style="flex:1.4;">
                        <option value=""><?= $at['ph_month'] ?></option>
                        <?php foreach ($at['months'] as $num => $name): ?>
                        <option value="<?= $num ?>"<?= $dobMonth === $num ? ' selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="dob_day" aria-label="<?= $at['ph_day'] ?>" style="flex:1;">
                        <option value=""><?= $at['ph_day'] ?></option>
                        <?php for ($d = 1; $d <= 31; $d++): $dd = str_pad($d, 2, '0', STR_PAD_LEFT); ?>
                        <option value="<?= $dd ?>"<?= $dobDay === $dd ? ' selected' : '' ?>><?= $d ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="dob_year" aria-label="<?= $at['ph_year'] ?>" style="flex:1.2;">
                        <option value=""><?= $at['ph_year'] ?></option>
                        <?php for ($y = $maxYear; $y >= $minYear; $y--): ?>
                        <option value="<?= $y ?>"<?= (int)$dobYear === $y ? ' selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <span class="hint" id="dob_hint"><?= $at['dob_hint'] ?></span>
            </div>
        </div>

        <!-- Address -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa-solid fa-location-dot"></i>
                <?= $at['s2_title'] ?>
            </div>
            <p class="form-section-desc"><?= $at['s2_desc'] ?></p>

            <div class="form-group">
                <label for="street_address"><?= $at['lbl_street'] ?> <span class="required">*</span></label>
                <input type="text" id="street_address" name="street_address"
                    placeholder="<?= $fr ? '123, rue Principale, app. 4B' : '123 Main Street, Apt 4B' ?>"
                    value="<?= htmlspecialchars($old['street_address'] ?? '') ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city"><?= $at['lbl_city'] ?> <span class="required">*</span></label>
                    <input type="text" id="city" name="city" placeholder="Montréal"
                        value="<?= htmlspecialchars($old['city'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="province"><?= $at['lbl_province'] ?> <span class="required">*</span></label>
                    <?php $oldProvince = $old['province'] ?? ''; ?>
                    <select id="province" name="province" required>
                        <option value=""><?= $at['ph_province'] ?></option>
                        <?php foreach (['Ontario','Quebec','British Columbia','Alberta','Manitoba','Saskatchewan','Nova Scotia','New Brunswick','Newfoundland and Labrador','Prince Edward Island','Northwest Territories','Yukon','Nunavut'] as $prov): ?>
                            <option value="<?= $prov ?>" <?= $oldProvince === $prov ? 'selected' : '' ?>><?= $prov ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="postal_code"><?= $at['lbl_postal'] ?> <span class="required">*</span></label>
                <input type="text" id="postal_code" name="postal_code" placeholder="H3Z 2Y7"
                    pattern="[A-Za-z][0-9][A-Za-z] ?[0-9][A-Za-z][0-9]"
                    value="<?= htmlspecialchars($old['postal_code'] ?? '') ?>" required>
                <span class="hint"><?= $at['postal_hint'] ?></span>
            </div>
        </div>

        <!-- Vehicle & License -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa-solid fa-car"></i>
                <?= $at['s3_title'] ?>
            </div>
            <p class="form-section-desc"><?= $at['s3_desc'] ?></p>

            <div class="form-group">
                <label for="vehicle_type"><?= $at['lbl_vehicle'] ?> <span class="required">*</span></label>
                <?php $oldVehicle = $old['vehicle_type'] ?? ''; ?>
                <select id="vehicle_type" name="vehicle_type" required>
                    <option value=""><?= $at['ph_vehicle'] ?></option>
                    <?php foreach ($at['vehicles'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $oldVehicle === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="license_number"><?= $at['lbl_license_num'] ?></label>
                    <input type="text" id="license_number" name="license_number" placeholder="D1234-56789-12345"
                        value="<?= htmlspecialchars($old['license_number'] ?? '') ?>">
                    <span class="hint"><?= $at['license_hint'] ?></span>
                </div>
                <div class="form-group">
                    <label for="license_expiry"><?= $at['lbl_license_exp'] ?></label>
                    <input type="date" id="license_expiry" name="license_expiry"
                        value="<?= htmlspecialchars($old['license_expiry'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- Availability -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa-solid fa-calendar-days"></i>
                <?= $at['s4_title'] ?>
            </div>
            <p class="form-section-desc"><?= $at['s4_desc'] ?></p>

            <div class="form-group">
                <label><?= $at['lbl_days'] ?> <span class="required">*</span></label>
                <div class="checkbox-group">
                    <?php foreach ($at['days'] as $i => $dayLabel):
                        $dayId    = $at['day_ids'][$i];
                        $dayValue = $at['day_values'][$i];
                    ?>
                    <div class="checkbox-item">
                        <input type="checkbox" id="<?= $dayId ?>" name="available_days[]" value="<?= $dayValue ?>"
                            <?= in_array($dayValue, $oldDays) ? 'checked' : '' ?>>
                        <label for="<?= $dayId ?>"><?= $dayLabel ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <span class="hint" style="margin-top:8px;"><?= $at['days_hint'] ?></span>
            </div>

            <div class="form-group">
                <label for="preferred_shift"><?= $at['lbl_shift'] ?> <span class="required">*</span></label>
                <?php $oldShift = $old['preferred_shift'] ?? ''; ?>
                <select id="preferred_shift" name="preferred_shift" required>
                    <option value=""><?= $at['ph_shift'] ?></option>
                    <?php foreach ($at['shifts'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $oldShift === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa-solid fa-comment"></i>
                <?= $at['s5_title'] ?>
            </div>
            <p class="form-section-desc"><?= $at['s5_desc'] ?></p>

            <div class="form-group">
                <label for="motivation"><?= $at['lbl_motivation'] ?></label>
                <textarea id="motivation" name="motivation"
                    placeholder="<?= htmlspecialchars($at['ph_motivation']) ?>"><?= htmlspecialchars($old['motivation'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="previous_experience"><?= $at['lbl_exp'] ?></label>
                <?php $oldExp = $old['previous_experience'] ?? ''; ?>
                <select id="previous_experience" name="previous_experience">
                    <option value=""><?= $at['ph_exp'] ?></option>
                    <option value="Yes" <?= $oldExp === 'Yes' ? 'selected' : '' ?>><?= $at['opt_yes'] ?></option>
                    <option value="No"  <?= $oldExp === 'No'  ? 'selected' : '' ?>><?= $at['opt_no'] ?></option>
                </select>
            </div>
        </div>

        <!-- Background Check Declaration -->
        <div class="form-section">
            <div class="form-section-title">
                <i class="fa-solid fa-shield-halved"></i>
                <?= $at['s6_title'] ?>
            </div>
            <p class="form-section-desc"><?= $at['s6_desc'] ?></p>

            <div class="info-box">
                <?= $at['bgcheck_info'] ?>
            </div>

            <div class="form-group">
                <label><?= $at['lbl_criminal'] ?> <span class="required">*</span></label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="criminal_record" value="no" id="cr_no"
                            <?= ($old['criminal_record'] ?? 'no') === 'no' ? 'checked' : '' ?>
                            required onchange="toggleCriminalDetails(this.value)">
                        <?= $at['cr_no'] ?>
                    </label>
                    <label>
                        <input type="radio" name="criminal_record" value="yes" id="cr_yes"
                            <?= ($old['criminal_record'] ?? '') === 'yes' ? 'checked' : '' ?>
                            onchange="toggleCriminalDetails(this.value)">
                        <?= $at['cr_yes'] ?>
                    </label>
                </div>
            </div>

            <div class="form-group" id="criminalDetailsGroup"
                style="display:<?= ($old['criminal_record'] ?? '') === 'yes' ? 'block' : 'none' ?>;">
                <label for="criminal_record_details">
                    <?= $at['lbl_cr_details'] ?> <span class="required">*</span>
                </label>
                <textarea id="criminal_record_details" name="criminal_record_details"
                    placeholder="<?= htmlspecialchars($at['ph_cr_details']) ?>"><?= htmlspecialchars($old['criminal_record_details'] ?? '') ?></textarea>
                <span class="hint"><?= $at['cr_hint'] ?></span>
            </div>

            <div class="form-group">
                <label class="terms-label">
                    <input type="checkbox" name="bgcheck_acknowledged" id="bgcheck_acknowledged" required
                        <?= !empty($old['bgcheck_acknowledged']) ? 'checked' : '' ?>>
                    <span><?= $at['bgcheck_ack'] ?></span>
                </label>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-paper-plane"></i>
                <?= $at['btn_submit'] ?>
            </button>
        </div>
    </form>

    <div class="apply-links">
        <?= $at['already_driver'] ?> <a href="<?= url('login') ?>"><?= $at['sign_in'] ?></a>
    </div>

</div>
</main>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
    const _AT = {
        noDays:      <?= json_encode($at['js_no_days']) ?>,
        dobInvalid:  <?= json_encode($at['dob_invalid']) ?>,
        dobRequired: <?= json_encode($at['dob_required']) ?>,
    };

    function toggleCriminalDetails(value) {
        const group = document.getElementById('criminalDetailsGroup');
        const textarea = document.getElementById('criminal_record_details');
        group.style.display = value === 'yes' ? 'block' : 'none';
        textarea.required = value === 'yes';
    }
    const crSelected = document.querySelector('input[name="criminal_record"]:checked');
    if (crSelected) toggleCriminalDetails(crSelected.value);

    // Day selection validation
    const form = document.getElementById('driverApplicationForm');
    const dayCheckboxes = document.querySelectorAll('input[name="available_days[]"]');
    form.addEventListener('submit', function(e) {
        const checkedDays = Array.from(dayCheckboxes).filter(cb => cb.checked);
        if (checkedDays.length === 0) {
            e.preventDefault();
            alert(_AT.noDays);
        }
    });

    // Vehicle type - conditionally require license fields
    const vehicleType = document.getElementById('vehicle_type');
    const licenseNumber = document.getElementById('license_number');
    const licenseExpiry = document.getElementById('license_expiry');
    vehicleType.addEventListener('change', function() {
        const requiresLicense = ['Car', 'Van'].includes(this.value);
        licenseNumber.required = requiresLicense;
        licenseExpiry.required = requiresLicense;
    });

    // Date of birth - three-select combo -> hidden field
    const dobHidden = document.getElementById('date_of_birth');
    const dobMonth  = document.getElementById('dob_month');
    const dobDay    = document.getElementById('dob_day');
    const dobYear   = document.getElementById('dob_year');
    const dobHint   = document.getElementById('dob_hint');

    function syncDob() {
        const m = dobMonth.value, d = dobDay.value, y = dobYear.value;
        if (m && d && y) {
            dobHidden.value = y + '-' + m + '-' + d;
            const dob = new Date(y, parseInt(m) - 1, parseInt(d));
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const md = today.getMonth() - dob.getMonth();
            if (md < 0 || (md === 0 && today.getDate() < dob.getDate())) age--;
            if (age < 18) {
                dobHidden.value = '';
                dobHint.textContent = _AT.dobInvalid;
                dobHint.style.color = '#ef4444';
                [dobMonth, dobDay, dobYear].forEach(s => s.style.borderColor = '#ef4444');
            } else {
                dobHint.textContent = _AT.dobInvalid;
                dobHint.style.color = '';
                [dobMonth, dobDay, dobYear].forEach(s => s.style.borderColor = '');
            }
        } else {
            dobHidden.value = '';
        }
    }

    [dobMonth, dobDay, dobYear].forEach(s => s.addEventListener('change', syncDob));

    form.addEventListener('submit', function(e) {
        if (!dobHidden.value) {
            e.preventDefault();
            dobHint.textContent = _AT.dobRequired;
            dobHint.style.color = '#ef4444';
            [dobMonth, dobDay, dobYear].forEach(s => s.style.borderColor = '#ef4444');
            dobMonth.closest('.form-group').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, true);

    // Postal code formatting
    document.getElementById('postal_code').addEventListener('input', function() {
        let v = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (v.length > 3) v = v.slice(0, 3) + ' ' + v.slice(3, 6);
        this.value = v;
    });

    // Phone formatting
    document.getElementById('phone').addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '');
        if (v.length <= 3) v = v.length ? '(' + v : v;
        else if (v.length <= 6) v = '(' + v.slice(0, 3) + ') ' + v.slice(3);
        else v = '(' + v.slice(0, 3) + ') ' + v.slice(3, 6) + '-' + v.slice(6, 10);
        this.value = v;
    });
</script>
</body>
</html>
