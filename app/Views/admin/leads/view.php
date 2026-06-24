<?php
$currentPage = 'leads';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'edit' => 'Edit',
        'back' => 'Back',
        'contact_info' => 'Contact Information',
        'email' => 'Email',
        'phone' => 'Phone',
        'company' => 'Company',
        'job_title' => 'Job Title',
        'location' => 'Location',
        'assigned_to' => 'Assigned To',
        'not_provided' => 'Not provided',
        'unassigned' => 'Unassigned',
        'lead_details' => 'Lead Details',
        'interest' => 'Interest',
        'source' => 'Source',
        'estimated_value' => 'Estimated Value',
        'created' => 'Created',
        'last_contact' => 'Last Contact',
        'never' => 'Never',
        'notes' => 'Notes',
        'no_notes' => 'No notes yet',
        'activity' => 'Activity',
        'no_activity' => 'No activity recorded yet',
        'comm_center' => 'Communication Center',
        'twilio_not_configured' => 'Twilio not configured.',
        'add_credentials' => 'Add credentials to .env to enable calling/SMS.',
        'calls' => 'Calls',
        'sms' => 'SMS',
        'send_sms' => 'Send SMS',
        'send_email' => 'Send Email',
        'recent_comms' => 'Recent Communications',
        'no_phone' => 'No phone number on file.',
        'status_actions' => 'Status & Actions',
        'update' => 'Update',
        'follow_up' => 'Follow-up',
        'next_follow_up' => 'Next Follow-up',
        'not_scheduled' => 'Not scheduled',
        'schedule_follow_up' => 'Schedule Follow-up',
        'delete_lead' => 'Delete Lead',
        'delete_warning' => 'This action cannot be undone.',
        'opt_out_warning' => 'This lead has opted out of SMS messages.',
        'interest_type' => 'Interest Type',
        'interest_details' => 'Interest Details',
    ],
    'fr' => [
        'edit' => 'Modifier',
        'back' => 'Retour',
        'contact_info' => 'Informations de Contact',
        'email' => 'Courriel',
        'phone' => 'Téléphone',
        'company' => 'Entreprise',
        'job_title' => 'Poste',
        'location' => 'Emplacement',
        'assigned_to' => 'Assigné à',
        'not_provided' => 'Non fourni',
        'unassigned' => 'Non assigné',
        'lead_details' => 'Détails du Prospect',
        'interest' => 'Intérêt',
        'source' => 'Source',
        'estimated_value' => 'Valeur estimée',
        'created' => 'Créé',
        'last_contact' => 'Dernier contact',
        'never' => 'Jamais',
        'notes' => 'Notes',
        'no_notes' => 'Aucune note pour l\'instant',
        'activity' => 'Activité',
        'no_activity' => 'Aucune activité enregistrée',
        'comm_center' => 'Centre de Communication',
        'twilio_not_configured' => 'Twilio non configuré.',
        'add_credentials' => 'Ajoutez les identifiants à .env pour activer les appels/SMS.',
        'calls' => 'Appels',
        'sms' => 'SMS',
        'send_sms' => 'Envoyer SMS',
        'send_email' => 'Envoyer Courriel',
        'recent_comms' => 'Communications Récentes',
        'no_phone' => 'Aucun numéro de téléphone enregistré.',
        'status_actions' => 'Statut & Actions',
        'update' => 'Mettre à jour',
        'follow_up' => 'Suivi',
        'next_follow_up' => 'Prochain suivi',
        'not_scheduled' => 'Non planifié',
        'schedule_follow_up' => 'Planifier un suivi',
        'delete_lead' => 'Supprimer le Prospect',
        'delete_warning' => 'Cette action est irréversible.',
        'opt_out_warning' => 'Ce prospect a refusé les messages SMS.',
        'interest_type' => 'Type d\'intérêt',
        'interest_details' => 'Détails de l\'intérêt',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];
$pageTitle = htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']);

$avatarInitials = strtoupper(substr($lead['first_name'], 0, 1) . substr($lead['last_name'], 0, 1));
$avatarColors = [
    'new'         => '#1d4ed8',
    'contacted'   => '#4f46e5',
    'qualified'   => '#059669',
    'proposal'    => '#b45309',
    'negotiation' => '#c2410c',
    'won'         => '#16a34a',
    'lost'        => '#6b7280',
];
$avatarColor = $avatarColors[$lead['status']] ?? '#00b207';

ob_start();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.header-left h1 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 8px;
}

.header-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.lead-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
    letter-spacing: -0.5px;
}

.header-identity {
    display: flex;
    align-items: center;
    gap: 16px;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
}

@media (max-width: 1200px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title i {
    color: #00b207;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.info-item {
    margin-bottom: 16px;
}

.info-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.info-value {
    font-size: 14px;
    color: #1a1a1a;
    font-weight: 500;
}

.info-value a {
    color: #00b207;
    text-decoration: none;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-new { background: #dbeafe; color: #1d4ed8; }
.badge-contacted { background: #e0e7ff; color: #4f46e5; }
.badge-qualified { background: #d1fae5; color: #059669; }
.badge-proposal { background: #fef3c7; color: #b45309; }
.badge-negotiation { background: #fed7aa; color: #c2410c; }
.badge-won { background: #bbf7d0; color: #16a34a; }
.badge-lost { background: #fecaca; color: #dc2626; }

.priority-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-low { background: #f3f4f6; color: #6b7280; }
.priority-medium { background: #dbeafe; color: #2563eb; }
.priority-high { background: #fef3c7; color: #d97706; }
.priority-urgent { background: #fee2e2; color: #dc2626; }

.tag {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    color: white;
    margin-right: 6px;
    margin-bottom: 6px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary { background: #00b207; color: white; }
.btn-primary:hover { background: #009906; }
.btn-secondary { background: #f3f4f6; color: #666; }
.btn-secondary:hover { background: #e5e7eb; }
.btn-danger { background: #fee2e2; color: #dc2626; }
.btn-danger:hover { background: #fecaca; }
.btn-sm { padding: 6px 12px; font-size: 13px; }

.quick-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.status-select {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

/* Activity Timeline */
.timeline {
    position: relative;
    padding-left: 28px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: #e5e7eb;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -28px;
    top: 4px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #00b207;
    border: 3px solid white;
    box-shadow: 0 0 0 2px #e5e7eb;
}

.timeline-dot.call { background: #3b82f6; }
.timeline-dot.email { background: #8b5cf6; }
.timeline-dot.meeting { background: #f59e0b; }
.timeline-dot.status_change { background: #10b981; }
.timeline-dot.follow_up { background: #ef4444; }

.timeline-content {
    font-size: 14px;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
}

.timeline-type {
    font-weight: 600;
    color: #1a1a1a;
    text-transform: capitalize;
}

.timeline-date {
    font-size: 12px;
    color: #999;
}

.timeline-description {
    color: #666;
}

.timeline-by {
    font-size: 12px;
    color: #999;
    margin-top: 4px;
}

/* Add Activity Form */
.activity-form {
    background: #f8fafc;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.activity-form .form-row {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
}

.activity-form select, .activity-form input, .activity-form textarea {
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
}

.activity-form textarea {
    width: 100%;
    min-height: 80px;
    resize: vertical;
}

.alert {
    padding: 14px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-success { background: #d1fae5; color: #065f46; }
.alert-error { background: #fee2e2; color: #991b1b; }

.notes-box {
    background: #f8fafc;
    border-radius: 8px;
    padding: 16px;
    font-size: 14px;
    color: #666;
    white-space: pre-wrap;
}

.delete-form {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #f3f4f6;
}

/* Twilio Communication Styles */
.comm-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 16px;
}

.comm-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 16px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.comm-btn.call-btn {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.comm-btn.call-btn:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-1px);
}

.comm-btn.sms-btn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.comm-btn.sms-btn:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
}

.comm-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.comm-btn i {
    font-size: 16px;
}

.sms-form {
    display: none;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.sms-form.active {
    display: block;
}

.sms-templates {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 12px;
}

.sms-template-btn {
    padding: 6px 12px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.sms-template-btn:hover {
    background: #e5e7eb;
}

.sms-template-btn.active {
    background: #00b207;
    color: white;
    border-color: #00b207;
}

.sms-textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    margin-bottom: 10px;
}

.sms-textarea:focus {
    outline: none;
    border-color: #00b207;
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
}

.char-count {
    font-size: 12px;
    color: #666;
    text-align: right;
    margin-bottom: 12px;
}

.char-count.warning {
    color: #d97706;
}

.char-count.over {
    color: #dc2626;
}

.comm-history {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.comm-history-title {
    font-size: 13px;
    font-weight: 600;
    color: #666;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.comm-item {
    display: flex;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 13px;
}

.comm-item:last-child {
    border-bottom: none;
}

.comm-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.comm-icon.call {
    background: #dbeafe;
    color: #2563eb;
}

.comm-icon.sms {
    background: #d1fae5;
    color: #059669;
}

.comm-icon.inbound {
    background: #fef3c7;
    color: #d97706;
}

.comm-details {
    flex: 1;
    min-width: 0;
}

.comm-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2px;
}

.comm-type {
    font-weight: 500;
    color: #1a1a1a;
}

.comm-time {
    font-size: 11px;
    color: #999;
}

.comm-content {
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.comm-duration {
    font-size: 11px;
    color: #666;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 10px;
}

.comm-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
}

.comm-stat {
    text-align: center;
}

.comm-stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
}

.comm-stat-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
}

.call-status {
    display: none;
    padding: 16px;
    background: #f0f9ff;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 16px;
}

.call-status.active {
    display: block;
}

.call-status .status-icon {
    font-size: 32px;
    color: #3b82f6;
    margin-bottom: 8px;
}

.call-status .status-text {
    font-weight: 500;
    color: #1e40af;
}

.opt-out-warning {
    background: #fef3c7;
    color: #92400e;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.twilio-not-configured {
    background: #f3f4f6;
    padding: 16px;
    border-radius: 8px;
    text-align: center;
    color: #666;
    font-size: 13px;
}

.twilio-not-configured i {
    font-size: 24px;
    color: #999;
    margin-bottom: 8px;
    display: block;
}
</style>

<div class="page-header">
    <div class="header-left">
        <div class="header-identity">
            <div class="lead-avatar" style="background: <?= $avatarColor ?>;"><?= $avatarInitials ?></div>
            <div>
                <h1><?= htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) ?></h1>
                <div class="header-badges">
                    <span class="badge badge-<?= $lead['status'] ?>"><?= ucfirst($lead['status']) ?></span>
                    <span class="priority-badge priority-<?= $lead['priority'] ?>"><?= ucfirst($lead['priority']) ?></span>
                    <?php foreach ($leadTags as $tag): ?>
                        <span class="tag" style="background: <?= $tag['color'] ?>"><?= htmlspecialchars($tag['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="<?= url('admin/leads') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> <?= $t['back'] ?>
        </a>
        <a href="<?= url('admin/leads/edit?id=' . $lead['id']) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> <?= $t['edit'] ?>
        </a>
    </div>
</div>

<?php if ($flash = getFlash('success')): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
<?php if ($flash = getFlash('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="content-grid">
    <div class="main-column">
        <!-- Contact Info -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-user"></i> <?= $t['contact_info'] ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><?= $t['email'] ?></div>
                    <div class="info-value">
                        <?php if ($lead['email']): ?>
                            <a href="mailto:<?= htmlspecialchars($lead['email']) ?>"><?= htmlspecialchars($lead['email']) ?></a>
                        <?php else: ?>
                            <span style="color: #999;"><?= $t['not_provided'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['phone'] ?></div>
                    <div class="info-value">
                        <?php if ($lead['phone']): ?>
                            <a href="tel:<?= htmlspecialchars($lead['phone']) ?>"><?= htmlspecialchars($lead['phone']) ?></a>
                        <?php else: ?>
                            <span style="color: #999;"><?= $t['not_provided'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['company'] ?></div>
                    <div class="info-value"><?= $lead['company_name'] ? htmlspecialchars($lead['company_name']) : '<span style="color: #999;">-</span>' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['job_title'] ?></div>
                    <div class="info-value"><?= $lead['job_title'] ? htmlspecialchars($lead['job_title']) : '<span style="color: #999;">-</span>' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['location'] ?></div>
                    <div class="info-value">
                        <?= $lead['city'] ? htmlspecialchars($lead['city']) . ', ' : '' ?>
                        <?= $lead['province'] ? htmlspecialchars($lead['province']) : '' ?>
                        <?= $lead['country'] ? ', ' . htmlspecialchars($lead['country']) : '' ?>
                        <?php if (!$lead['city'] && !$lead['province']): ?>
                            <span style="color: #999;">-</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['assigned_to'] ?></div>
                    <div class="info-value"><?= $lead['assigned_to_name'] ? htmlspecialchars($lead['assigned_to_name']) : '<span style="color: #999;">' . $t['unassigned'] . '</span>' ?></div>
                </div>
            </div>
        </div>

        <!-- Lead Details -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-info-circle"></i> <?= $t['lead_details'] ?></h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><?= $t['interest_type'] ?></div>
                    <div class="info-value"><?= ucwords(str_replace('_', ' ', $lead['interest_type'])) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['source'] ?></div>
                    <div class="info-value"><?= ucwords(str_replace('_', ' ', $lead['source'])) ?><?= $lead['source_details'] ? ' - ' . htmlspecialchars($lead['source_details']) : '' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['estimated_value'] ?></div>
                    <div class="info-value"><?= $lead['estimated_value'] ? '$' . number_format($lead['estimated_value'], 2) : '<span style="color: #999;">-</span>' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label"><?= $t['created'] ?></div>
                    <div class="info-value"><?= date('M j, Y g:i A', strtotime($lead['created_at'])) ?></div>
                </div>
            </div>
            <?php if ($lead['interest_details']): ?>
                <div class="info-item" style="margin-top: 16px;">
                    <div class="info-label"><?= $t['interest_details'] ?></div>
                    <div class="notes-box"><?= nl2br(htmlspecialchars($lead['interest_details'])) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($lead['notes']): ?>
                <div class="info-item" style="margin-top: 16px;">
                    <div class="info-label">Notes</div>
                    <div class="notes-box"><?= nl2br(htmlspecialchars($lead['notes'])) ?></div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($supplierApp)): ?>
        <!-- Supplier Application -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-file-contract"></i> Supplier Application #<?= $supplierApp['id'] ?></h3>
            <div style="display: flex; gap: 8px; margin-bottom: 16px; align-items: center; flex-wrap: wrap;">
                <span class="badge badge-<?= $supplierApp['status'] === 'pending' ? 'new' : ($supplierApp['status'] === 'approved' ? 'won' : ($supplierApp['status'] === 'rejected' ? 'lost' : ($supplierApp['status'] === 'info_requested' ? 'proposal' : 'contacted'))) ?>">
                    <?= ucfirst(str_replace('_', ' ', $supplierApp['status'])) ?>
                </span>
                <span style="font-size: 12px; color: #999;">Submitted <?= date('M j, Y g:i A', strtotime($supplierApp['created_at'])) ?></span>
            </div>

            <?php if (in_array($supplierApp['status'], ['pending', 'under_review', 'info_requested'])): ?>
            <!-- Application Actions -->
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e5e7eb;">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('approveModal').style.display='flex'" style="gap: 6px;">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
                <button type="button" class="btn" onclick="document.getElementById('rejectModal').style.display='flex'" style="background: #fee2e2; color: #dc2626; gap: 6px;">
                    <i class="fas fa-times-circle"></i> Reject
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('infoModal').style.display='flex'" style="gap: 6px;">
                    <i class="fas fa-question-circle"></i> Request Info
                </button>
            </div>
            <?php endif; ?>

            <?php if ($supplierApp['status'] === 'approved' && !empty($supplierApp['reviewed_at'])): ?>
            <div style="padding: 12px 16px; background: #f0fdf4; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 16px; font-size: 13px; color: #166534;">
                <i class="fas fa-check-circle"></i>
                Approved on <?= date('M j, Y g:i A', strtotime($supplierApp['reviewed_at'])) ?>
                <?php if (!empty($supplierApp['admin_notes'])): ?>
                    <div style="margin-top: 6px; color: #15803d;"><?= htmlspecialchars($supplierApp['admin_notes']) ?></div>
                <?php endif; ?>
            </div>
            <?php elseif ($supplierApp['status'] === 'rejected' && !empty($supplierApp['reviewed_at'])): ?>
            <div style="padding: 12px 16px; background: #fef2f2; border-radius: 8px; border: 1px solid #fecaca; margin-bottom: 16px; font-size: 13px; color: #991b1b;">
                <i class="fas fa-times-circle"></i>
                Rejected on <?= date('M j, Y g:i A', strtotime($supplierApp['reviewed_at'])) ?>
                <?php if (!empty($supplierApp['admin_notes'])): ?>
                    <div style="margin-top: 6px; color: #b91c1c;"><?= nl2br(htmlspecialchars($supplierApp['admin_notes'])) ?></div>
                <?php endif; ?>
            </div>
            <?php elseif ($supplierApp['status'] === 'info_requested'): ?>
            <div style="padding: 12px 16px; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe; margin-bottom: 16px; font-size: 13px; color: #1e40af;">
                <i class="fas fa-clock"></i>
                Waiting for additional information from applicant
                <?php if (!empty($supplierApp['admin_notes'])): ?>
                    <div style="margin-top: 6px; color: #1d4ed8; white-space: pre-line;"><?= htmlspecialchars($supplierApp['admin_notes']) ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($linkedSupplier) && in_array($linkedSupplier['status'], ['pending_verification', 'inactive'])): ?>
            <?php
                $vlDeadline = $linkedSupplier['verification_deadline'] ?? null;
                $vlDaysLeft = $vlDeadline ? max(0, (int)ceil((strtotime($vlDeadline) - time()) / 86400)) : null;
                $vlExpired = $linkedSupplier['status'] === 'inactive' || ($vlDaysLeft !== null && $vlDaysLeft <= 0);
                $vlUrgent = !$vlExpired && $vlDaysLeft !== null && $vlDaysLeft <= 7;
            ?>
            <div style="padding: 14px 16px; background: <?= $vlExpired ? '#fef2f2' : ($vlUrgent ? '#fef3c7' : '#eff6ff') ?>; border-radius: 8px; border: 1px solid <?= $vlExpired ? '#fecaca' : ($vlUrgent ? '#fde68a' : '#bfdbfe') ?>; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div style="font-size: 13px; color: <?= $vlExpired ? '#991b1b' : ($vlUrgent ? '#92400e' : '#1e40af') ?>;">
                        <i class="fas fa-<?= $vlExpired ? 'exclamation-triangle' : 'hourglass-half' ?>"></i>
                        <?php if ($vlExpired): ?>
                            <strong>Verification Expired</strong> — Account deactivated.
                            <?php if ($vlDeadline): ?>Deadline was <?= date('M j, Y', strtotime($vlDeadline)) ?>.<?php endif; ?>
                        <?php else: ?>
                            <strong>Verification Deadline:</strong> <?= date('M j, Y', strtotime($vlDeadline)) ?>
                            (<?= $vlDaysLeft ?> day<?= $vlDaysLeft !== 1 ? 's' : '' ?> remaining)
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="<?= url('admin/leads/extend-supplier-deadline') ?>" style="display: flex; align-items: center; gap: 8px;">
                        <?= csrfField() ?>
                        <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                        <input type="hidden" name="supplier_id" value="<?= $linkedSupplier['id'] ?>">
                        <select name="extra_days" style="padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;">
                            <option value="7">+7 days</option>
                            <option value="15" selected>+15 days</option>
                            <option value="30">+30 days</option>
                            <option value="60">+60 days</option>
                        </select>
                        <button type="submit" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px; gap: 4px;">
                            <i class="fas fa-calendar-plus"></i>
                            <?= $vlExpired ? 'Reactivate & Extend' : 'Extend' ?>
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">NEQ (Enterprise Number)</div>
                    <div class="info-value" style="font-family: monospace; font-size: 16px; letter-spacing: 1px;"><?= htmlspecialchars($supplierApp['neq_number'] ?? '') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Legal Name</div>
                    <div class="info-value"><?= htmlspecialchars($supplierApp['legal_name'] ?? '') ?></div>
                </div>
                <?php if (!empty($supplierApp['operating_names'])): ?>
                <div class="info-item">
                    <div class="info-label">Operating Names</div>
                    <div class="info-value"><?= htmlspecialchars($supplierApp['operating_names']) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <div class="info-label">Registered Address</div>
                    <div class="info-value">
                        <?= htmlspecialchars($supplierApp['registered_address_street'] ?? '') ?><br>
                        <?= htmlspecialchars($supplierApp['registered_address_city'] ?? '') ?>, <?= htmlspecialchars($supplierApp['registered_address_province'] ?? '') ?> <?= htmlspecialchars($supplierApp['registered_address_postal'] ?? '') ?>
                    </div>
                </div>
            </div>

            <!-- Verification Documents -->
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f3f4f6;">
                <div class="info-label" style="margin-bottom: 12px; font-size: 13px; font-weight: 600;">Verification Documents</div>
                <?php
                $docs = [
                    'doc_certificate_incorporation' => 'Certificate of Incorporation',
                    'doc_declaration_registration' => 'Declaration of Registration',
                    'doc_enterprise_register' => 'Enterprise Register File Search',
                ];
                $hasAnyDoc = false;
                $missingCount = 0;
                foreach ($docs as $f => $l) {
                    $st = $supplierApp[$f . '_status'] ?? 'pending';
                    if (empty($supplierApp[$f]) && $st !== 'na') $missingCount++;
                }
                ?>

                <?php if ($missingCount > 0): ?>
                <div style="margin-bottom: 12px; padding: 12px 16px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 16px;"></i>
                    <span style="font-size: 13px; color: #991b1b; font-weight: 600;"><?= $missingCount ?> document<?= $missingCount > 1 ? 's' : '' ?> missing — supplier needs to upload</span>
                </div>
                <?php endif; ?>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach ($docs as $field => $label):
                        $statusField = $field . '_status';
                        $docStatus = $supplierApp[$statusField] ?? 'pending';
                        $hasFile = !empty($supplierApp[$field]);

                        // Background/border based on review status
                        if ($docStatus === 'approved') {
                            $bgColor = '#f0fdf4'; $borderColor = '#bbf7d0';
                        } elseif ($docStatus === 'rejected') {
                            $bgColor = '#fef2f2'; $borderColor = '#fecaca';
                        } elseif ($docStatus === 'na') {
                            $bgColor = '#f9fafb'; $borderColor = '#e5e7eb';
                        } elseif ($hasFile) {
                            $bgColor = '#fffbeb'; $borderColor = '#fde68a';
                        } else {
                            $bgColor = '#fef2f2'; $borderColor = '#fecaca';
                        }
                    ?>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; background: <?= $bgColor ?>; border-radius: 8px; border: 1px solid <?= $borderColor ?>;" id="doc-row-<?= $field ?>">
                            <?php if ($docStatus === 'na'): ?>
                                <i class="fas fa-minus-circle" style="font-size: 20px; color: #9ca3af; flex-shrink: 0;"></i>
                                <div style="flex: 1;">
                                    <div style="font-size: 13px; color: #9ca3af; text-decoration: line-through;"><?= $label ?></div>
                                    <div style="font-size: 11px; color: #9ca3af;">N/A — Not required</div>
                                </div>
                            <?php elseif ($hasFile): ?>
                                <?php
                                $hasAnyDoc = true;
                                $ext = strtolower(pathinfo($supplierApp[$field], PATHINFO_EXTENSION));
                                $icon = $ext === 'pdf' ? 'fa-file-pdf' : 'fa-file-image';
                                $iconColor = $ext === 'pdf' ? '#dc2626' : '#3b82f6';
                                ?>
                                <i class="fas <?= $icon ?>" style="font-size: 20px; color: <?= $iconColor ?>; flex-shrink: 0;"></i>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 600; color: #1a1a1a;">
                                        <?= $label ?>
                                        <?php if ($docStatus === 'approved'): ?>
                                            <i class="fas fa-check-circle" style="color: #16a34a; margin-left: 4px;" title="Approved"></i>
                                        <?php elseif ($docStatus === 'rejected'): ?>
                                            <i class="fas fa-times-circle" style="color: #dc2626; margin-left: 4px;" title="Rejected"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 11px; color: #6b7280; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"><?= htmlspecialchars(basename($supplierApp[$field])) ?></div>
                                </div>
                                <a href="<?= url($supplierApp[$field]) ?>" target="_blank" class="btn btn-secondary btn-sm" style="flex-shrink: 0;">
                                    <i class="fas fa-download"></i> View
                                </a>
                            <?php else: ?>
                                <i class="fas fa-exclamation-circle" style="font-size: 20px; color: #dc2626; flex-shrink: 0;"></i>
                                <div style="flex: 1;">
                                    <div style="font-size: 13px; color: #dc2626; font-weight: 600;"><?= $label ?></div>
                                    <div style="font-size: 11px; color: #ef4444;">Not uploaded</div>
                                </div>
                            <?php endif; ?>

                            <!-- Review action buttons -->
                            <div style="display: flex; gap: 5px; flex-shrink: 0;" id="doc-actions-<?= $field ?>">
                                <button type="button" onclick="setDocStatus('<?= $field ?>', 'approved', <?= $supplierApp['id'] ?>)"
                                    style="padding: 5px 10px; border-radius: 20px; border: 1px solid <?= $docStatus === 'approved' ? '#16a34a' : '#e5e7eb' ?>; background: <?= $docStatus === 'approved' ? '#dcfce7' : 'white' ?>; color: <?= $docStatus === 'approved' ? '#16a34a' : '#6b7280' ?>; cursor: pointer; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px; white-space: nowrap;">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" onclick="setDocStatus('<?= $field ?>', 'rejected', <?= $supplierApp['id'] ?>)"
                                    style="padding: 5px 10px; border-radius: 20px; border: 1px solid <?= $docStatus === 'rejected' ? '#dc2626' : '#e5e7eb' ?>; background: <?= $docStatus === 'rejected' ? '#fee2e2' : 'white' ?>; color: <?= $docStatus === 'rejected' ? '#dc2626' : '#6b7280' ?>; cursor: pointer; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px; white-space: nowrap;">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                <button type="button" onclick="setDocStatus('<?= $field ?>', 'na', <?= $supplierApp['id'] ?>)"
                                    style="padding: 5px 10px; border-radius: 20px; border: 1px solid <?= $docStatus === 'na' ? '#6b7280' : '#e5e7eb' ?>; background: <?= $docStatus === 'na' ? '#f3f4f6' : 'white' ?>; color: <?= $docStatus === 'na' ? '#6b7280' : '#9ca3af' ?>; cursor: pointer; font-size: 11px; font-weight: 600; white-space: nowrap;">
                                    N/A
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($hasAnyDoc): ?>
                <div style="margin-top: 12px; padding: 10px 14px; background: #eff6ff; border-radius: 8px; font-size: 12px; color: #1e40af;">
                    <i class="fas fa-info-circle"></i>
                    Verify business registration at <a href="https://www.registreentreprises.gouv.qc.ca" target="_blank" style="color: #1d4ed8; font-weight: 600;">Registraire des entreprises du Qu&eacute;bec</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($businessProfile)): ?>
        <!-- Distribution Business Account Card -->
        <?php
            $bpStatus    = $businessProfile['status'] ?? 'pending';
            $bpStatusMap = [
                'pending'   => ['label' => 'Pending Review',  'color' => '#d97706', 'bg' => '#fffbeb', 'icon' => 'clock'],
                'active'    => ['label' => 'Approved',        'color' => '#16a34a', 'bg' => '#f0fdf4', 'icon' => 'check-circle'],
                'suspended' => ['label' => 'Rejected',        'color' => '#dc2626', 'bg' => '#fef2f2', 'icon' => 'times-circle'],
            ];
            $bpBadge = $bpStatusMap[$bpStatus] ?? $bpStatusMap['pending'];
        ?>
        <div class="card">
            <h3 class="card-title" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                <span><i class="fas fa-building"></i> Distribution Business Account</span>
                <a href="<?= url('admin/business-accounts/view?id=' . $businessProfile['id']) ?>" class="btn btn-secondary btn-sm" style="font-size:12px; font-weight:400;">
                    <i class="fas fa-external-link-alt"></i> Full Profile
                </a>
            </h3>

            <!-- Status badge -->
            <div style="display:flex; gap:8px; align-items:center; margin-bottom:16px; flex-wrap:wrap;">
                <span style="display:inline-flex; align-items:center; gap:6px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; background:<?= $bpBadge['bg'] ?>; color:<?= $bpBadge['color'] ?>;">
                    <i class="fas fa-<?= $bpBadge['icon'] ?>"></i> <?= $bpBadge['label'] ?>
                </span>
                <span style="font-size:12px; color:#999;">
                    Registered <?= date('M j, Y', strtotime($businessProfile['created_at'] ?? 'now')) ?>
                </span>
            </div>

            <?php if ($bpStatus === 'pending'): ?>
            <!-- Action buttons -->
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; padding:16px; background:#f8fafc; border-radius:8px; border:1px solid #e5e7eb;">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('bpApproveModal').style.display='flex'" style="gap:6px;">
                    <i class="fas fa-check-circle"></i> Approve Account
                </button>
                <button type="button" class="btn" onclick="document.getElementById('bpRejectModal').style.display='flex'" style="background:#fee2e2; color:#dc2626; gap:6px;">
                    <i class="fas fa-times-circle"></i> Reject
                </button>
            </div>
            <?php elseif ($bpStatus === 'active' && !empty($businessProfile['verified_at'])): ?>
            <div style="padding:10px 14px; background:#f0fdf4; border-radius:8px; border-left:3px solid #16a34a; font-size:13px; color:#166534; margin-bottom:16px;">
                <i class="fas fa-check-circle"></i>
                Approved on <?= date('M j, Y g:i A', strtotime($businessProfile['verified_at'])) ?>
            </div>
            <?php elseif ($bpStatus === 'suspended' && !empty($businessProfile['rejection_reason'])): ?>
            <div style="padding:10px 14px; background:#fef2f2; border-radius:8px; border-left:3px solid #ef4444; font-size:13px; color:#991b1b; margin-bottom:16px;">
                <i class="fas fa-times-circle"></i>
                <strong>Rejected.</strong> Reason: <?= htmlspecialchars($businessProfile['rejection_reason']) ?>
            </div>
            <?php endif; ?>

            <?php
                $bpDeadline  = $businessProfile['verification_deadline'] ?? null;
                $bpDaysLeft  = $bpDeadline ? max(0, (int)ceil((strtotime($bpDeadline) - time()) / 86400)) : null;
                $bpExpired   = $bpStatus === 'pending' && $bpDeadline && $bpDaysLeft <= 0;
                $bpUrgent    = !$bpExpired && $bpDaysLeft !== null && $bpDaysLeft <= 7;
            ?>
            <?php if ($bpStatus === 'pending' && $bpDeadline): ?>
            <div style="padding:14px 16px; background:<?= $bpExpired ? '#fef2f2' : ($bpUrgent ? '#fef3c7' : '#eff6ff') ?>; border-radius:8px; border:1px solid <?= $bpExpired ? '#fecaca' : ($bpUrgent ? '#fde68a' : '#bfdbfe') ?>; margin-bottom:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <div style="font-size:13px; color:<?= $bpExpired ? '#991b1b' : ($bpUrgent ? '#92400e' : '#1e40af') ?>;">
                        <i class="fas fa-<?= $bpExpired ? 'exclamation-triangle' : 'hourglass-half' ?>"></i>
                        <?php if ($bpExpired): ?>
                            <strong>Verification Deadline Passed</strong> — deadline was <?= date('M j, Y', strtotime($bpDeadline)) ?>.
                        <?php else: ?>
                            <strong>Verification Deadline:</strong> <?= date('M j, Y', strtotime($bpDeadline)) ?>
                            (<?= $bpDaysLeft ?> day<?= $bpDaysLeft !== 1 ? 's' : '' ?> remaining)
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="<?= url('admin/business-accounts/extend-deadline') ?>" style="display:flex; align-items:center; gap:8px;">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= $businessProfile['id'] ?>">
                        <input type="hidden" name="back_url" value="<?= url('admin/leads/view?id=' . $lead['id']) ?>">
                        <select name="extra_days" style="padding:6px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:12px;">
                            <option value="7">+7 days</option>
                            <option value="15" selected>+15 days</option>
                            <option value="30">+30 days</option>
                            <option value="60">+60 days</option>
                        </select>
                        <button type="submit" class="btn btn-secondary" style="font-size:12px; padding:6px 12px; gap:4px;">
                            <i class="fas fa-calendar-plus"></i> Extend
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Company info -->
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:16px; margin-bottom:16px;">
                <div>
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:4px;">Company Name</div>
                    <div style="font-size:14px; font-weight:600; color:#111827;"><?= htmlspecialchars($businessProfile['company_name'] ?? '') ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:4px;">NEQ</div>
                    <div style="font-size:14px; font-family:monospace; letter-spacing:1px;"><?= htmlspecialchars($businessProfile['neq_number'] ?? '') ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:4px;">Legal Name</div>
                    <div style="font-size:14px;"><?= htmlspecialchars($businessProfile['legal_name'] ?? '') ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:4px;">Contact</div>
                    <div style="font-size:14px;"><?= htmlspecialchars($businessProfile['first_name'] . ' ' . $businessProfile['last_name']) ?></div>
                    <div style="font-size:12px; color:#6b7280;"><?= htmlspecialchars($businessProfile['email'] ?? '') ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:4px;">Registered Address</div>
                    <div style="font-size:13px; color:#374151;">
                        <?= htmlspecialchars($businessProfile['registered_address_street'] ?? '') ?><br>
                        <?= htmlspecialchars($businessProfile['registered_address_city'] ?? '') ?>, <?= htmlspecialchars($businessProfile['registered_address_province'] ?? '') ?> <?= htmlspecialchars($businessProfile['registered_address_postal'] ?? '') ?>
                    </div>
                </div>
                <?php if (!empty($businessProfile['operating_names'])): ?>
                <div>
                    <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:4px;">Operating Names</div>
                    <div style="font-size:13px;"><?= htmlspecialchars($businessProfile['operating_names']) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Document -->
            <?php if (!empty($businessProfile['doc_certificate'])): ?>
            <?php $docExt = strtolower(pathinfo($businessProfile['doc_certificate'], PATHINFO_EXTENSION)); ?>
            <div style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:#f9fafb; border-radius:8px; font-size:13px; color:#374151; border:1px solid #e5e7eb;">
                <i class="fas fa-<?= $docExt === 'pdf' ? 'file-pdf' : 'file-image' ?>" style="color:<?= $docExt === 'pdf' ? '#dc2626' : '#3b82f6' ?>;"></i>
                <span style="flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars(basename($businessProfile['doc_certificate'])) ?></span>
                <a href="<?= url($businessProfile['doc_certificate']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="font-size:12px; flex-shrink:0;">
                    <i class="fas fa-external-link-alt"></i> View
                </a>
            </div>
            <?php else: ?>
            <div style="padding:10px 14px; background:#fef3c7; border-radius:8px; font-size:13px; color:#92400e; border:1px solid #fde68a;">
                <i class="fas fa-exclamation-triangle"></i> No verification document uploaded yet.
            </div>
            <?php endif; ?>
        </div>

        <!-- Approve modal -->
        <div id="bpApproveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:12px; padding:28px; max-width:440px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <h3 style="margin:0 0 8px; color:#111827;"><i class="fas fa-check-circle" style="color:#16a34a;"></i> Approve Business Account</h3>
                <p style="font-size:14px; color:#6b7280; margin:0 0 20px;">
                    This will activate the account for <strong><?= htmlspecialchars($businessProfile['company_name']) ?></strong> and send them an approval email.
                </p>
                <form method="POST" action="<?= url('admin/business-accounts/approve') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $businessProfile['id'] ?>">
                    <input type="hidden" name="back_url" value="<?= url('admin/leads/view?id=' . $lead['id']) ?>">
                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('bpApproveModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle"></i> Confirm Approval</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reject modal -->
        <div id="bpRejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:12px; padding:28px; max-width:440px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <h3 style="margin:0 0 8px; color:#dc2626;"><i class="fas fa-times-circle"></i> Reject Application</h3>
                <p style="font-size:14px; color:#6b7280; margin:0 0 16px;">
                    Provide a reason (optional). An email will be sent to the applicant.
                </p>
                <form method="POST" action="<?= url('admin/business-accounts/reject') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $businessProfile['id'] ?>">
                    <input type="hidden" name="back_url" value="<?= url('admin/leads/view?id=' . $lead['id']) ?>">
                    <textarea name="rejection_reason" rows="3" placeholder="Reason for rejection (optional)…"
                        style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:10px 12px; font-size:13px; resize:vertical; font-family:inherit; margin-bottom:16px; box-sizing:border-box;"></textarea>
                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('bpRejectModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn" style="background:#dc2626; color:#fff;"><i class="fas fa-times-circle"></i> Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($lead['interest_type'] === 'driver'): ?>
        <!-- Driver Application Card -->
        <div class="card">
            <h3 class="card-title" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                <span><i class="fas fa-id-card"></i> Driver Application</span>
                <a href="<?= url('admin/delivery/staff?tab=applications') ?>" class="btn btn-secondary btn-sm" style="font-size:12px; font-weight:400;">
                    <i class="fas fa-external-link-alt"></i> View in Delivery Staff
                </a>
            </h3>

            <?php if (!empty($driverApp)): ?>
            <?php
            $stageBadges = [
                'submitted'           => ['label'=>'Submitted',            'color'=>'#4f46e5','bg'=>'#eef2ff'],
                'under_review'        => ['label'=>'Under Review',         'color'=>'#d97706','bg'=>'#fffbeb'],
                'interview_requested' => ['label'=>'Interview Requested',  'color'=>'#b45309','bg'=>'#fef3c7'],
                'interview_scheduled' => ['label'=>'Interview Scheduled',  'color'=>'#059669','bg'=>'#ecfdf5'],
                'approved'            => ['label'=>'Approved',             'color'=>'#16a34a','bg'=>'#f0fdf4'],
                'rejected'            => ['label'=>'Rejected',             'color'=>'#dc2626','bg'=>'#fef2f2'],
            ];
            $driverStage = $driverApp['pipeline_stage'] ?? 'submitted';
            $driverBadge = $stageBadges[$driverStage] ?? ['label'=>ucfirst($driverStage),'color'=>'#6b7280','bg'=>'#f3f4f6'];
            ?>

            <div style="display:flex; gap:8px; align-items:center; margin-bottom:16px; flex-wrap:wrap;">
                <span style="background:<?= $driverBadge['bg'] ?>; color:<?= $driverBadge['color'] ?>; padding:4px 14px; border-radius:20px; font-size:12px; font-weight:600;">
                    <?= $driverBadge['label'] ?>
                </span>
                <span style="font-size:12px; color:#999;">Application #<?= $driverApp['id'] ?> · Submitted <?= date('M j, Y g:i A', strtotime($driverApp['created_at'])) ?></span>
            </div>

            <?php if (!in_array($driverStage, ['approved','rejected'])): ?>
            <!-- Pipeline Actions -->
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; padding:14px 16px; background:#f8fafc; border-radius:8px; border:1px solid #e5e7eb;">
                <?php if ($driverStage === 'submitted'): ?>
                <button type="button" onclick="driverMarkUnderReview(<?= $driverApp['id'] ?>, this)" class="btn btn-secondary" style="font-size:13px; gap:5px;">
                    <i class="fas fa-magnifying-glass"></i> Mark Under Review
                </button>
                <?php endif; ?>
                <?php if (in_array($driverStage, ['submitted','under_review'])): ?>
                <button type="button" onclick="driverOpenInterviewModal(<?= $driverApp['id'] ?>)" class="btn btn-primary" style="font-size:13px; gap:5px;">
                    <i class="fas fa-calendar"></i> Request Interview
                </button>
                <?php endif; ?>
                <button type="button" onclick="driverOpenApproveModal(<?= $driverApp['id'] ?>)"
                    style="background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; padding:8px 14px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; display:flex; align-items:center; gap:5px;">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
                <button type="button" onclick="driverOpenRejectModal(<?= $driverApp['id'] ?>)" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; padding:8px 14px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; display:flex; align-items:center; gap:5px;">
                    <i class="fas fa-times-circle"></i> Reject
                </button>
            </div>
            <?php endif; ?>

            <!-- Application Summary -->
            <div class="info-grid" style="margin-bottom:20px;">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= htmlspecialchars($driverApp['first_name'] . ' ' . $driverApp['last_name']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Vehicle Type</div>
                    <div class="info-value"><?= htmlspecialchars(ucfirst($driverApp['vehicle_type'] ?? 'N/A')) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">City</div>
                    <div class="info-value"><?= htmlspecialchars(($driverApp['city'] ?? '') . ', ' . ($driverApp['province'] ?? '')) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Preferred Shift</div>
                    <div class="info-value"><?= htmlspecialchars($driverApp['preferred_shift'] ?? 'N/A') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Available Days</div>
                    <div class="info-value" style="font-size:12px;"><?= htmlspecialchars(str_replace(',', ', ', $driverApp['available_days'] ?? 'N/A')) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Previous Experience</div>
                    <div class="info-value"><?= ($driverApp['previous_experience'] ?? '') === 'yes' ? 'Yes' : 'No' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Criminal Record Declared</div>
                    <div class="info-value">
                        <?php if (!empty($driverApp['criminal_record'])): ?>
                            <span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600;">Yes</span>
                        <?php else: ?>
                            <span style="background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600;">No</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($driverApp['criminal_record']) && !empty($driverApp['criminal_record_details'])): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Declaration Details</div>
                    <div class="info-value" style="white-space:pre-wrap; font-size:13px;"><?= htmlspecialchars($driverApp['criminal_record_details']) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Message Thread -->
            <div id="message-thread-section" style="border-top:1px solid #f3f4f6; padding-top:20px;">
                <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:12px;">
                    <i class="fas fa-comments" style="color:#3b82f6;"></i> Message Thread
                </div>
                <div id="driver-messages-box" style="max-height:320px; overflow-y:auto; display:flex; flex-direction:column; gap:8px; margin-bottom:12px; padding:14px; background:#f8fafc; border-radius:8px; border:1px solid #e5e7eb;">
                    <?php if (empty($driverMessages)): ?>
                    <div style="text-align:center; color:#9ca3af; font-size:13px; padding:24px 0;">No messages yet. Start the conversation below.</div>
                    <?php else: ?>
                    <?php foreach ($driverMessages as $dm):
                        $isAdmin   = $dm['sender_type'] === 'admin';
                        $isSystem  = $dm['sender_type'] === 'system' || ($isAdmin && empty($dm['sender_id']));
                        if ($isSystem) {
                            $senderLabel = '<i class="fas fa-robot" style="font-size:10px;"></i> System';
                            $align  = 'center';
                            $bgColor = '#f3f4f6';
                            $fgColor = '#6b7280';
                            $border  = '1px solid #e5e7eb';
                        } elseif ($isAdmin) {
                            $name = trim($dm['sender_name'] ?? '');
                            $senderLabel = htmlspecialchars($name ?: 'Admin') . ' <span style="opacity:.7;">(Admin)</span>';
                            $align  = 'flex-end';
                            $bgColor = '#1d4ed8';
                            $fgColor = '#fff';
                            $border  = 'none';
                        } else {
                            $senderLabel = htmlspecialchars($driverApp['first_name'] ?? 'Applicant');
                            $align  = 'flex-start';
                            $bgColor = '#ffffff';
                            $fgColor = '#374151';
                            $border  = '1px solid #e5e7eb';
                        }
                    ?>
                    <div style="display:flex; flex-direction:column; align-items:<?= $isSystem ? 'center' : $align ?>;">
                        <?php if ($isSystem): ?>
                        <div style="max-width:85%; background:<?= $bgColor ?>; color:<?= $fgColor ?>; padding:6px 12px; border-radius:20px; font-size:12px; line-height:1.4; border:<?= $border ?>; text-align:center;">
                            <?= nl2br(htmlspecialchars($dm['message'])) ?>
                        </div>
                        <?php else: ?>
                        <div style="max-width:75%; background:<?= $bgColor ?>; color:<?= $fgColor ?>; padding:9px 13px; border-radius:10px; font-size:13px; line-height:1.5; box-shadow:0 1px 2px rgba(0,0,0,0.06); border:<?= $border ?>;">
                            <?= nl2br(htmlspecialchars($dm['message'])) ?>
                        </div>
                        <?php endif; ?>
                        <div style="font-size:11px; color:#9ca3af; margin-top:3px;">
                            <?= $senderLabel ?> · <?= date('M j, Y g:i A', strtotime($dm['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div style="display:flex; gap:8px; align-items:flex-end;">
                    <textarea id="driver-msg-input" rows="2" placeholder="Type a message to the applicant..." style="flex:1; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-family:inherit; resize:vertical;"></textarea>
                    <button type="button" onclick="driverSendMsg(<?= $driverApp['id'] ?>, this)" style="background:#1d4ed8; color:#fff; border:none; border-radius:8px; padding:9px 18px; cursor:pointer; font-size:13px; font-weight:600; white-space:nowrap;">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </div>
            </div>

            <?php else: ?>
            <div style="text-align:center; color:#9ca3af; font-size:13px; padding:24px 0;">
                <i class="fas fa-file-slash" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                No driver application linked to this lead yet.
            </div>
            <?php endif; ?>
        </div>

        <!-- Background Check -->
        <?php if (!empty($driverApp)): ?>
        <?php
        $bgStatus   = $driverApp['bgcheck_status'] ?? 'not_requested';
        $bgColors   = [
            'not_requested' => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'icon' => 'circle-minus',      'label' => 'Not Requested'],
            'requested'     => ['bg' => '#fef3c7', 'color' => '#b45309', 'icon' => 'envelope',           'label' => 'Requested — Awaiting Upload'],
            'uploaded'      => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'icon' => 'cloud-arrow-up',    'label' => 'Uploaded — Pending Review'],
            'verified'      => ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'shield-check',      'label' => 'Verified ✓'],
            'flagged'       => ['bg' => '#fee2e2', 'color' => '#dc2626', 'icon' => 'shield-exclamation', 'label' => 'Flagged ⚠'],
            'waived'        => ['bg' => '#f0fdf4', 'color' => '#15803d', 'icon' => 'shield-halved',     'label' => 'Waived by Admin'],
        ];
        $bgc = $bgColors[$bgStatus] ?? $bgColors['not_requested'];
        ?>
        <div class="card" id="bgcheck-card">
            <h3 class="card-title" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                <span><i class="fas fa-shield-halved"></i> Background Check</span>
                <span style="display:inline-flex; align-items:center; gap:6px; background:<?= $bgc['bg'] ?>; color:<?= $bgc['color'] ?>; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">
                    <i class="fas fa-<?= $bgc['icon'] ?>"></i> <?= $bgc['label'] ?>
                </span>
            </h3>

            <?php if ($bgStatus === 'not_requested'): ?>
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:12px 16px; font-size:13px; color:#1e40af;">
                    <i class="fas fa-info-circle"></i> The driver uploads their background check themselves through their <strong>Driver Portal</strong> after approval. No action needed from admin at this stage.
                </div>

            <?php elseif ($bgStatus === 'uploaded'): ?>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; font-size:13px;">
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;">Document Type</div>
                        <div style="font-weight:500;"><?= htmlspecialchars($driverApp['bgcheck_doc_type'] ?? '—') ?></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;">Document Date</div>
                        <div style="font-weight:500; <?= $driverApp['bgcheck_doc_date'] && strtotime($driverApp['bgcheck_doc_date']) < strtotime('-1 year') ? 'color:#dc2626;' : '' ?>">
                            <?= $driverApp['bgcheck_doc_date'] ? date('M j, Y', strtotime($driverApp['bgcheck_doc_date'])) : '—' ?>
                            <?php if ($driverApp['bgcheck_doc_date'] && strtotime($driverApp['bgcheck_doc_date']) < strtotime('-1 year')): ?>
                                <span style="color:#dc2626; font-size:11px;"> ⚠ Expired (>1 year)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;">Uploaded</div>
                        <div><?= $driverApp['bgcheck_uploaded_at'] ? date('M j, Y g:i A', strtotime($driverApp['bgcheck_uploaded_at'])) : '—' ?></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;">Document</div>
                        <?php if (!empty($driverApp['bgcheck_file_path'])): ?>
                            <a href="<?= url('admin/delivery/bgcheck/download?app_id=' . $driverApp['id']) ?>" target="_blank" style="color:#2563eb; font-weight:600; font-size:13px;">
                                <i class="fas fa-file-arrow-down"></i> View / Download
                            </a>
                        <?php else: ?>
                            <span style="color:#9ca3af;">No file</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Criminal record declaration from application -->
                <div style="background:<?= !empty($driverApp['criminal_record']) ? '#fffbeb' : '#f0fdf4' ?>; border:1px solid <?= !empty($driverApp['criminal_record']) ? '#fde68a' : '#bbf7d0' ?>; border-radius:8px; padding:12px 16px; font-size:13px; margin-bottom:16px;">
                    <span style="font-weight:600; color:#374151;">Self-Declaration:</span>
                    <?php if (!empty($driverApp['criminal_record'])): ?>
                        <span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600; margin-left:6px;">Criminal record declared — Yes</span>
                        <?php if (!empty($driverApp['criminal_record_details'])): ?>
                            <div style="margin-top:6px; color:#6b7280; white-space:pre-wrap;"><?= htmlspecialchars($driverApp['criminal_record_details']) ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600; margin-left:6px;">No criminal record declared</span>
                    <?php endif; ?>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap; padding:14px; background:#f8fafc; border-radius:8px; border:1px solid #e5e7eb;">
                    <button type="button" onclick="bgcheckAction(<?= $driverApp['id'] ?>, 'verify', this)" style="background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;">
                        <i class="fas fa-shield-check"></i> Verify — Mark Clear
                    </button>
                    <button type="button" onclick="bgcheckOpenFlag(<?= $driverApp['id'] ?>)" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;">
                        <i class="fas fa-shield-exclamation"></i> Flag Issue
                    </button>
                    <button type="button" onclick="bgcheckOpenWaive(<?= $driverApp['id'] ?>)" style="background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;">
                        <i class="fas fa-circle-exclamation"></i> Waive (Override)
                    </button>
                </div>

            <?php elseif ($bgStatus === 'verified' || $bgStatus === 'waived'): ?>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; font-size:13px; margin-bottom:16px;">
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;">Type</div>
                        <div><?= htmlspecialchars($driverApp['bgcheck_doc_type'] ?? '—') ?></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;">Document Date</div>
                        <div><?= $driverApp['bgcheck_doc_date'] ? date('M j, Y', strtotime($driverApp['bgcheck_doc_date'])) : '—' ?></div>
                    </div>
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;"><?= $bgStatus === 'verified' ? 'Verified' : 'Waived' ?> On</div>
                        <div><?= $driverApp['bgcheck_verified_at'] ? date('M j, Y', strtotime($driverApp['bgcheck_verified_at'])) : '—' ?></div>
                    </div>
                    <?php if (!empty($driverApp['bgcheck_file_path'])): ?>
                    <div>
                        <div style="font-size:11px; color:#6b7280; text-transform:uppercase; font-weight:600; margin-bottom:3px;">Document</div>
                        <a href="<?= url('admin/delivery/bgcheck/download?app_id=' . $driverApp['id']) ?>" target="_blank" style="color:#2563eb; font-weight:600;">
                            <i class="fas fa-file-arrow-down"></i> View / Download
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($driverApp['bgcheck_notes'])): ?>
                    <div style="background:#f8fafc; border-radius:8px; padding:12px 14px; font-size:13px; color:#374151; border-left:3px solid <?= $bgStatus === 'flagged' ? '#dc2626' : '#16a34a' ?>;">
                        <strong>Notes:</strong> <?= nl2br(htmlspecialchars($driverApp['bgcheck_notes'])) ?>
                    </div>
                <?php endif; ?>

            <?php elseif ($bgStatus === 'flagged'): ?>
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:16px; margin-bottom:16px; font-size:13px; color:#991b1b;">
                    <i class="fas fa-triangle-exclamation"></i> <strong>Background check flagged.</strong>
                    <?php if (!empty($driverApp['bgcheck_notes'])): ?>
                        <div style="margin-top:6px;"><?= nl2br(htmlspecialchars($driverApp['bgcheck_notes'])) ?></div>
                    <?php endif; ?>
                </div>
                <div style="font-size:13px; color:#6b7280; margin-bottom:12px;">
                    <i class="fas fa-info-circle"></i> Contact the driver and ask them to upload a new document through their <strong>Driver Portal</strong>. You can also waive the requirement below.
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="button" onclick="bgcheckOpenWaive(<?= $driverApp['id'] ?>)" style="background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;">
                        <i class="fas fa-circle-exclamation"></i> Waive (Override)
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Flag / Waive Modals -->
        <div id="bgcheckFlagModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:16px; padding:28px; width:460px; max-width:95vw;">
                <h3 style="margin-bottom:10px; font-size:1.05rem;"><i class="fas fa-shield-exclamation" style="color:#dc2626;"></i> Flag Background Check Issue</h3>
                <p style="font-size:13px; color:#6b7280; margin-bottom:16px;">Describe the issue found with this background check. The driver will not be notified automatically.</p>
                <textarea id="bgcheckFlagNotes" rows="3" placeholder="e.g. Conviction found — disqualifying offence..." style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-family:inherit; font-size:13px; margin-bottom:16px; resize:vertical;"></textarea>
                <input type="hidden" id="bgcheckFlagAppId" value="">
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('bgcheckFlagModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="button" onclick="bgcheckSubmitFlag(this)" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; padding:9px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Flag Issue</button>
                </div>
            </div>
        </div>

        <div id="bgcheckWaiveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:16px; padding:28px; width:460px; max-width:95vw;">
                <h3 style="margin-bottom:10px; font-size:1.05rem;"><i class="fas fa-circle-exclamation" style="color:#d97706;"></i> Waive Background Check</h3>
                <p style="font-size:13px; color:#6b7280; margin-bottom:4px;">This overrides the background check requirement and allows approval. <strong>Reason is required.</strong></p>
                <p style="font-size:12px; color:#dc2626; margin-bottom:16px;">This action is logged and auditable.</p>
                <textarea id="bgcheckWaiveNotes" rows="3" placeholder="Reason for waiving (required)..." style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-family:inherit; font-size:13px; margin-bottom:16px; resize:vertical;"></textarea>
                <input type="hidden" id="bgcheckWaiveAppId" value="">
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('bgcheckWaiveModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="button" onclick="bgcheckSubmitWaive(this)" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; padding:9px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Waive & Allow Approval</button>
                </div>
            </div>
        </div>

        <script>
        (function() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            window.bgcheckRequest = function(appId, btn) {
                const orig = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                const params = new URLSearchParams({ _csrf_token: csrf, application_id: appId });
                fetch('<?= url('admin/delivery/bgcheck/request') ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body: params })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) { alert(data.message); location.reload(); }
                        else { alert(data.error || 'Error'); btn.disabled = false; btn.innerHTML = orig; }
                    }).catch(() => { alert('Request failed.'); btn.disabled = false; btn.innerHTML = orig; });
            };

            window.bgcheckAction = function(appId, action, btn) {
                const orig = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                const params = new URLSearchParams({ _csrf_token: csrf, application_id: appId, action: action });
                fetch('<?= url('admin/delivery/bgcheck/verify') ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body: params })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) { location.reload(); }
                        else { alert(data.error || 'Error'); btn.disabled = false; btn.innerHTML = orig; }
                    }).catch(() => { alert('Request failed.'); btn.disabled = false; btn.innerHTML = orig; });
            };

            window.bgcheckOpenFlag = function(appId) {
                document.getElementById('bgcheckFlagAppId').value = appId;
                document.getElementById('bgcheckFlagNotes').value = '';
                document.getElementById('bgcheckFlagModal').style.display = 'flex';
            };

            window.bgcheckSubmitFlag = function(btn) {
                const notes = document.getElementById('bgcheckFlagNotes').value.trim();
                const appId = document.getElementById('bgcheckFlagAppId').value;
                if (!notes) { alert('Please describe the issue.'); return; }
                btn.disabled = true;
                const params = new URLSearchParams({ _csrf_token: csrf, application_id: appId, action: 'flag', notes: notes });
                fetch('<?= url('admin/delivery/bgcheck/verify') ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body: params })
                    .then(r => r.json())
                    .then(data => { if (data.success) { location.reload(); } else { alert(data.error || 'Error'); btn.disabled = false; } })
                    .catch(() => { alert('Request failed.'); btn.disabled = false; });
            };

            window.bgcheckOpenWaive = function(appId) {
                document.getElementById('bgcheckWaiveAppId').value = appId;
                document.getElementById('bgcheckWaiveNotes').value = '';
                document.getElementById('bgcheckWaiveModal').style.display = 'flex';
            };

            window.bgcheckSubmitWaive = function(btn) {
                const notes = document.getElementById('bgcheckWaiveNotes').value.trim();
                const appId = document.getElementById('bgcheckWaiveAppId').value;
                if (!notes) { alert('A reason is required to waive the background check.'); return; }
                btn.disabled = true;
                const params = new URLSearchParams({ _csrf_token: csrf, application_id: appId, action: 'waive', notes: notes });
                fetch('<?= url('admin/delivery/bgcheck/verify') ?>', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body: params })
                    .then(r => r.json())
                    .then(data => { if (data.success) { location.reload(); } else { alert(data.error || 'Error'); btn.disabled = false; } })
                    .catch(() => { alert('Request failed.'); btn.disabled = false; });
            };
        })();
        </script>
        <?php endif; ?>

        <!-- Compliance Documents -->
        <?php if (!empty($driverApp)): ?>
        <?php
        $cdDefs = [
            'class5_license'        => ['label' => 'Class 5 Driver\'s License',          'icon' => 'id-card'],
            'saaq_record'           => ['label' => 'SAAQ Driving Record',                 'icon' => 'car-side'],
            'commercial_insurance'  => ['label' => 'Proof of Commercial Insurance (COI)', 'icon' => 'file-shield'],
            'vehicle_registration'  => ['label' => 'Vehicle Registration',                'icon' => 'file-contract'],
            'work_authorization'    => ['label' => 'Proof of Work Authorization',         'icon' => 'passport'],
        ];
        $cdColors = [
            'not_uploaded'  => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'icon' => 'circle-minus',       'label' => 'Not Submitted'],
            'not_required'  => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'icon' => 'circle-check',       'label' => 'Not Required'],
            'uploaded'      => ['bg' => '#fef3c7', 'color' => '#b45309', 'icon' => 'clock',              'label' => 'Awaiting Review'],
            'verified'      => ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'shield-check',       'label' => 'Verified'],
            'flagged'       => ['bg' => '#fee2e2', 'color' => '#dc2626', 'icon' => 'shield-exclamation', 'label' => 'Flagged'],
        ];
        $cdDone = 0;
        foreach ($cdDefs as $cdType => $_) {
            $cdS = ($driverComplianceDocs[$cdType]['status'] ?? 'not_uploaded');
            if (in_array($cdS, ['verified', 'not_required'])) $cdDone++;
        }
        ?>
        <div class="card" id="compliance-card">
            <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i class="fas fa-file-shield"></i> Compliance Documents
                <span style="margin-left:auto; font-size:12px; font-weight:600; padding:4px 12px; border-radius:20px;
                    background:<?= $cdDone === 5 ? '#dcfce7' : ($cdDone > 0 ? '#fef3c7' : '#f3f4f6') ?>;
                    color:<?= $cdDone === 5 ? '#16a34a' : ($cdDone > 0 ? '#b45309' : '#6b7280') ?>;">
                    <?= $cdDone ?>/5 complete
                </span>
            </h3>

            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:12px;">
            <?php foreach ($cdDefs as $type => $def):
                $doc    = $driverComplianceDocs[$type] ?? null;
                $status = $doc['status'] ?? 'not_uploaded';
                $sc     = $cdColors[$status] ?? $cdColors['not_uploaded'];
            ?>
            <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;">
                <div style="background:#f9fafb; padding:10px 14px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-<?= $def['icon'] ?>" style="color:#2563eb; width:14px; text-align:center;"></i>
                    <span style="font-size:12px; font-weight:700; color:#111827; flex:1; line-height:1.3;"><?= htmlspecialchars($def['label']) ?></span>
                    <span style="font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; background:<?= $sc['bg'] ?>; color:<?= $sc['color'] ?>; white-space:nowrap;">
                        <i class="fas fa-<?= $sc['icon'] ?>"></i> <?= $sc['label'] ?>
                    </span>
                </div>
                <div style="padding:10px 14px;">
                    <?php if ($doc && $doc['uploaded_at']): ?>
                    <div style="font-size:11px; color:#6b7280; margin-bottom:6px;">
                        Submitted: <?= date('M j, Y', strtotime($doc['uploaded_at'])) ?>
                        <?php if ($doc['doc_date']): ?> &nbsp;·&nbsp; Doc date: <?= date('M j, Y', strtotime($doc['doc_date'])) ?><?php endif; ?>
                        <?php if ($doc['doc_subtype']): ?><br><?= htmlspecialchars($doc['doc_subtype']) ?><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($doc['admin_notes'])): ?>
                    <div style="font-size:11px; background:#fffbeb; border:1px solid #fde68a; border-radius:5px; padding:5px 8px; color:#92400e; margin-bottom:6px;">
                        <?= htmlspecialchars($doc['admin_notes']) ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($doc && !empty($doc['file_path']) && in_array($status, ['uploaded','verified','flagged'])): ?>
                    <a href="<?= url('admin/delivery/compliance/download?doc_id=' . $doc['id']) ?>" target="_blank"
                       style="display:inline-flex; align-items:center; gap:4px; font-size:11px; color:#2563eb; text-decoration:none; font-weight:600; margin-bottom:6px;">
                        <i class="fas fa-eye"></i> View File
                    </a>
                    <?php endif; ?>

                    <?php if ($doc && in_array($status, ['uploaded','flagged','not_required'])): ?>
                    <div style="display:flex; gap:5px; flex-wrap:wrap; margin-top:4px;">
                        <?php if ($status !== 'verified'): ?>
                        <button onclick="cdReview(<?= $doc['id'] ?>, 'verify', '<?= addslashes($def['label']) ?>')"
                                style="padding:4px 10px; background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:inherit;">
                            <i class="fas fa-check"></i> Verify
                        </button>
                        <?php endif; ?>
                        <?php if ($status !== 'flagged'): ?>
                        <button onclick="cdReview(<?= $doc['id'] ?>, 'flag', '<?= addslashes($def['label']) ?>')"
                                style="padding:4px 10px; background:#fee2e2; color:#dc2626; border:1px solid #fecaca; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:inherit;">
                            <i class="fas fa-flag"></i> Flag
                        </button>
                        <?php endif; ?>
                        <?php if ($status !== 'not_required'): ?>
                        <button onclick="cdReview(<?= $doc['id'] ?>, 'waive', '<?= addslashes($def['label']) ?>')"
                                style="padding:4px 10px; background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer; font-family:inherit;">
                            <i class="fas fa-ban"></i> Waive
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php elseif ($status === 'verified'): ?>
                    <div style="font-size:11px; color:#16a34a; font-weight:600;">
                        <i class="fas fa-shield-check"></i> Verified <?= $doc['verified_at'] ? date('M j, Y', strtotime($doc['verified_at'])) : '' ?>
                    </div>
                    <?php else: ?>
                    <div style="font-size:11px; color:#9ca3af; font-style:italic;">Awaiting driver submission</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>

        <!-- Compliance review modal -->
        <div id="cdReviewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:16px; padding:28px; width:440px; max-width:95vw;">
                <h3 style="margin-bottom:8px; font-size:1.05rem;" id="cdmTitle"></h3>
                <p style="font-size:13px; color:#6b7280; margin-bottom:16px;" id="cdmDesc"></p>
                <textarea id="cdmNotes" rows="3" placeholder="Admin notes..." style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-family:inherit; font-size:13px; margin-bottom:16px; resize:vertical;"></textarea>
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('cdReviewModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="button" id="cdmConfirmBtn" onclick="cdSubmitReview()" style="padding:9px 20px; border:none; border-radius:8px; cursor:pointer; font-weight:600; color:#fff;">Confirm</button>
                </div>
            </div>
        </div>
        <script>
        (function(){
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            let _cdId = null, _cdAction = null;
            window.cdReview = function(docId, action, label) {
                _cdId = docId; _cdAction = action;
                const t = {verify:'Verify Document', flag:'Flag Document', waive:'Waive Requirement'};
                const d = {
                    verify: `Mark "${label}" as verified.`,
                    flag:   `Flag "${label}" — driver will be asked to re-upload.`,
                    waive:  `Waive the "${label}" requirement for this driver.`
                };
                const c = {verify:'#16a34a', flag:'#dc2626', waive:'#6b7280'};
                document.getElementById('cdmTitle').textContent = t[action];
                document.getElementById('cdmDesc').textContent  = d[action];
                document.getElementById('cdmConfirmBtn').style.background = c[action];
                document.getElementById('cdmNotes').value = '';
                document.getElementById('cdReviewModal').style.display = 'flex';
            };
            window.cdSubmitReview = function() {
                const notes = document.getElementById('cdmNotes').value.trim();
                if (_cdAction === 'flag' && !notes) { alert('Please enter a reason for flagging.'); return; }
                const btn = document.getElementById('cdmConfirmBtn');
                btn.disabled = true; btn.textContent = 'Saving...';
                const params = new URLSearchParams({ _csrf_token: csrf, doc_id: _cdId, action: _cdAction, notes: notes });
                fetch('<?= url('admin/delivery/compliance/review') ?>', {
                    method:'POST',
                    headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
                    body: params
                }).then(r=>r.json()).then(data=>{
                    if (data.success) { location.reload(); }
                    else { alert(data.error||'Failed'); btn.disabled=false; btn.textContent='Confirm'; }
                }).catch(()=>{ alert('Network error.'); btn.disabled=false; btn.textContent='Confirm'; });
            };
        })();
        </script>
        <?php endif; ?>

        <!-- Driver Training Progress -->
        <?php if (!empty($driverApp) && !empty($driverApp['user_id'])): ?>
        <div class="card">
            <h3 class="card-title">
                <i class="fas fa-graduation-cap"></i> Driver Training
                <?php if (!empty($driverCert)): ?>
                    <span style="margin-left:auto; display:inline-flex; align-items:center; gap:6px; background:#dcfce7; color:#16a34a; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">
                        <i class="fas fa-certificate"></i> Certified — <?= htmlspecialchars($driverCert['cert_number']) ?>
                    </span>
                <?php else: ?>
                    <span style="margin-left:auto;">
                        <form method="POST" action="<?= url('admin/training/driver/certify') ?>" style="display:inline;" onsubmit="return confirm('Manually certify this driver? This bypasses training.')">
                            <?= csrfField() ?>
                            <input type="hidden" name="driver_id" value="<?= (int)$driverApp['user_id'] ?>">
                            <input type="hidden" name="redirect" value="<?= url('admin/leads/view?id=' . $lead['id']) ?>">
                            <button type="submit" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer;">
                                <i class="fas fa-award"></i> Manual Certify
                            </button>
                        </form>
                    </span>
                <?php endif; ?>
            </h3>

            <?php if (empty($driverTrainingProgress)): ?>
                <div style="text-align:center; color:#9ca3af; font-size:13px; padding:20px 0;">
                    <i class="fas fa-book-open" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                    No training modules started yet.
                </div>
            <?php else:
                $passedCount = 0;
                foreach ($driverTrainingProgress as $tp) {
                    if (($tp['status'] ?? '') === 'passed') $passedCount++;
                }
            ?>
                <?php $totalModuleCount = count($driverTrainingProgress); ?>
                <div style="margin-bottom:16px; display:flex; align-items:center; gap:12px;">
                    <div style="flex:1; background:#f3f4f6; border-radius:99px; height:8px;">
                        <div style="width:<?= $totalModuleCount > 0 ? round(($passedCount / $totalModuleCount) * 100) : 0 ?>%; background:#16a34a; height:100%; border-radius:99px; transition:width .3s;"></div>
                    </div>
                    <span style="font-size:13px; font-weight:600; color:#374151;"><?= $passedCount ?>/<?= $totalModuleCount ?> modules passed</span>
                </div>
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:8px 12px; text-align:left; font-weight:600; color:#6b7280; font-size:11px; text-transform:uppercase;">#</th>
                            <th style="padding:8px 12px; text-align:left; font-weight:600; color:#6b7280; font-size:11px; text-transform:uppercase;">Module</th>
                            <th style="padding:8px 12px; text-align:center; font-weight:600; color:#6b7280; font-size:11px; text-transform:uppercase;">Status</th>
                            <th style="padding:8px 12px; text-align:center; font-weight:600; color:#6b7280; font-size:11px; text-transform:uppercase;">Score</th>
                            <th style="padding:8px 12px; text-align:center; font-weight:600; color:#6b7280; font-size:11px; text-transform:uppercase;">Attempts</th>
                            <th style="padding:8px 12px; text-align:center; font-weight:600; color:#6b7280; font-size:11px; text-transform:uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($driverTrainingProgress as $tp):
                            $status = $tp['status'] ?? 'locked';
                            $attempts = (int)($tp['attempts'] ?? 0);
                            $maxAttempts = (int)($tp['max_attempts'] ?? 3);
                            $bestScore = (int)($tp['best_score'] ?? 0);
                            $statusColors = [
                                'locked'    => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => 'Locked'],
                                'available' => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'label' => 'In Progress'],
                                'passed'    => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => 'Passed'],
                                'failed'    => ['bg' => '#fee2e2', 'color' => '#dc2626', 'label' => 'Failed'],
                            ];
                            $sc = $statusColors[$status] ?? $statusColors['locked'];
                        ?>
                            <tr style="border-top:1px solid #f3f4f6;">
                                <td style="padding:10px 12px; color:#9ca3af;"><?= (int)$tp['order_num'] ?></td>
                                <td style="padding:10px 12px; font-weight:500; color:#111827;"><?= htmlspecialchars($tp['title']) ?></td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; background:<?= $sc['bg'] ?>; color:<?= $sc['color'] ?>;">
                                        <?php if ($status === 'passed'): ?><i class="fas fa-check"></i><?php elseif ($status === 'failed'): ?><i class="fas fa-times"></i><?php elseif ($status === 'locked'): ?><i class="fas fa-lock"></i><?php else: ?><i class="fas fa-play"></i><?php endif; ?>
                                        <?= $sc['label'] ?>
                                    </span>
                                </td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <?php if ($status !== 'locked' && $attempts > 0): ?>
                                        <span style="font-weight:600; color:<?= $bestScore >= 80 ? '#16a34a' : '#dc2626' ?>;"><?= $bestScore ?>%</span>
                                    <?php else: ?>
                                        <span style="color:#d1d5db;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px; text-align:center; color:#6b7280;">
                                    <?php if ($status !== 'locked'): ?>
                                        <?= $attempts ?>/<?= $maxAttempts ?>
                                        <?php if ($attempts >= $maxAttempts && $status === 'failed'): ?>
                                            <span style="color:#dc2626; font-size:11px; display:block;">Max reached</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color:#d1d5db;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <?php if ($status !== 'locked'): ?>
                                        <form method="POST" action="<?= url('admin/training/driver/reset-module') ?>" style="display:inline;" onsubmit="return confirm('Reset this module for the driver?')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="driver_id" value="<?= (int)$driverApp['user_id'] ?>">
                                            <input type="hidden" name="module_id" value="<?= (int)$tp['tm_id'] ?>">
                                            <input type="hidden" name="redirect" value="<?= url('admin/leads/view?id=' . $lead['id']) ?>">
                                            <button type="submit" style="background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; padding:4px 10px; border-radius:6px; font-size:11px; cursor:pointer;">
                                                <i class="fas fa-redo"></i> Reset
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#e5e7eb; font-size:11px;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Driver Pipeline Modals -->
        <div id="driverInterviewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:16px; padding:28px; width:500px; max-width:95vw; max-height:90vh; overflow-y:auto;">
                <h3 style="margin-bottom:8px; font-size:1.05rem;">Request Interview</h3>
                <p style="font-size:13px; color:#6b7280; margin-bottom:16px;">Add one or more time slots — the applicant will choose one.</p>
                <div id="driverSlots" style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;"></div>
                <button type="button" onclick="driverAddSlot()" style="font-size:13px; color:#3b82f6; background:none; border:1px dashed #93c5fd; border-radius:8px; padding:6px 14px; cursor:pointer; margin-bottom:20px;">+ Add time slot</button>
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('driverInterviewModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="button" onclick="driverSubmitInterview(this)" class="btn btn-primary">Send Interview Request</button>
                </div>
            </div>
        </div>

        <div id="driverApproveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:16px; padding:28px; width:420px; max-width:95vw;">
                <h3 style="margin-bottom:10px; font-size:1.05rem;"><i class="fas fa-check-circle" style="color:#16a34a;"></i> Approve Driver</h3>
                <p style="font-size:13px; color:#6b7280; margin-bottom:20px;">This activates the applicant's account and sends them a congratulations email.</p>
                <input type="hidden" id="driverApproveAppId" value="">
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('driverApproveModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="button" onclick="driverSubmitApprove(this)" style="background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; padding:9px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Approve</button>
                </div>
            </div>
        </div>

        <div id="driverRejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:16px; padding:28px; width:420px; max-width:95vw;">
                <h3 style="margin-bottom:10px; font-size:1.05rem;"><i class="fas fa-times-circle" style="color:#dc2626;"></i> Reject Application</h3>
                <textarea id="driverRejectReason" rows="3" placeholder="Reason for rejection (optional — sent to applicant)..." style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-family:inherit; font-size:13px; margin-bottom:16px; resize:vertical;"></textarea>
                <input type="hidden" id="driverRejectAppId" value="">
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('driverRejectModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                    <button type="button" onclick="driverSubmitReject(this)" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; padding:9px 20px; border-radius:8px; cursor:pointer; font-weight:600;">Reject</button>
                </div>
            </div>
        </div>

        <script>
        (function() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            function apiCall(url, body, onSuccess, btn) {
                const origHTML = btn ? btn.innerHTML : null;
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
                const params = new URLSearchParams();
                for (const [key, value] of Object.entries(body)) {
                    if (Array.isArray(value)) {
                        value.forEach(v => params.append(key + '[]', v));
                    } else {
                        params.append(key, value);
                    }
                }
                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrf },
                    body: params.toString()
                }).then(r => r.json()).then(d => {
                    if (d.success) {
                        if (btn) { btn.disabled = false; btn.innerHTML = origHTML; }
                        onSuccess(d);
                    } else {
                        alert(d.error || 'Something went wrong.');
                        if (btn) { btn.disabled = false; btn.innerHTML = origHTML; }
                    }
                }).catch(() => {
                    alert('Network error.');
                    if (btn) { btn.disabled = false; btn.innerHTML = origHTML; }
                });
            }

            window.driverMarkUnderReview = function(appId, btn) {
                if (!confirm('Mark this application as Under Review?')) return;
                apiCall('<?= url('admin/delivery/pipeline/under-review') ?>', { application_id: appId }, () => location.reload(), btn);
            };

            window.driverOpenInterviewModal = function(appId) {
                const modal = document.getElementById('driverInterviewModal');
                modal.style.display = 'flex';
                modal.dataset.appId = appId;
                document.getElementById('driverSlots').innerHTML = '';
                driverAddSlot();
            };

            window.driverAddSlot = function() {
                const row = document.createElement('div');
                row.style.cssText = 'display:flex; align-items:center; gap:8px;';
                row.innerHTML = '<input type="datetime-local" style="flex:1; padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; font-size:13px;">' +
                    '<button type="button" onclick="this.parentElement.remove()" style="width:32px; height:32px; border:1px solid #fecaca; border-radius:6px; background:#fee2e2; color:#dc2626; cursor:pointer; font-size:16px; flex-shrink:0;">×</button>';
                document.getElementById('driverSlots').appendChild(row);
            };

            window.driverSubmitInterview = function(btn) {
                const appId = document.getElementById('driverInterviewModal').dataset.appId;
                const times = Array.from(document.querySelectorAll('#driverSlots input[type="datetime-local"]'))
                    .map(i => i.value).filter(v => v);
                if (!times.length) { alert('Add at least one time slot.'); return; }
                apiCall('<?= url('admin/delivery/pipeline/request-interview') ?>', { application_id: appId, proposed_times: times }, () => location.reload(), btn);
            };

            window.driverOpenApproveModal = function(appId) {
                document.getElementById('driverApproveAppId').value = appId;
                document.getElementById('driverApproveModal').style.display = 'flex';
            };

            window.driverSubmitApprove = function(btn) {
                const appId = document.getElementById('driverApproveAppId').value;
                apiCall('<?= url('admin/delivery/pipeline/approve') ?>', { application_id: appId, use_existing_account: '1' }, () => location.reload(), btn);
            };

            window.driverOpenRejectModal = function(appId) {
                document.getElementById('driverRejectAppId').value = appId;
                document.getElementById('driverRejectReason').value = '';
                document.getElementById('driverRejectModal').style.display = 'flex';
            };

            window.driverSubmitReject = function(btn) {
                const appId = document.getElementById('driverRejectAppId').value;
                const reason = document.getElementById('driverRejectReason').value;
                apiCall('<?= url('admin/delivery/pipeline/reject') ?>', { application_id: appId, reason: reason }, () => location.reload(), btn);
            };

            window.driverSendMsg = function(appId, btn) {
                const input = document.getElementById('driver-msg-input');
                const msg = input.value.trim();
                if (!msg) return;
                apiCall('<?= url('admin/delivery/pipeline/send-message') ?>', { application_id: appId, message: msg }, function() {
                    input.value = '';
                    const box = document.getElementById('driver-messages-box');
                    // Remove "no messages" placeholder if present
                    const placeholder = box.querySelector('div[style*="text-align:center"]');
                    if (placeholder) placeholder.remove();
                    const now = new Date();
                    const timeStr = now.toLocaleString('en-US', { month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true });
                    const wrap = document.createElement('div');
                    wrap.style.cssText = 'display:flex; flex-direction:column; align-items:flex-end;';
                    wrap.innerHTML = '<div style="max-width:75%; background:#1d4ed8; color:#fff; padding:9px 13px; border-radius:10px; font-size:13px; line-height:1.5;">' +
                        msg.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/\n/g,'<br>') +
                        '</div><div style="font-size:11px; color:#9ca3af; margin-top:3px;">You (Admin) · ' + timeStr + '</div>';
                    box.appendChild(wrap);
                    box.scrollTop = box.scrollHeight;
                }, btn);
            };
        })();
        </script>
        <?php endif; ?>

        <!-- Activity Timeline -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-history"></i> Activity Timeline</h3>

            <!-- Add Activity Form -->
            <form method="POST" action="<?= url('admin/leads/add-activity') ?>" class="activity-form">
                <?= csrfField() ?>
                <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">

                <div class="form-row">
                    <select name="activity_type" required style="min-width: 140px;">
                        <option value="note">Note</option>
                        <option value="call">Call</option>
                        <option value="email">Email</option>
                        <option value="meeting">Meeting</option>
                        <option value="follow_up">Follow-up</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="date" name="next_follow_up" placeholder="Next follow-up" style="flex: 1;">
                </div>
                <textarea name="description" placeholder="What happened?" required></textarea>
                <div style="display: flex; justify-content: flex-end; margin-top: 12px;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Activity
                    </button>
                </div>
            </form>

            <?php if (empty($activities)): ?>
                <p style="color: #999; text-align: center; padding: 20px;">No activities recorded yet.</p>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($activities as $activity): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= $activity['activity_type'] ?>"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <span class="timeline-type"><?= ucwords(str_replace('_', ' ', $activity['activity_type'])) ?></span>
                                    <span class="timeline-date"><?= date('M j, Y g:i A', strtotime($activity['created_at'])) ?></span>
                                </div>
                                <div class="timeline-description"><?= nl2br(htmlspecialchars($activity['description'])) ?></div>
                                <?php if ($activity['created_by_name']): ?>
                                    <div class="timeline-by">by <?= htmlspecialchars($activity['created_by_name']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-column">
        <!-- Quick Actions / Status -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-bolt"></i> <?= $t['status_actions'] ?></h3>

            <!-- Quick Status Change -->
            <form method="POST" action="<?= url('admin/leads/update-status') ?>" style="margin-bottom: 16px;">
                <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                <div style="display: flex; gap: 8px;">
                    <select name="status" class="status-select" style="flex: 1;">
                        <?php if ($lead['interest_type'] === 'driver'): ?>
                            <option value="new"          <?= $lead['status'] === 'new'          ? 'selected' : '' ?>>New</option>
                            <option value="contacted"    <?= $lead['status'] === 'contacted'    ? 'selected' : '' ?>>Contacted</option>
                            <option value="qualified"    <?= $lead['status'] === 'qualified'    ? 'selected' : '' ?>>Under Review</option>
                            <option value="converted"    <?= $lead['status'] === 'converted'    ? 'selected' : '' ?>>Approved</option>
                            <option value="lost"         <?= $lead['status'] === 'lost'         ? 'selected' : '' ?>>Rejected</option>
                        <?php else: ?>
                            <option value="new"         <?= $lead['status'] === 'new'         ? 'selected' : '' ?>>New</option>
                            <option value="contacted"   <?= $lead['status'] === 'contacted'   ? 'selected' : '' ?>>Contacted</option>
                            <option value="qualified"   <?= $lead['status'] === 'qualified'   ? 'selected' : '' ?>>Qualified</option>
                            <option value="proposal"    <?= $lead['status'] === 'proposal'    ? 'selected' : '' ?>>Proposal</option>
                            <option value="negotiation" <?= $lead['status'] === 'negotiation' ? 'selected' : '' ?>>Negotiation</option>
                            <option value="won"         <?= $lead['status'] === 'won'         ? 'selected' : '' ?>>Won</option>
                            <option value="lost"        <?= $lead['status'] === 'lost'        ? 'selected' : '' ?>>Lost</option>
                        <?php endif; ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm"><?= $t['update'] ?></button>
                </div>
            </form>
        </div>

        <!-- Follow-up -->
        <div class="card">
            <h3 class="card-title"><i class="fas fa-calendar"></i> <?= $t['follow_up'] ?></h3>
            <div class="info-item">
                <div class="info-label"><?= $t['next_follow_up'] ?></div>
                <div class="info-value">
                    <?php if ($lead['next_follow_up']): ?>
                        <?php
                        $followUp = strtotime($lead['next_follow_up']);
                        $today = strtotime('today');
                        $style = '';
                        if ($followUp < $today) $style = 'color: #dc2626; font-weight: 600;';
                        elseif ($followUp == $today) $style = 'color: #d97706; font-weight: 600;';
                        ?>
                        <span style="<?= $style ?>"><?= date('F j, Y', $followUp) ?></span>
                        <?php if ($followUp < $today): ?>
                            <span style="color: #dc2626; font-size: 12px;">(Overdue)</span>
                        <?php elseif ($followUp == $today): ?>
                            <span style="color: #d97706; font-size: 12px;">(Today)</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color: #999;">Not set</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-item" style="margin-top: 12px;">
                <div class="info-label">Last Contacted</div>
                <div class="info-value">
                    <?= $lead['last_contacted_at'] ? date('M j, Y g:i A', strtotime($lead['last_contacted_at'])) : '<span style="color: #999;">Never</span>' ?>
                </div>
            </div>
            <?php if ($lead['converted_at']): ?>
                <div class="info-item" style="margin-top: 12px;">
                    <div class="info-label">Converted</div>
                    <div class="info-value" style="color: #16a34a;">
                        <?= date('M j, Y', strtotime($lead['converted_at'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($lead['interest_type'] !== 'driver'): ?>
        <!-- Communication Center (Twilio) -->
        <div class="card" id="comm-center">
            <h3 class="card-title"><i class="fas fa-headset"></i> <?= $t['comm_center'] ?></h3>

            <?php
            $twilioConfigured = !empty($_ENV['TWILIO_ACCOUNT_SID']) && !empty($_ENV['TWILIO_AUTH_TOKEN']);
            $hasPhone = !empty($lead['phone']);
            $smsOptOut = !empty($lead['sms_opt_out']);
            ?>

            <?php if (!$twilioConfigured): ?>
                <div class="twilio-not-configured">
                    <i class="fas fa-plug"></i>
                    <?= $t['twilio_not_configured'] ?><br>
                    <small><?= $t['add_credentials'] ?></small>
                </div>
                <div class="quick-actions" style="margin-top: 16px;">
                    <?php if ($lead['email']): ?>
                        <a href="mailto:<?= htmlspecialchars($lead['email']) ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                    <?php endif; ?>
                    <?php if ($hasPhone): ?>
                        <a href="tel:<?= htmlspecialchars($lead['phone']) ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-phone"></i> Call
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>

                <?php if ($smsOptOut): ?>
                    <div class="opt-out-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $t['opt_out_warning'] ?>
                    </div>
                <?php endif; ?>

                <!-- Communication Stats -->
                <div class="comm-stats">
                    <div class="comm-stat">
                        <div class="comm-stat-value"><?= $lead['total_calls'] ?? 0 ?></div>
                        <div class="comm-stat-label"><?= $t['calls'] ?></div>
                    </div>
                    <div class="comm-stat">
                        <div class="comm-stat-value"><?= $lead['total_sms'] ?? 0 ?></div>
                        <div class="comm-stat-label"><?= $t['sms'] ?></div>
                    </div>
                </div>

                <!-- Call Status Display -->
                <div class="call-status" id="call-status">
                    <div class="status-icon"><i class="fas fa-phone-volume"></i></div>
                    <div class="status-text" id="call-status-text">Calling...</div>
                </div>

                <!-- Call & SMS Buttons -->
                <?php if ($hasPhone): ?>
                    <div class="comm-actions">
                        <button type="button" class="comm-btn call-btn" id="btn-call" onclick="initiateCall(<?= $lead['id'] ?>)">
                            <i class="fas fa-phone"></i> Call
                        </button>
                        <button type="button" class="comm-btn sms-btn" id="btn-sms" onclick="toggleSMSForm()" <?= $smsOptOut ? 'disabled' : '' ?>>
                            <i class="fas fa-comment-dots"></i> SMS
                        </button>
                    </div>

                    <!-- SMS Form -->
                    <div class="sms-form" id="sms-form">
                        <div class="sms-templates" id="sms-templates">
                            <span style="font-size: 12px; color: #666; width: 100%; margin-bottom: 4px;">Quick templates:</span>
                            <!-- Templates loaded via JS -->
                        </div>
                        <textarea class="sms-textarea" id="sms-message" placeholder="Type your message..." maxlength="480" oninput="updateCharCount()"></textarea>
                        <div class="char-count" id="char-count">0 / 160 characters</div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSMSForm()" style="flex: 1;">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="btn-send-sms" onclick="sendSMS(<?= $lead['id'] ?>)" style="flex: 2;">
                                <i class="fas fa-paper-plane"></i> Send SMS
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="color: #999; font-size: 13px; text-align: center; padding: 16px 0;">
                        <i class="fas fa-phone-slash"></i><br>
                        No phone number on file.
                    </p>
                <?php endif; ?>

                <!-- Quick Email -->
                <?php if ($lead['email']): ?>
                    <div style="margin-top: 12px;">
                        <a href="mailto:<?= htmlspecialchars($lead['email']) ?>" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                            <i class="fas fa-envelope"></i> <?= $t['send_email'] ?>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Recent Communications -->
                <div class="comm-history" id="comm-history">
                    <div class="comm-history-title">
                        <i class="fas fa-history"></i> <?= $t['recent_comms'] ?>
                    </div>
                    <div id="comm-history-list">
                        <p style="color: #999; font-size: 12px; text-align: center;">Loading...</p>
                    </div>
                </div>

            <?php endif; ?>

            <!-- Supplier Messages Thread (always shown, regardless of Twilio) -->
            <?php if (!empty($linkedSupplier['id'])): ?>
                <?php
                    $supplierId     = (int) $linkedSupplier['id'];
                    $supplierName   = $linkedSupplier['company_name'] ?? ($linkedSupplier['first_name'] . ' ' . $linkedSupplier['last_name']);
                    try {
                        $threadMessages = \App\Controllers\SupplierMessagesController::getMessages($supplierId, 50);
                        require dirname(dirname(__DIR__)) . '/admin/suppliers/messages-thread.php';
                    } catch (\Throwable $e) {
                        // table may not exist yet
                    }
                ?>
            <?php else: ?>
                <div style="margin-top:20px;padding:14px 16px;background:#f9fafb;border:1px dashed #d1d5db;border-radius:8px;font-size:13px;color:#9ca3af;text-align:center;">
                    <i class="fas fa-comments" style="margin-right:6px;"></i>
                    No supplier account linked to this lead yet. Messages will appear here once a supplier account is created.
                </div>
            <?php endif; ?>
        </div>
        <?php endif; // end non-driver comm center ?>

        <!-- Delete -->
        <div class="card">
            <h3 class="card-title" style="color: #dc2626;"><i class="fas fa-trash"></i> Danger Zone</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 16px;">
                <?= $t['delete_warning'] ?>
            </p>
            <form method="POST" action="<?= url('admin/leads/delete') ?>" onsubmit="return confirm('<?= $currentLang === 'fr' ? 'Êtes-vous sûr de vouloir supprimer ce prospect ? Cette action est irréversible.' : 'Are you sure you want to delete this lead? This cannot be undone.' ?>');">
                <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    <i class="fas fa-trash"></i> <?= $t['delete_lead'] ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($supplierApp) && in_array($supplierApp['status'], ['pending', 'under_review', 'info_requested'])): ?>
<!-- Approve Modal -->
<div id="approveModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
        <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1a1a1a;">
                <i class="fas fa-check-circle" style="color: #00b207;"></i> Approve Supplier Application
            </h3>
        </div>
        <form method="POST" action="<?= url('admin/leads/approve-supplier') ?>">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
            <input type="hidden" name="application_id" value="<?= $supplierApp['id'] ?>">
            <div style="padding: 24px;">
                <div style="background: #f0fdf4; border-radius: 8px; padding: 16px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                    <p style="margin: 0 0 8px; font-size: 14px; color: #166534; font-weight: 600;">This will:</p>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #15803d; line-height: 1.8;">
                        <li>Create a supplier account for <strong><?= htmlspecialchars($supplierApp['business_name'] ?: $supplierApp['legal_name']) ?></strong></li>
                        <li>Generate login credentials</li>
                        <li>Send a welcome email to <strong><?= htmlspecialchars($supplierApp['email']) ?></strong></li>
                        <li>Mark this lead as "Won"</li>
                    </ul>
                </div>
                <div style="margin-bottom: 0;">
                    <label style="font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px;">Admin Notes (optional)</label>
                    <textarea name="admin_notes" rows="2" placeholder="Any internal notes about this approval..." style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical;"></textarea>
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('approveModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Approve &amp; Create Account</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
        <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1a1a1a;">
                <i class="fas fa-times-circle" style="color: #dc2626;"></i> Reject Supplier Application
            </h3>
        </div>
        <form method="POST" action="<?= url('admin/leads/reject-supplier') ?>">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
            <input type="hidden" name="application_id" value="<?= $supplierApp['id'] ?>">
            <div style="padding: 24px;">
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px;">Rejection Reason *</label>
                    <select name="rejection_reason" required style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-family: inherit;" onchange="if(this.value==='other'){document.getElementById('rejectNotesField').required=true;document.getElementById('rejectNotesField').placeholder='Please specify the reason...';}else{document.getElementById('rejectNotesField').required=false;document.getElementById('rejectNotesField').placeholder='Additional details (optional)...';}">
                        <option value="">Select a reason...</option>
                        <option value="Incomplete documentation">Incomplete documentation</option>
                        <option value="Business not eligible for our marketplace">Business not eligible</option>
                        <option value="Unable to verify business registration">Unable to verify registration</option>
                        <option value="Duplicate application">Duplicate application</option>
                        <option value="Product category not currently accepted">Product category not accepted</option>
                        <option value="other">Other (specify below)</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px;">Additional Notes</label>
                    <textarea id="rejectNotesField" name="admin_notes" rows="3" placeholder="Additional details (optional)..." style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical;"></textarea>
                </div>
                <div style="margin-top: 12px; padding: 10px 14px; background: #fef3c7; border-radius: 6px; font-size: 12px; color: #92400e;">
                    <i class="fas fa-info-circle"></i> A rejection email will be sent to the applicant with the reason above.
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('rejectModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Reject Application</button>
            </div>
        </form>
    </div>
</div>

<!-- Request Info Modal -->
<?php
$infoDocList = [
    'doc_certificate_incorporation' => 'Certificate of Incorporation',
    'doc_declaration_registration' => 'Declaration of Registration',
    'doc_enterprise_register' => 'Enterprise Register File Search',
];
$missingDocs = [];
foreach ($infoDocList as $docField => $docLabel) {
    $docSt = $supplierApp[$docField . '_status'] ?? 'pending';
    if (empty($supplierApp[$docField]) || $docSt === 'rejected') {
        $missingDocs[$docField] = [
            'label' => $docLabel,
            'rejected' => !empty($supplierApp[$docField]) && $docSt === 'rejected',
        ];
    }
}
?>
<div id="infoModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; max-width: 520px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
        <div style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #1a1a1a;">
                <i class="fas fa-question-circle" style="color: #2563eb;"></i> Request More Information
            </h3>
        </div>
        <form method="POST" action="<?= url('admin/leads/request-supplier-info') ?>">
            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
            <input type="hidden" name="application_id" value="<?= $supplierApp['id'] ?>">
            <div style="padding: 24px;">
                <?php if (!empty($missingDocs)): ?>
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 8px;">
                        <i class="fas fa-file-circle-exclamation" style="color: #dc2626; margin-right: 4px;"></i>
                        Missing / Rejected Documents
                    </label>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; display: flex; flex-direction: column; gap: 10px;">
                        <?php foreach ($missingDocs as $docField => $docInfo): ?>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #991b1b;">
                            <input type="checkbox" name="missing_docs[]" value="<?= htmlspecialchars($docField) ?>" checked style="width: 16px; height: 16px; accent-color: #dc2626; cursor: pointer; flex-shrink: 0;">
                            <span style="flex: 1;">
                                <?= htmlspecialchars($docInfo['label']) ?>
                                <?php if ($docInfo['rejected']): ?>
                                    <span style="font-size: 11px; background: #fee2e2; color: #dc2626; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">Rejected</span>
                                <?php else: ?>
                                    <span style="font-size: 11px; background: #fef3c7; color: #92400e; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">Not uploaded</span>
                                <?php endif; ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p style="font-size: 11px; color: #6b7280; margin: 6px 0 0; line-height: 1.4;">
                        <i class="fas fa-info-circle"></i> Checked documents will be listed in the email sent to the supplier.
                    </p>
                </div>
                <?php endif; ?>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px;">Additional Notes</label>
                    <textarea name="info_message" rows="3" placeholder="Any additional details or instructions for the supplier..." style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div style="padding: 10px 14px; background: #eff6ff; border-radius: 6px; font-size: 12px; color: #1e40af;">
                    <i class="fas fa-envelope"></i> An email will be sent to <strong><?= htmlspecialchars($supplierApp['email']) ?></strong> requesting this information. The application status will be set to "Info Requested".
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('infoModal').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn" style="background: #2563eb; color: white;"><i class="fas fa-paper-plane"></i> Send Request</button>
            </div>
        </form>
    </div>
</div>

<script>
// Close modals on outside click
['approveModal', 'rejectModal', 'infoModal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
// Close modals on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['approveModal', 'rejectModal', 'infoModal'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
    }
});
</script>
<?php endif; ?>

<script>
// Document review status
async function setDocStatus(docField, status, appId) {
    try {
        const formData = new FormData();
        formData.append('application_id', appId);
        formData.append('doc_field', docField);
        formData.append('status', status);
        formData.append('<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>', '<?= generateCsrfToken() ?>');

        const res = await fetch('<?= url('admin/leads/update-doc-status') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating document status');
        }
    } catch (err) {
        alert('Error updating document status');
    }
}
</script>

<?php if ($twilioConfigured): ?>
<script>
// Twilio Communication Functions
const API_URL = '<?= url('api/twilio') ?>';
const LEAD_ID = <?= $lead['id'] ?>;
const LEAD_NAME = '<?= htmlspecialchars($lead['first_name']) ?>';
let smsTemplates = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSMSTemplates();
    loadCommunicationHistory();
    if (window.location.hash === '#messages') {
        const section = document.getElementById('message-thread-section');
        if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

// Load SMS Templates
async function loadSMSTemplates() {
    try {
        const response = await fetch(`${API_URL}/sms-templates`);
        const data = await response.json();

        if (data.success && data.templates) {
            smsTemplates = data.templates;
            renderTemplateButtons();
        }
    } catch (error) {
        console.error('Failed to load SMS templates:', error);
    }
}

// Render template buttons
function renderTemplateButtons() {
    const container = document.getElementById('sms-templates');
    if (!container || !smsTemplates.length) return;

    const lang = document.documentElement.lang || 'en';

    let html = '<span style="font-size: 12px; color: #666; width: 100%; margin-bottom: 4px;">Quick templates:</span>';

    smsTemplates.slice(0, 5).forEach((t, i) => {
        const name = (lang === 'fr' && t.name_fr) ? t.name_fr : t.name;
        html += `<button type="button" class="sms-template-btn" onclick="useTemplate(${i})">${name}</button>`;
    });

    container.innerHTML = html;
}

// Use a template
function useTemplate(index) {
    const template = smsTemplates[index];
    if (!template) return;

    const lang = document.documentElement.lang || 'en';
    let body = (lang === 'fr' && template.body_fr) ? template.body_fr : template.body;

    // Replace placeholders
    body = body.replace('{name}', LEAD_NAME);
    body = body.replace('{first_name}', LEAD_NAME);
    body = body.replace('{sender}', '<?= htmlspecialchars($_SESSION['user_name'] ?? 'OCSAPP') ?>');

    document.getElementById('sms-message').value = body;
    updateCharCount();

    // Highlight active button
    document.querySelectorAll('.sms-template-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i === index);
    });
}

// Toggle SMS form visibility
function toggleSMSForm() {
    const form = document.getElementById('sms-form');
    form.classList.toggle('active');

    if (form.classList.contains('active')) {
        document.getElementById('sms-message').focus();
    }
}

// Update character count
function updateCharCount() {
    const textarea = document.getElementById('sms-message');
    const counter = document.getElementById('char-count');
    const length = textarea.value.length;

    counter.textContent = `${length} / 160 characters`;
    counter.className = 'char-count';

    if (length > 160 && length <= 320) {
        counter.textContent = `${length} / 320 (2 messages)`;
        counter.classList.add('warning');
    } else if (length > 320) {
        counter.textContent = `${length} / 480 (3 messages)`;
        counter.classList.add('over');
    }
}

// Send SMS
async function sendSMS(leadId) {
    const message = document.getElementById('sms-message').value.trim();

    if (!message) {
        alert('Please enter a message');
        return;
    }

    const btn = document.getElementById('btn-send-sms');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
        const formData = new FormData();
        formData.append('lead_id', leadId);
        formData.append('message', message);

        const response = await fetch(`${API_URL}/send-sms`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Clear form and close
            document.getElementById('sms-message').value = '';
            toggleSMSForm();
            updateCharCount();

            // Refresh history
            loadCommunicationHistory();

            // Update stats
            const smsStatEl = document.querySelector('.comm-stat-value');
            if (smsStatEl) {
                const callStat = document.querySelectorAll('.comm-stat-value')[1];
                if (callStat) callStat.textContent = parseInt(callStat.textContent) + 1;
            }

            showNotification('SMS sent successfully!', 'success');
        } else {
            showNotification(data.error || 'Failed to send SMS', 'error');
        }
    } catch (error) {
        console.error('Send SMS error:', error);
        showNotification('Failed to send SMS', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send SMS';
    }
}

// Initiate Call
async function initiateCall(leadId) {
    const btn = document.getElementById('btn-call');
    const statusDiv = document.getElementById('call-status');
    const statusText = document.getElementById('call-status-text');

    btn.disabled = true;
    statusDiv.classList.add('active');
    statusText.textContent = 'Initiating call...';

    try {
        const formData = new FormData();
        formData.append('lead_id', leadId);

        const response = await fetch(`${API_URL}/make-call`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            statusText.textContent = 'Call in progress...';

            // Poll for status updates
            if (data.communication_id) {
                pollCallStatus(data.communication_id);
            }

            // Update stats
            const callStatEl = document.querySelectorAll('.comm-stat-value')[0];
            if (callStatEl) callStatEl.textContent = parseInt(callStatEl.textContent) + 1;

        } else {
            statusText.textContent = data.error || 'Call failed';
            setTimeout(() => {
                statusDiv.classList.remove('active');
            }, 3000);
        }
    } catch (error) {
        console.error('Call error:', error);
        statusText.textContent = 'Call failed - network error';
        setTimeout(() => {
            statusDiv.classList.remove('active');
        }, 3000);
    } finally {
        btn.disabled = false;
    }
}

// Poll call status
function pollCallStatus(commId) {
    // Simple timeout to hide status after 30 seconds
    setTimeout(() => {
        document.getElementById('call-status').classList.remove('active');
        loadCommunicationHistory();
    }, 30000);
}

// Load communication history
async function loadCommunicationHistory() {
    const container = document.getElementById('comm-history-list');
    if (!container) return;

    try {
        const response = await fetch(`${API_URL}/communications?lead_id=${LEAD_ID}`);
        const data = await response.json();

        if (data.success && data.communications) {
            renderCommunicationHistory(data.communications);
        }
    } catch (error) {
        console.error('Failed to load communication history:', error);
        container.innerHTML = '<p style="color: #999; font-size: 12px;">Failed to load history</p>';
    }
}

// Render communication history
function renderCommunicationHistory(communications) {
    const container = document.getElementById('comm-history-list');

    if (!communications.length) {
        container.innerHTML = '<p style="color: #999; font-size: 12px; text-align: center;">No communications yet</p>';
        return;
    }

    let html = '';

    communications.slice(0, 5).forEach(comm => {
        const isCall = comm.type === 'call';
        const isInbound = comm.direction === 'inbound';
        const icon = isCall ? 'phone' : 'comment';
        const iconClass = isInbound ? 'inbound' : comm.type;

        const time = new Date(comm.created_at).toLocaleString('en-US', {
            month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit'
        });

        let content = '';
        if (isCall) {
            const duration = comm.duration ? `${Math.floor(comm.duration / 60)}:${String(comm.duration % 60).padStart(2, '0')}` : '';
            content = comm.status === 'completed' ?
                `<span class="comm-duration">${duration}</span>` :
                `<span style="color: #999;">${comm.status}</span>`;
        } else {
            content = comm.content ? comm.content.substring(0, 50) + (comm.content.length > 50 ? '...' : '') : '';
        }

        html += `
            <div class="comm-item">
                <div class="comm-icon ${iconClass}">
                    <i class="fas fa-${icon}${isInbound ? '-volume' : ''}"></i>
                </div>
                <div class="comm-details">
                    <div class="comm-meta">
                        <span class="comm-type">${isInbound ? 'Inbound' : 'Outbound'} ${isCall ? 'Call' : 'SMS'}</span>
                        <span class="comm-time">${time}</span>
                    </div>
                    <div class="comm-content">${content}</div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Show notification
function showNotification(message, type = 'info') {
    const existing = document.querySelector('.notification-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `notification-toast ${type}`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 14px 20px;
        background: ${type === 'success' ? '#d1fae5' : '#fee2e2'};
        color: ${type === 'success' ? '#065f46' : '#991b1b'};
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
