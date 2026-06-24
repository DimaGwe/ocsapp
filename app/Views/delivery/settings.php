<?php $currentPage = 'settings'; include __DIR__ . '/layout-header.php'; ?>

<style>
.supplier-main-content {
    padding: 0;
}
.page-header {
    margin-bottom: 28px;
}
.page-header h1 {
    font-size: 26px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 4px;
}
.page-header p {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}
.settings-container {
    width: 100%;
}
.settings-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    margin-bottom: 24px;
}
.settings-card .card-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 8px 8px 0 0;
}
.settings-card .card-header h2 {
    margin: 0;
    font-size: 18px;
    color: #1f2937;
}
.settings-card .card-header i {
    margin-right: 8px;
    color: #00b207;
}
.settings-card .card-body {
    padding: 24px;
}

/* Photo row */
.photo-row {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}
.avatar-wrap { flex-shrink: 0; }
.avatar-img {
    width: 88px; height: 88px;
    border-radius: 50%; object-fit: cover;
    border: 3px solid #e5e7eb;
}
.avatar-initials {
    width: 88px; height: 88px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00b207, #009206);
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; font-weight: 700;
    border: 3px solid #e5e7eb;
}
.photo-info { flex: 1; }
.photo-name { font-size: 17px; font-weight: 700; color: #1f2937; margin: 0 0 2px; }
.photo-role { font-size: 13px; color: #6b7280; margin: 0 0 12px; }
.photo-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.photo-hint { font-size: 12px; color: #9ca3af; }
.btn-cancel-photo {
    background: none; border: 1px solid #d1d5db;
    border-radius: 6px; padding: 5px 12px;
    font-size: 13px; cursor: pointer; color: #6b7280; font-family: inherit;
}

/* Form */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}
.form-group input,
.form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    transition: border-color 0.2s;
    box-sizing: border-box;
}
.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #00b207;
    box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
}
.form-group input:disabled {
    background: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
}
.form-group small {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #6b7280;
}
.required { color: #ef4444; }
.form-actions {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.last-login-info { font-size: 13px; color: #6b7280; }

/* Buttons */
.btn {
    padding: 10px 20px; border: none; border-radius: 6px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    transition: all 0.2s; font-family: inherit;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-primary {
    background: linear-gradient(135deg, #00b207 0%, #008505 100%);
    color: white;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,178,7,0.3); }
.btn-outline-sm {
    padding: 7px 14px; font-size: 13px;
    background: none; border: 1px solid #00b207; color: #00b207;
    border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s;
}
.btn-outline-sm:hover { background: #00b207; color: white; }
.btn-primary-sm {
    padding: 6px 14px; font-size: 13px;
    background: #00b207; color: white; border: none;
    border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: 600;
    display: inline-flex; align-items: center; gap: 5px; transition: background 0.15s;
}
.btn-primary-sm:hover { background: #009206; }

/* Alert */
.alert {
    padding: 12px 16px; border-radius: 6px;
    margin-top: 12px; font-size: 14px;
}
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
.alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

/* Driver status tiles */
.status-tile {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 16px;
}
.status-tile-label {
    font-size: 12px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; color: #6b7280; margin-bottom: 10px;
    display: flex; align-items: center; gap: 6px;
}
.status-tile-label i { color: #00b207; }
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 20px;
    font-size: 13px; font-weight: 600;
}
.status-num {
    font-size: 28px; font-weight: 700; color: #1f2937; line-height: 1;
}
.status-tile-sub { font-size: 12px; color: #6b7280; margin-top: 4px; }

/* Notifications card */
.notif-pref-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; padding: 6px 0;
}
.notif-pref-info { flex: 1; }
.notif-pref-label {
    font-size: 14px; font-weight: 600; color: #111827;
    display: flex; align-items: center; gap: 8px; margin-bottom: 4px;
}
.notif-pref-label i { color: #00b207; }
.notif-pref-desc { font-size: 13px; color: #6b7280; line-height: 1.5; }
.toggle-switch { position: relative; display: inline-block; width: 46px; height: 26px; flex-shrink: 0; cursor: pointer; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-track { position: absolute; inset: 0; background: #d1d5db; border-radius: 26px; transition: background 0.2s; }
.toggle-track::before {
    content: ''; position: absolute; width: 20px; height: 20px;
    left: 3px; bottom: 3px; background: white; border-radius: 50%;
    transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.toggle-switch input:checked + .toggle-track { background: #00b207; }
.toggle-switch input:checked + .toggle-track::before { transform: translateX(20px); }
.btn-test-sound {
    background: none; border: 1px solid #00b207; color: #00b207;
    border-radius: 8px; padding: 7px 16px; font-size: 13px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    transition: background 0.15s, color 0.15s; font-family: inherit;
}
.btn-test-sound:hover { background: #00b207; color: white; }
.notif-status-badge {
    display: inline-block; padding: 4px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 600; flex-shrink: 0;
}
.notif-status-badge.granted { background: #d1fae5; color: #059669; }
.notif-status-badge.default { background: #fef3c7; color: #92400e; }
.notif-status-badge.denied  { background: #fee2e2; color: #dc2626; }

/* Password */
.password-wrapper { position: relative; }
.password-wrapper input { padding-right: 44px; }
.password-toggle {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #6b7280;
    padding: 4px 8px; font-size: 16px; transition: color 0.2s;
}
.password-toggle:hover { color: #00b207; }
.pw-rules-box { margin-top: 8px; display: flex; flex-direction: column; gap: 2px; }
.pw-rule      { font-size: 13px; display: flex; align-items: center; gap: 6px; padding: 2px 0; transition: color 0.2s; }
.pw-rule-fail { color: #9ca3af; }
.pw-rule-ok   { color: #00b207; }
.pw-rule i    { font-size: 12px; }
.pw-match-error { font-size: 13px; color: #ef4444; margin-top: 6px; display: none; }

/* Payment preference */
.payment-pref-options {
    display: flex; gap: 12px; flex-wrap: wrap; margin-top: 6px;
}
.pref-option {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 18px; border: 2px solid #d1d5db; border-radius: 8px;
    cursor: pointer; font-size: 14px; font-weight: 600; color: #374151;
    transition: all 0.2s; background: #fff;
}
.pref-option:hover  { border-color: #00b207; background: #f0fdf4; }
.pref-option.active { border-color: #00b207; background: #f0fdf4; color: #166534; }
.pref-option input[type="radio"] { display: none; }
.pref-option i { color: #00b207; }

@media (max-width: 768px) {
    .form-row { grid-template-columns: 1fr; }
    .photo-row { flex-direction: column; align-items: flex-start; gap: 16px; }
    .settings-card .card-body { padding: 16px; }
    .settings-card .card-header { padding: 16px; }
    .payment-pref-options { flex-direction: column; }
    .pref-option { width: 100%; }
}
</style>

<div class="supplier-main-content">
    <div class="page-header">
        <h1><i class="fas fa-cog"></i> <?= $fr ? 'Paramètres' : 'Settings' ?></h1>
        <p><?= $fr ? 'Gérez les paramètres et la sécurité de votre compte' : 'Manage your account settings and security' ?></p>
    </div>

    <div class="settings-container">

        <!-- Profile Information -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-user"></i> <?= $fr ? 'Informations du profil' : 'Profile Information' ?></h2>
            </div>
            <div class="card-body">

                <!-- Photo row -->
                <div class="photo-row">
                    <div class="avatar-wrap" id="avatarWrap">
                        <?php if (!empty($driver['avatar'])): ?>
                            <img class="avatar-img" id="avatarImg"
                                 src="<?= htmlspecialchars('https://ocsapp.ca/' . ltrim($driver['avatar'], '/')) ?>"
                                 alt="">
                        <?php else: ?>
                            <div class="avatar-initials" id="avatarImg">
                                <?= strtoupper(substr($driver['first_name'] ?? 'D', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="photo-info">
                        <p class="photo-name"><?= htmlspecialchars(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? '')) ?></p>
                        <p class="photo-role"><?= $fr ? 'Livreur' : 'Delivery Driver' ?></p>
                        <div class="photo-actions">
                            <form method="POST" action="<?= url('delivery/profile/photo') ?>" enctype="multipart/form-data" id="photoForm">
                                <?= csrfField() ?>
                                <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" style="display:none;">
                                <button type="button" class="btn btn-outline-sm" onclick="document.getElementById('photoInput').click()">
                                    <i class="fas fa-camera"></i> <?= $fr ? 'Changer la photo' : 'Change Photo' ?>
                                </button>
                                <span class="photo-hint"><?= $fr ? 'JPEG, PNG ou WebP · Max 5 Mo' : 'JPEG, PNG or WebP · Max 5 MB' ?></span>
                            </form>
                        </div>
                        <div id="photoPreview" style="display:none;margin-top:10px;padding:10px 14px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;display:none;align-items:center;gap:10px;">
                            <img id="photoPreviewImg" src="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                            <span id="photoPreviewName" style="font-size:13px;color:#16a34a;flex:1;"></span>
                            <button type="button" class="btn-cancel-photo" onclick="cancelPhotoPreview()"><?= $fr ? 'Annuler' : 'Cancel' ?></button>
                            <button type="button" class="btn btn-primary-sm" onclick="document.getElementById('photoForm').submit()">
                                <i class="fas fa-check"></i> <?= $fr ? 'Enregistrer' : 'Save' ?>
                            </button>
                        </div>
                    </div>
                </div>

                <hr style="margin:24px 0;border:none;border-top:1px solid #e5e7eb;">

                <form id="profileForm">
                    <?= csrfField() ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name"><?= $fr ? 'Prénom' : 'First Name' ?> <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name"
                                   value="<?= htmlspecialchars($driver['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name"><?= $fr ? 'Nom de famille' : 'Last Name' ?> <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name"
                                   value="<?= htmlspecialchars($driver['last_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= $fr ? 'Courriel' : 'Email' ?></label>
                            <input type="email" value="<?= htmlspecialchars($driver['email'] ?? '') ?>" disabled>
                            <small><?= $fr ? "Contactez l'administrateur pour modifier le courriel" : 'Contact admin to change email' ?></small>
                        </div>
                        <div class="form-group">
                            <label for="phone"><?= $fr ? 'Téléphone' : 'Phone' ?></label>
                            <input type="tel" id="phone" name="phone"
                                   value="<?= htmlspecialchars($driver['phone'] ?? '') ?>"
                                   pattern="[\d\s\-\(\)\+]{10,}"
                                   title="<?= $fr ? 'Entrez un numéro valide (au moins 10 chiffres)' : 'Enter a valid phone number (at least 10 digits)' ?>">
                        </div>
                    </div>

                    <div id="profileMessage" class="alert" style="display:none;"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $fr ? 'Enregistrer le profil' : 'Save Profile' ?>
                        </button>
                        <span class="last-login-info">
                            <?= $fr ? 'Membre depuis' : 'Member since' ?>:
                            <?php
                            if (!empty($driver['created_at'])) {
                                $ts = strtotime($driver['created_at']);
                                if ($fr) {
                                    $m = ['','janv.','févr.','mars','avr.','mai','juin','juill.','août','sept.','oct.','nov.','déc.'];
                                    echo (int)date('j',$ts) . ' ' . $m[(int)date('n',$ts)] . ' ' . date('Y',$ts);
                                } else {
                                    echo date('M j, Y', $ts);
                                }
                            } else { echo '-'; }
                            ?>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Driver Status -->
        <?php
        try {
            $db = \Database::getConnection();
            $certStmt = $db->prepare("SELECT cert_number, issued_at FROM driver_certificates WHERE driver_id = ? LIMIT 1");
            $certStmt->execute([userId()]);
            $settingsCert = $certStmt->fetch(\PDO::FETCH_ASSOC);

            $bgStmt = $db->prepare("SELECT bgcheck_status FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
            $bgStmt->execute([userId()]);
            $settingsBg = $bgStmt->fetchColumn();

            $delStmt = $db->prepare("SELECT COUNT(*) FROM delivery_assignments WHERE driver_id = ? AND status = 'delivered'");
            $delStmt->execute([userId()]);
            $settingsDel = (int)$delStmt->fetchColumn();

            $ratingStmt = $db->prepare("SELECT AVG(rating) FROM driver_ratings WHERE driver_id = ?");
            $ratingStmt->execute([userId()]);
            $settingsRating = $ratingStmt->fetchColumn();
        } catch (\Exception $e) {
            $settingsCert = null; $settingsBg = null; $settingsDel = 0; $settingsRating = null;
        }

        $bgMap = [
            'verified' => ['#dcfce7','#16a34a','fa-shield-halved', $fr ? 'Vérifié' : 'Verified'],
            'waived'   => ['#dcfce7','#16a34a','fa-shield-halved', $fr ? 'Dispensé' : 'Waived'],
            'uploaded' => ['#fef9c3','#ca8a04','fa-clock',         $fr ? "En cours d'examen" : 'Under Review'],
            'flagged'  => ['#fee2e2','#dc2626','fa-flag',          $fr ? 'Signalé' : 'Flagged'],
        ];
        [$bgBg,$bgColor,$bgIcon,$bgLabel] = $bgMap[$settingsBg] ?? ['#f3f4f6','#6b7280','fa-circle',$fr ? 'Non soumis' : 'Not Submitted'];
        ?>
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-id-badge"></i> <?= $fr ? 'Statut du livreur' : 'Driver Status' ?></h2>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px;">
                    <!-- Training -->
                    <div class="status-tile">
                        <div class="status-tile-label"><i class="fas fa-graduation-cap"></i> <?= $fr ? 'Formation' : 'Training' ?></div>
                        <?php if ($settingsCert): ?>
                            <span class="status-badge" style="background:#dcfce7;color:#16a34a;">
                                <i class="fas fa-circle-check"></i> <?= $fr ? 'Certifié' : 'Certified' ?>
                            </span>
                            <div class="status-tile-sub"><?= htmlspecialchars($settingsCert['cert_number']) ?></div>
                        <?php else: ?>
                            <span class="status-badge" style="background:#fef9c3;color:#ca8a04;">
                                <i class="fas fa-clock"></i> <?= $fr ? 'En cours' : 'In Progress' ?>
                            </span>
                            <div class="status-tile-sub">
                                <a href="<?= url('delivery/training') ?>" style="color:#00b207;font-size:12px;"><?= $fr ? 'Voir la formation' : 'View training' ?> →</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Bgcheck -->
                    <div class="status-tile">
                        <div class="status-tile-label"><i class="fas fa-shield-halved"></i> <?= $fr ? 'Vérif. antécédents' : 'Background Check' ?></div>
                        <span class="status-badge" style="background:<?= $bgBg ?>;color:<?= $bgColor ?>;">
                            <i class="fas <?= $bgIcon ?>"></i> <?= $bgLabel ?>
                        </span>
                    </div>
                    <!-- Deliveries -->
                    <div class="status-tile">
                        <div class="status-tile-label"><i class="fas fa-box"></i> <?= $fr ? 'Livraisons' : 'Deliveries' ?></div>
                        <span class="status-num"><?= number_format($settingsDel) ?></span>
                        <div class="status-tile-sub"><?= $fr ? 'complétées' : 'completed' ?></div>
                    </div>
                    <!-- Rating -->
                    <?php if ($settingsRating): ?>
                    <div class="status-tile">
                        <div class="status-tile-label"><i class="fas fa-star"></i> <?= $fr ? 'Évaluation' : 'Rating' ?></div>
                        <span class="status-badge" style="background:#dbeafe;color:#2563eb;">
                            <i class="fas fa-star"></i> <?= number_format((float)$settingsRating, 1) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="border-top:1px solid #e5e7eb;padding-top:16px;">
                    <p style="font-size:13px;color:#6b7280;margin:0 0 12px;">
                        <?= $fr ? 'Des questions sur votre statut ? Contactez notre équipe de support livreur.' : 'Questions about your status? Contact our driver support team.' ?>
                    </p>
                    <a href="mailto:drivers@ocsapp.ca" style="display:inline-flex;align-items:center;gap:6px;background:#00b207;color:white;padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fas fa-envelope"></i> <?= $fr ? 'Contacter le support' : 'Contact Support' ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-university"></i> <?= $fr ? 'Informations de paiement' : 'Payment Information' ?></h2>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">
                    <?= $fr
                        ? "Fournissez vos coordonnées bancaires pour recevoir vos paiements. Toutes les informations sont chiffrées et accessibles uniquement par le personnel autorisé d'OCSAPP."
                        : "Provide your banking details so we can process your driver payouts. All information is encrypted and only accessible by authorized OCSAPP staff." ?>
                </p>
                <form id="paymentForm">
                    <?= csrfField() ?>

                    <!-- Payment preference -->
                    <div class="form-group">
                        <label><?= $fr ? 'Mode de paiement préféré' : 'Payment Preference' ?></label>
                        <div class="payment-pref-options">
                            <?php $pref = $payment['payment_preference'] ?? ''; ?>
                            <label class="pref-option <?= $pref === 'eft'     ? 'active' : '' ?>">
                                <input type="radio" name="payment_preference" value="eft" <?= $pref === 'eft' ? 'checked' : '' ?>>
                                <i class="fas fa-exchange-alt"></i> <?= $fr ? 'TVE / Virement direct' : 'EFT / Direct Deposit' ?>
                            </label>
                            <label class="pref-option <?= $pref === 'interac' ? 'active' : '' ?>">
                                <input type="radio" name="payment_preference" value="interac" <?= $pref === 'interac' ? 'checked' : '' ?>>
                                <i class="fas fa-envelope"></i> <?= $fr ? 'Virement Interac' : 'Interac e-Transfer' ?>
                            </label>
                            <label class="pref-option <?= $pref === 'cheque'  ? 'active' : '' ?>">
                                <input type="radio" name="payment_preference" value="cheque" <?= $pref === 'cheque' ? 'checked' : '' ?>>
                                <i class="fas fa-file-invoice-dollar"></i> <?= $fr ? 'Chèque' : 'Cheque' ?>
                            </label>
                        </div>
                    </div>

                    <!-- EFT fields -->
                    <div id="eftFields" class="banking-section" style="display:<?= $pref === 'eft' ? 'block' : 'none' ?>;">
                        <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                        <h3 style="font-size:15px;color:#374151;margin-bottom:16px;"><i class="fas fa-landmark" style="color:#00b207;margin-right:8px;"></i><?= $fr ? 'Détails TVE / Virement direct' : 'EFT / Direct Deposit Details' ?></h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_name"><?= $fr ? 'Nom de la banque' : 'Bank Name' ?> <span class="required">*</span></label>
                                <input type="text" id="bank_name" name="bank_name"
                                       value="<?= htmlspecialchars($payment['bank_name'] ?? '') ?>"
                                       placeholder="<?= $fr ? 'ex. TD, RBC, BMO' : 'e.g. TD, RBC, BMO' ?>">
                            </div>
                            <div class="form-group">
                                <label for="bank_account_holder"><?= $fr ? 'Nom du titulaire du compte' : 'Account Holder Name' ?> <span class="required">*</span></label>
                                <input type="text" id="bank_account_holder" name="bank_account_holder"
                                       value="<?= htmlspecialchars($payment['bank_account_holder'] ?? '') ?>"
                                       placeholder="<?= $fr ? 'Nom sur le compte' : 'Name on account' ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_transit"><?= $fr ? 'Numéro de transit (5 chiffres)' : 'Transit Number (5 digits)' ?></label>
                                <input type="text" id="bank_transit" name="bank_transit"
                                       value="<?= htmlspecialchars($payment['bank_transit'] ?? '') ?>"
                                       placeholder="12345" maxlength="5" pattern="\d{5}">
                            </div>
                            <div class="form-group">
                                <label for="bank_institution"><?= $fr ? "Numéro d'institution (3 chiffres)" : 'Institution Number (3 digits)' ?></label>
                                <input type="text" id="bank_institution" name="bank_institution"
                                       value="<?= htmlspecialchars($payment['bank_institution'] ?? '') ?>"
                                       placeholder="004" maxlength="3" pattern="\d{3}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bank_account"><?= $fr ? 'Numéro de compte' : 'Account Number' ?></label>
                                <input type="text" id="bank_account" name="bank_account"
                                       value="<?= htmlspecialchars($payment['bank_account'] ?? '') ?>"
                                       placeholder="<?= $fr ? '7-12 chiffres' : '7-12 digits' ?>" maxlength="12">
                            </div>
                            <div class="form-group">
                                <label for="bank_account_type"><?= $fr ? 'Type de compte' : 'Account Type' ?></label>
                                <select id="bank_account_type" name="bank_account_type">
                                    <option value=""><?= $fr ? 'Sélectionner le type' : 'Select type' ?></option>
                                    <option value="chequing" <?= ($payment['bank_account_type'] ?? '') === 'chequing' ? 'selected' : '' ?>><?= $fr ? 'Chèques' : 'Chequing' ?></option>
                                    <option value="savings"  <?= ($payment['bank_account_type'] ?? '') === 'savings'  ? 'selected' : '' ?>><?= $fr ? 'Épargne' : 'Savings' ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Interac fields -->
                    <div id="interacFields" class="banking-section" style="display:<?= $pref === 'interac' ? 'block' : 'none' ?>;">
                        <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                        <h3 style="font-size:15px;color:#374151;margin-bottom:16px;"><i class="fas fa-envelope" style="color:#00b207;margin-right:8px;"></i><?= $fr ? 'Détails Virement Interac' : 'Interac e-Transfer Details' ?></h3>
                        <div class="form-group">
                            <label for="interac_email"><?= $fr ? 'Courriel Virement' : 'e-Transfer Email' ?> <span class="required">*</span></label>
                            <input type="email" id="interac_email" name="interac_email"
                                   value="<?= htmlspecialchars($payment['interac_email'] ?? '') ?>"
                                   placeholder="paiements@exemple.ca">
                            <small><?= $fr ? "L'adresse courriel utilisée pour recevoir les virements Interac" : 'The email address you use to receive Interac e-Transfers' ?></small>
                        </div>
                    </div>

                    <!-- Cheque notice -->
                    <div id="chequeFields" class="banking-section" style="display:<?= $pref === 'cheque' ? 'block' : 'none' ?>;">
                        <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                        <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:14px 18px;border-radius:6px;">
                            <p style="margin:0;font-size:14px;color:#92400e;">
                                <?= $fr
                                    ? '<strong>Les paiements par chèque</strong> seront envoyés par courrier à l\'adresse figurant dans votre dossier de candidature. Assurez-vous que votre adresse est à jour.'
                                    : '<strong>Cheque payments</strong> will be mailed to the address on file from your driver application. Please ensure your address is current.' ?>
                            </p>
                        </div>
                    </div>

                    <div id="paymentMessage" class="alert" style="display:none;"></div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $fr ? 'Enregistrer les informations de paiement' : 'Save Payment Info' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Notifications & Sound -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-bell"></i> <?= $fr ? 'Notifications et son' : 'Notifications &amp; Sound' ?></h2>
            </div>
            <div class="card-body">
                <div class="notif-pref-row">
                    <div class="notif-pref-info">
                        <div class="notif-pref-label"><i class="fas fa-volume-up"></i> <?= $fr ? 'Sons de notification' : 'Notification Sounds' ?></div>
                        <div class="notif-pref-desc"><?= $fr ? "Jouer un son lorsqu'une nouvelle notification ou alerte arrive." : 'Play a chime when a new notification or alert arrives.' ?></div>
                    </div>
                    <label class="toggle-switch" title="Toggle notification sounds">
                        <input type="checkbox" id="soundToggle" onchange="onDriverSoundToggle(this.checked)">
                        <span class="toggle-track"></span>
                    </label>
                </div>
                <div style="margin-top:8px;margin-bottom:20px;">
                    <button type="button" class="btn-test-sound" onclick="testDriverSound()">
                        <i class="fas fa-play-circle"></i> <?= $fr ? 'Tester le son' : 'Test Sound' ?>
                    </button>
                    <span id="testSoundMsg" style="margin-left:10px;font-size:13px;color:#6b7280;display:none;"></span>
                </div>
                <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 20px;">
                <div class="notif-pref-row">
                    <div class="notif-pref-info">
                        <div class="notif-pref-label"><i class="fas fa-desktop"></i> <?= $fr ? 'Notifications du navigateur' : 'Browser Notifications' ?></div>
                        <div class="notif-pref-desc"><?= $fr ? "Afficher une fenêtre contextuelle même lorsque vous regardez un autre onglet." : "Show a system popup even when you're looking at another tab." ?></div>
                    </div>
                    <span id="browserNotifStatus" class="notif-status-badge"></span>
                </div>
                <div id="browserNotifAction" style="margin-top:10px;"></div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-lock"></i> <?= $fr ? 'Modifier le mot de passe' : 'Change Password' ?></h2>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label for="current_password"><?= $fr ? 'Mot de passe actuel' : 'Current Password' ?> <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="current_password" name="current_password" required>
                            <button type="button" class="password-toggle" data-target="current_password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="new_password"><?= $fr ? 'Nouveau mot de passe' : 'New Password' ?> <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="new_password" name="new_password" required>
                            <button type="button" class="password-toggle" data-target="new_password"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="pw-rules-box" id="pwRulesBox"></div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><?= $fr ? 'Confirmer le nouveau mot de passe' : 'Confirm New Password' ?> <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <button type="button" class="password-toggle" data-target="confirm_password"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="pw-match-error" id="pwMatchError"><?= $fr ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.' ?></div>
                    </div>
                    <div id="passwordMessage" class="alert" style="display:none;"></div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= $fr ? 'Mettre à jour le mot de passe' : 'Update Password' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div><!-- /.settings-container -->
</div><!-- /.supplier-main-content -->

<script>
// ── Photo preview ───────────────────────────────────────────────────────────
document.getElementById('photoInput').addEventListener('change', function() {
    var file = this.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
        alert(<?= json_encode($fr ? 'Le fichier est trop volumineux. La taille maximale est de 5 Mo.' : 'File is too large. Maximum size is 5 MB.') ?>);
        this.value = '';
        return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('photoPreviewImg').src = e.target.result;
        document.getElementById('photoPreviewName').textContent = file.name;
        var prev = document.getElementById('photoPreview');
        prev.style.display = 'flex';
    };
    reader.readAsDataURL(file);
});

function cancelPhotoPreview() {
    document.getElementById('photoInput').value = '';
    document.getElementById('photoPreview').style.display = 'none';
}

// ── Profile form (AJAX) ─────────────────────────────────────────────────────
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
    try {
        var resp = await fetch('<?= url('delivery/update-profile') ?>', {
            method: 'POST', body: new FormData(this)
        });
        var data = await resp.json();
        showMsg('profileMessage', data.message || (data.success ? '<?= $fr ? 'Enregistré.' : 'Saved.' ?>' : '<?= $fr ? 'Erreur.' : 'Error.' ?>'), data.success ? 'success' : 'error');
    } catch(err) {
        showMsg('profileMessage', '<?= $fr ? 'Une erreur est survenue.' : 'An error occurred.' ?>', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <?= $fr ? 'Enregistrer le profil' : 'Save Profile' ?>';
    }
});

// ── Password form (AJAX) ────────────────────────────────────────────────────
var pwRules = [
    { id: 'rule-length',  test: function(pw) { return pw.length >= 10; },           label: <?= json_encode($fr ? '10 caractères minimum' : 'At least 10 characters') ?> },
    { id: 'rule-upper',   test: function(pw) { return /[A-Z]/.test(pw); },          label: <?= json_encode($fr ? 'Une lettre majuscule (A-Z)' : 'One uppercase letter (A-Z)') ?> },
    { id: 'rule-lower',   test: function(pw) { return /[a-z]/.test(pw); },          label: <?= json_encode($fr ? 'Une lettre minuscule (a-z)' : 'One lowercase letter (a-z)') ?> },
    { id: 'rule-number',  test: function(pw) { return /[0-9]/.test(pw); },          label: <?= json_encode($fr ? 'Un chiffre (0-9)' : 'One number (0-9)') ?> },
    { id: 'rule-special', test: function(pw) { return /[!@#$%^&*]/.test(pw); },    label: <?= json_encode($fr ? 'Un caractère spécial (!@#$%^&*)' : 'One special character (!@#$%^&*)') ?> },
];

(function() {
    var box = document.getElementById('pwRulesBox');
    var newInput  = document.getElementById('new_password');
    var confInput = document.getElementById('confirm_password');
    var matchErr  = document.getElementById('pwMatchError');

    pwRules.forEach(function(r) {
        var el = document.createElement('div');
        el.id = r.id; el.className = 'pw-rule pw-rule-fail';
        el.innerHTML = '<i class="fas fa-circle-xmark"></i> ' + r.label;
        box.appendChild(el);
    });

    function updateRules(pw) {
        pwRules.forEach(function(r) {
            var el = document.getElementById(r.id);
            var ok = r.test(pw);
            el.className = 'pw-rule ' + (ok ? 'pw-rule-ok' : 'pw-rule-fail');
            el.innerHTML = '<i class="fas fa-' + (ok ? 'circle-check' : 'circle-xmark') + '"></i> ' + r.label;
        });
    }

    newInput.addEventListener('input', function() {
        updateRules(this.value);
        if (confInput.value) matchErr.style.display = (this.value !== confInput.value) ? 'block' : 'none';
    });
    confInput.addEventListener('input', function() {
        matchErr.style.display = (newInput.value !== this.value && this.value) ? 'block' : 'none';
    });
})();

document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    var newPw   = document.getElementById('new_password').value;
    var confPw  = document.getElementById('confirm_password').value;

    if (!pwRules.every(function(r) { return r.test(newPw); })) {
        showMsg('passwordMessage', <?= json_encode($fr ? 'Le mot de passe ne respecte pas tous les critères.' : 'Password does not meet all requirements.') ?>, 'error');
        return;
    }
    if (newPw !== confPw) {
        document.getElementById('pwMatchError').style.display = 'block';
        return;
    }

    var btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
    try {
        var resp = await fetch('<?= url('delivery/update-password') ?>', {
            method: 'POST', body: new FormData(this)
        });
        var data = await resp.json();
        showMsg('passwordMessage', data.message || (data.success ? '<?= $fr ? 'Mot de passe mis à jour.' : 'Password updated.' ?>' : 'Error'), data.success ? 'success' : 'error');
        if (data.success) this.reset();
    } catch(err) {
        showMsg('passwordMessage', '<?= $fr ? 'Une erreur est survenue.' : 'An error occurred.' ?>', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <?= $fr ? 'Mettre à jour le mot de passe' : 'Update Password' ?>';
    }
});

function showMsg(id, msg, type) {
    var el = document.getElementById(id);
    el.textContent = msg;
    el.className = 'alert alert-' + type;
    el.style.display = 'block';
    if (type === 'success') setTimeout(function() { el.style.display = 'none'; }, 5000);
}

// ── Password visibility toggles ──────────────────────────────────────────────
document.querySelectorAll('.password-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = document.getElementById(this.dataset.target);
        var icon  = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text'; icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password'; icon.className = 'fas fa-eye';
        }
    });
});

// ── Payment preference toggle ────────────────────────────────────────────────
document.querySelectorAll('input[name="payment_preference"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.banking-section').forEach(function(s) { s.style.display = 'none'; });
        document.querySelectorAll('.pref-option').forEach(function(o) { o.classList.remove('active'); });
        this.closest('.pref-option').classList.add('active');
        var map = { eft: 'eftFields', interac: 'interacFields', cheque: 'chequeFields' };
        if (map[this.value]) document.getElementById(map[this.value]).style.display = 'block';
    });
});

// ── Payment form (AJAX) ──────────────────────────────────────────────────────
document.getElementById('paymentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
    try {
        var resp = await fetch('<?= url('delivery/update-payment') ?>', {
            method: 'POST', body: new FormData(this)
        });
        var data = await resp.json();
        showMsg('paymentMessage', data.message || (data.success ? '<?= $fr ? 'Enregistré.' : 'Saved.' ?>' : '<?= $fr ? 'Erreur.' : 'Error.' ?>'), data.success ? 'success' : 'error');
    } catch(err) {
        showMsg('paymentMessage', '<?= $fr ? 'Une erreur est survenue.' : 'An error occurred.' ?>', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <?= $fr ? 'Enregistrer les informations de paiement' : 'Save Payment Info' ?>';
    }
});

// ── Notifications & Sound ────────────────────────────────────────────────────
(function() {
    var _ns = {
        soundPlayed:     <?= json_encode($fr ? 'Son joué !' : 'Sound played!') ?>,
        soundNA:         <?= json_encode($fr ? 'Fonction sonore non disponible.' : 'Sound function not available.') ?>,
        notSupported:    <?= json_encode($fr ? 'Non pris en charge' : 'Not supported') ?>,
        notSupportedMsg: <?= json_encode($fr ? 'Votre navigateur ne prend pas en charge les notifications.' : 'Your browser does not support desktop notifications.') ?>,
        enabled:         <?= json_encode($fr ? 'Activées' : 'Enabled') ?>,
        enabledMsg:      <?= json_encode($fr ? 'Vous recevrez des notifications pour les nouvelles alertes.' : "You'll receive desktop notifications for new alerts.") ?>,
        blocked:         <?= json_encode($fr ? 'Bloquées' : 'Blocked') ?>,
        blockedMsg:      <?= json_encode($fr ? "Notifications bloquées. Allez dans les paramètres du site de votre navigateur pour les autoriser." : "Notifications are blocked. Go to your browser's site settings to allow them.") ?>,
        notEnabled:      <?= json_encode($fr ? 'Non activées' : 'Not enabled') ?>,
        enableBtn:       <?= json_encode($fr ? 'Activer les notifications' : 'Enable Browser Notifications') ?>,
    };

    var soundToggle = document.getElementById('soundToggle');
    soundToggle.checked = localStorage.getItem('drv_sound_enabled') !== 'off';

    window.onDriverSoundToggle = function(checked) {
        localStorage.setItem('drv_sound_enabled', checked ? 'on' : 'off');
    };

    window.testDriverSound = function() {
        var msg = document.getElementById('testSoundMsg');
        localStorage.setItem('drv_sound_enabled', 'on');
        soundToggle.checked = true;
        if (typeof playChimeFooter === 'function') {
            playChimeFooter(); msg.textContent = _ns.soundPlayed;
        } else if (typeof window.playChime === 'function') {
            window.playChime(); msg.textContent = _ns.soundPlayed;
        } else {
            msg.textContent = _ns.soundNA;
        }
        msg.style.display = 'inline';
        setTimeout(function() { msg.style.display = 'none'; }, 2500);
    };

    var statusBadge = document.getElementById('browserNotifStatus');
    var actionArea  = document.getElementById('browserNotifAction');

    function renderNotifStatus() {
        if (typeof Notification === 'undefined') {
            statusBadge.textContent = _ns.notSupported;
            statusBadge.className = 'notif-status-badge denied';
            actionArea.innerHTML = '<small style="color:#6b7280;">' + _ns.notSupportedMsg + '</small>';
            return;
        }
        var perm = Notification.permission;
        if (perm === 'granted') {
            statusBadge.textContent = _ns.enabled;
            statusBadge.className = 'notif-status-badge granted';
            actionArea.innerHTML = '<small style="color:#059669;"><i class="fas fa-check-circle"></i> ' + _ns.enabledMsg + '</small>';
        } else if (perm === 'denied') {
            statusBadge.textContent = _ns.blocked;
            statusBadge.className = 'notif-status-badge denied';
            actionArea.innerHTML = '<small style="color:#dc2626;"><i class="fas fa-ban"></i> ' + _ns.blockedMsg + '</small>';
        } else {
            statusBadge.textContent = _ns.notEnabled;
            statusBadge.className = 'notif-status-badge default';
            actionArea.innerHTML = '<button type="button" class="btn-test-sound" onclick="window.requestBrowserNotif()"><i class="fas fa-bell"></i> ' + _ns.enableBtn + '</button>';
        }
    }

    window.requestBrowserNotif = function() {
        Notification.requestPermission().then(function() { renderNotifStatus(); });
    };

    renderNotifStatus();
})();
</script>

<?php include __DIR__ . '/layout-footer.php'; ?>
