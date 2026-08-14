<?php
$currentPage = 'business-accounts';
$pageTitle = 'View Business Account';
ob_start();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.page-header h1 {
    display: flex;
    align-items: center;
    gap: 12px;
}

.back-link {
    color: #666;
    text-decoration: none;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}

.back-link:hover {
    color: #00b207;
}

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

.card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 24px;
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
}

.card-title i {
    color: #00b207;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.info-group {
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
    font-size: 15px;
    color: #1a1a1a;
    font-weight: 500;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-active { background: #dcfce7; color: #15803d; }
.badge-pending { background: #fef9c3; color: #854d0e; }
.badge-suspended { background: #fef2f2; color: #dc2626; }
.badge-standard { background: #f3f4f6; color: #666; }
.badge-approved { background: #dbeafe; color: #1d4ed8; }
.badge-premium { background: #fef3c7; color: #b45309; }
.badge-rejected { background: #fef2f2; color: #dc2626; }

.btn {
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #00b207;
    color: white;
}

.btn-primary:hover {
    background: #009906;
}

.btn-danger {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.btn-danger:hover {
    background: #fee2e2;
}

.btn-outline {
    background: white;
    color: #666;
    border: 1px solid #e5e7eb;
}

.btn-outline:hover {
    border-color: #00b207;
    color: #00b207;
}

.btn-block {
    width: 100%;
    justify-content: center;
}

.action-card {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
}

.action-card h4 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 12px;
    color: #1a1a1a;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    margin-bottom: 6px;
}

.form-group select,
.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
}

.form-group select:focus,
.form-group input:focus {
    outline: none;
    border-color: #00b207;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-success {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.danger-zone {
    border: 1px solid #fecaca;
    background: #fef2f2;
}

.danger-zone h4 {
    color: #dc2626;
}

@media (max-width: 900px) {
    .content-grid {
        grid-template-columns: 1fr;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<a href="<?= url('admin/business-accounts') ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Business Accounts
</a>

<div class="page-header">
    <div>
        <h1>
            <i class="fa-solid fa-building"></i>
            <?= htmlspecialchars($business['company_name'] ?? 'Unknown Company') ?>
        </h1>
    </div>
    <div>
        <?php
        $bStatus = $business['business_status'] ?? 'active';
        $statusClass = match($bStatus) {
            'active'    => 'badge-active',
            'pending'   => 'badge-pending',
            'suspended' => 'badge-suspended',
            default     => 'badge-standard',
        };
        $statusLabel = ucfirst($bStatus);
        ?>
        <span class="badge <?= $statusClass ?>" style="font-size: 14px; padding: 8px 16px;">
            <?= $statusLabel ?>
        </span>
    </div>
</div>

<?php if (!empty($_SESSION['admin_success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['admin_success']) ?>
    </div>
    <?php unset($_SESSION['admin_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['admin_error'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['admin_error']) ?>
    </div>
    <?php unset($_SESSION['admin_error']); ?>
<?php endif; ?>

<div class="content-grid">
    <!-- Main Content -->
    <div class="main-column">
        <!-- Company Information -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-building"></i> Company Information
            </div>
            <div class="info-grid">
                <div class="info-group">
                    <div class="info-label">Company Name</div>
                    <div class="info-value"><?= htmlspecialchars($business['company_name'] ?? 'N/A') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Account Tier</div>
                    <div class="info-value">
                        <?php
                        $tierClass = [
                            'standard' => 'badge-standard',
                            'approved' => 'badge-approved',
                            'premium' => 'badge-premium'
                        ];
                        $tierLabel = ucfirst($business['account_tier'] ?? 'standard');
                        $badgeClass = $tierClass[$business['account_tier'] ?? 'standard'] ?? 'badge-standard';
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $tierLabel ?></span>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Credit Limit</div>
                    <div class="info-value">$<?= number_format($business['credit_limit'] ?? 0, 2) ?> CAD</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Credit Approved</div>
                    <div class="info-value"><?= ($business['is_credit_approved'] ?? 0) ? 'Yes' : 'No' ?></div>
                </div>
            </div>
        </div>

        <!-- Quebec Legal Identity -->
        <?php if (!empty($business['neq_number']) || !empty($business['legal_name'])): ?>
        <div class="card">
            <div class="card-title">
                <i class="fas fa-landmark"></i> Quebec Legal Identity
            </div>
            <div class="info-grid">
                <?php if (!empty($business['neq_number'])): ?>
                <div class="info-group">
                    <div class="info-label">NEQ (Enterprise Number)</div>
                    <div class="info-value"><?= htmlspecialchars($business['neq_number']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($business['legal_name'])): ?>
                <div class="info-group">
                    <div class="info-label">Legal Name</div>
                    <div class="info-value"><?= htmlspecialchars($business['legal_name']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($business['operating_names'])): ?>
                <div class="info-group">
                    <div class="info-label">Operating Name(s)</div>
                    <div class="info-value"><?= htmlspecialchars($business['operating_names']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($business['registered_address_street'])): ?>
                <div class="info-group">
                    <div class="info-label">Registered Office Address</div>
                    <div class="info-value">
                        <?= htmlspecialchars($business['registered_address_street']) ?><br>
                        <?= htmlspecialchars($business['registered_address_city'] ?? '') ?>, <?= htmlspecialchars($business['registered_address_province'] ?? '') ?><br>
                        <?= htmlspecialchars($business['registered_address_postal'] ?? '') ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($business['verified_at'])): ?>
                <div class="info-group">
                    <div class="info-label">Verified On</div>
                    <div class="info-value"><?= date('F j, Y H:i', strtotime($business['verified_at'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- NEQ Verification, Option 2 (Sec. 6): live registry lookup + double-tap admin confirmation -->
        <?php if ($neqVerification): ?>
        <div class="card">
            <div class="card-title">
                <i class="fas fa-search"></i> NEQ Verification (Option 2 - Registry Lookup)
                <?php if ($neqVerification['final_status'] === 'confirmed'): ?>
                    <span style="font-size: 11px; font-weight: 600; background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 10px; margin-left: 8px;">Confirmed</span>
                <?php elseif ($neqVerification['final_status'] === 'rejected'): ?>
                    <span style="font-size: 11px; font-weight: 600; background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 10px; margin-left: 8px;">Rejected</span>
                <?php else: ?>
                    <span style="font-size: 11px; font-weight: 600; background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 10px; margin-left: 8px;">Pending Review</span>
                <?php endif; ?>
            </div>
            <div class="info-grid">
                <div class="info-group">
                    <div class="info-label">Lookup Status</div>
                    <div class="info-value">
                        <?php if ($neqVerification['lookup_status'] === 'api_not_configured'): ?>
                            No live Registraire integration configured - flagged for manual review.
                            (<a href="<?= url('admin/settings/integrations') ?>">Configure in Settings &gt; Integrations</a>)
                        <?php else: ?>
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $neqVerification['lookup_status']))) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($neqVerification['req_legal_name'])): ?>
                <div class="info-group">
                    <div class="info-label">Registry Legal Name</div>
                    <div class="info-value"><?= htmlspecialchars($neqVerification['req_legal_name']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($neqVerification['req_status'])): ?>
                <div class="info-group">
                    <div class="info-label">Registry Business Status</div>
                    <div class="info-value"><?= htmlspecialchars($neqVerification['req_status']) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($neqVerification['final_status'] === 'pending'): ?>
                <div style="margin-top: 16px; padding: 14px; background: #f9fafb; border-radius: 8px;">
                    <p style="margin: 0 0 12px; color: #666; font-size: 13px;">
                        Double-tap confirmation required: two distinct confirmations before this NEQ is treated as verified.
                    </p>
                    <?php if (empty($neqVerification['admin_step1_confirmed_at'])): ?>
                        <form action="<?= url('admin/business-accounts/neq/confirm-step1') ?>" method="POST" style="display: inline-block;">
                            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="verification_id" value="<?= $neqVerification['id'] ?>">
                            <input type="hidden" name="business_id" value="<?= $business['business_id'] ?>">
                            <button type="submit" class="btn btn-secondary">Step 1: I've Reviewed This</button>
                        </form>
                    <?php else: ?>
                        <p style="margin: 0 0 12px; color: #065f46; font-size: 13px;">
                            <i class="fas fa-check-circle"></i> Step 1 confirmed <?= date('M j, Y H:i', strtotime($neqVerification['admin_step1_confirmed_at'])) ?>.
                        </p>
                        <form action="<?= url('admin/business-accounts/neq/confirm-step2') ?>" method="POST" style="display: inline-block; margin-right: 8px;">
                            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="verification_id" value="<?= $neqVerification['id'] ?>">
                            <input type="hidden" name="business_id" value="<?= $business['business_id'] ?>">
                            <button type="submit" class="btn btn-primary">Step 2: Confirm NEQ Verified</button>
                        </form>
                        <form action="<?= url('admin/business-accounts/neq/reject') ?>" method="POST" style="display: inline-block;">
                            <input type="hidden" name="_csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="verification_id" value="<?= $neqVerification['id'] ?>">
                            <input type="hidden" name="business_id" value="<?= $business['business_id'] ?>">
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php elseif (!empty($neqVerification['admin_notes'])): ?>
                <div class="info-group" style="margin-top: 12px;">
                    <div class="info-label">Admin Notes</div>
                    <div class="info-value"><?= htmlspecialchars($neqVerification['admin_notes']) ?></div>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Verification Documents -->
        <div class="card" id="documentsPanel">
            <div class="card-title">
                <i class="fas fa-file-alt"></i> Verification Documents
            </div>

            <?php if (empty($documents)): ?>
                <p style="color:#9ca3af;font-size:13px;">No documents uploaded yet.</p>
            <?php else: ?>
                <?php foreach ($documents as $doc): ?>
                <?php
                    $docExt = strtolower(pathinfo($doc['file_path'], PATHINFO_EXTENSION));
                    $docUrl = url($doc['file_path']);
                    $statusColors = [
                        'pending'  => ['bg' => '#fef9c3', 'color' => '#854d0e', 'icon' => 'clock'],
                        'verified' => ['bg' => '#dcfce7', 'color' => '#15803d', 'icon' => 'check-circle'],
                        'rejected' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'icon' => 'times-circle'],
                    ];
                    $sc = $statusColors[$doc['status']] ?? $statusColors['pending'];
                ?>
                <div style="border:1px solid #e5e7eb;border-radius:10px;padding:16px;margin-bottom:16px;">
                    <!-- Doc header row -->
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;gap:12px;">
                        <div>
                            <div style="font-size:13px;font-weight:700;color:#1f2937;"><?= htmlspecialchars($doc['doc_label']) ?></div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:2px;">
                                Uploaded <?= date('M j, Y H:i', strtotime($doc['uploaded_at'])) ?>
                                <?php if ($doc['verified_at']): ?>
                                    &bull; Actioned <?= date('M j, Y H:i', strtotime($doc['verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;white-space:nowrap;">
                            <i class="fas fa-<?= $sc['icon'] ?>"></i> <?= ucfirst($doc['status']) ?>
                        </span>
                    </div>

                    <!-- File preview / link -->
                    <div style="margin-bottom:12px;">
                        <?php if (in_array($docExt, ['jpg','jpeg','png'])): ?>
                            <a href="<?= htmlspecialchars($docUrl) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($docUrl) ?>" alt="Document" style="max-width:100%;max-height:180px;border-radius:8px;border:1px solid #e5e7eb;">
                            </a>
                        <?php else: ?>
                            <a href="<?= htmlspecialchars($docUrl) ?>" target="_blank"
                               style="display:inline-flex;align-items:center;gap:8px;padding:9px 14px;border:1px solid #e5e7eb;border-radius:8px;color:#00b207;font-weight:600;text-decoration:none;font-size:13px;">
                                <i class="fas fa-file-pdf" style="font-size:16px;"></i> View Document
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($doc['rejection_reason'])): ?>
                    <div style="background:#fef2f2;border-left:3px solid #ef4444;padding:8px 12px;border-radius:0 6px 6px 0;font-size:12px;color:#991b1b;margin-bottom:12px;">
                        <strong>Rejection reason:</strong> <?= htmlspecialchars($doc['rejection_reason']) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Action buttons (only show if pending or re-actionable) -->
                    <?php if ($doc['status'] !== 'verified'): ?>
                    <form method="POST" action="<?= url('admin/business-accounts/documents/verify') ?>" style="display:inline;">
                        <?= csrfField() ?>
                        <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                        <input type="hidden" name="business_id" value="<?= $business['business_id'] ?? 0 ?>">
                        <button type="submit" class="btn btn-primary" style="font-size:12px;padding:7px 14px;">
                            <i class="fas fa-check"></i> Verify
                        </button>
                    </form>
                    <?php endif; ?>

                    <?php if ($doc['status'] !== 'rejected'): ?>
                    <button type="button" onclick="showRejectForm(<?= $doc['id'] ?>)"
                            class="btn btn-danger" style="font-size:12px;padding:7px 14px;margin-left:6px;">
                        <i class="fas fa-times"></i> Reject
                    </button>
                    <form id="rejectForm_<?= $doc['id'] ?>" method="POST" action="<?= url('admin/business-accounts/documents/reject') ?>"
                          style="display:none;margin-top:10px;">
                        <?= csrfField() ?>
                        <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                        <input type="hidden" name="business_id" value="<?= $business['business_id'] ?? 0 ?>">
                        <textarea name="rejection_reason" rows="2" placeholder="Reason for rejection (optional)..."
                            style="width:100%;padding:8px 10px;border:1px solid #fecaca;border-radius:6px;font-size:13px;font-family:inherit;resize:vertical;margin-bottom:6px;"></textarea>
                        <button type="submit" class="btn btn-danger" style="font-size:12px;padding:7px 14px;">
                            <i class="fas fa-times-circle"></i> Confirm Rejection
                        </button>
                        <button type="button" onclick="hideRejectForm(<?= $doc['id'] ?>)"
                                class="btn btn-outline" style="font-size:12px;padding:7px 14px;margin-left:6px;">
                            Cancel
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Contact Person -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-user"></i> Contact Person
            </div>
            <div class="info-grid">
                <div class="info-group">
                    <div class="info-label">Name</div>
                    <div class="info-value"><?= htmlspecialchars(($business['first_name'] ?? '') . ' ' . ($business['last_name'] ?? '')) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Email</div>
                    <div class="info-value">
                        <a href="mailto:<?= htmlspecialchars($business['email'] ?? '') ?>" style="color: #00b207;">
                            <?= htmlspecialchars($business['email'] ?? 'N/A') ?>
                        </a>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">Phone</div>
                    <div class="info-value">
                        <a href="tel:<?= htmlspecialchars($business['phone'] ?? '') ?>" style="color: #00b207;">
                            <?= htmlspecialchars($business['phone'] ?? 'N/A') ?>
                        </a>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">User Status</div>
                    <div class="info-value"><?= ucfirst($business['user_status'] ?? 'active') ?></div>
                </div>
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-location-dot"></i> Delivery Address
            </div>
            <div class="info-group">
                <div class="info-value">
                    <?= htmlspecialchars($business['delivery_street'] ?? '') ?><br>
                    <?= htmlspecialchars($business['delivery_city'] ?? '') ?>, <?= htmlspecialchars($business['delivery_province'] ?? '') ?><br>
                    <?= htmlspecialchars($business['delivery_postal_code'] ?? '') ?>, <?= htmlspecialchars($business['delivery_country'] ?? '') ?>
                </div>
            </div>
        </div>

        <!-- Messaging Panel -->
        <div class="card" id="messagingPanel">
            <div class="card-title">
                <i class="fas fa-comments"></i> Messages
                <span id="unreadBadge" style="display:none;background:#ef4444;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:8px;"></span>
            </div>

            <div id="messageThread" style="height:320px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:16px;background:#f9fafb;margin-bottom:14px;display:flex;flex-direction:column;gap:10px;">
                <p style="text-align:center;color:#9ca3af;font-size:13px;" id="msgLoading">Loading messages...</p>
            </div>

            <div style="display:flex;gap:8px;">
                <textarea id="adminMessageInput" rows="2"
                    style="flex:1;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;resize:none;"
                    placeholder="Type a message to this business account..."></textarea>
                <button onclick="sendAdminMessage()" style="padding:10px 18px;background:#00b207;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:14px;white-space:nowrap;">
                    <i class="fas fa-paper-plane"></i> Send
                </button>
            </div>
        </div>

        <!-- Account History -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-clock"></i> Account History
            </div>
            <div class="info-grid">
                <div class="info-group">
                    <div class="info-label">Member Since</div>
                    <div class="info-value"><?= !empty($business['user_created_at']) ? date('F j, Y', strtotime($business['user_created_at'])) : 'N/A' ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Profile Updated</div>
                    <div class="info-value"><?= !empty($business['updated_at']) ? date('F j, Y H:i', strtotime($business['updated_at'])) : 'N/A' ?></div>
                </div>
                <?php if (!empty($business['credit_approved_at'])): ?>
                    <div class="info-group">
                        <div class="info-label">Credit Approved On</div>
                        <div class="info-value"><?= date('F j, Y', strtotime($business['credit_approved_at'])) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity & Email Log -->
        <div class="card" id="activityPanel">
            <div class="card-title">
                <i class="fas fa-history"></i> Activity &amp; Email Log
            </div>

            <!-- Tabs -->
            <div style="display:flex;border-bottom:1px solid #e5e7eb;margin-bottom:16px;">
                <button onclick="switchLogTab('activity')" id="tabBtnActivity"
                    style="padding:8px 16px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:600;color:#00b207;border-bottom:2px solid #00b207;margin-bottom:-1px;">
                    <i class="fas fa-timeline" style="margin-right:6px;"></i>Activity Log
                </button>
                <button onclick="switchLogTab('emails')" id="tabBtnEmails"
                    style="padding:8px 16px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:500;color:#666;border-bottom:2px solid transparent;margin-bottom:-1px;">
                    <i class="fas fa-envelope" style="margin-right:6px;"></i>Emails Sent
                </button>
            </div>

            <!-- Activity Tab -->
            <div id="logTabActivity">
                <?php if (empty($activityLog)): ?>
                    <p style="color:#9ca3af;font-size:13px;text-align:center;padding:16px 0;">No activity recorded yet.</p>
                <?php else: ?>
                    <?php
                    $actorIcons = [
                        'admin'    => ['icon' => 'user-shield', 'color' => '#1d4ed8', 'bg' => '#dbeafe'],
                        'business' => ['icon' => 'building',    'color' => '#15803d', 'bg' => '#dcfce7'],
                        'system'   => ['icon' => 'cog',         'color' => '#6b7280', 'bg' => '#f3f4f6'],
                    ];
                    ?>
                    <div style="display:flex;flex-direction:column;gap:0;">
                        <?php foreach ($activityLog as $i => $entry): ?>
                            <?php $ai = $actorIcons[$entry['actor']] ?? $actorIcons['system']; ?>
                            <div style="display:flex;gap:12px;padding:10px 0;<?= $i > 0 ? 'border-top:1px solid #f3f4f6;' : '' ?>">
                                <div style="flex-shrink:0;width:32px;height:32px;border-radius:50%;background:<?= $ai['bg'] ?>;display:flex;align-items:center;justify-content:center;margin-top:2px;">
                                    <i class="fas fa-<?= $ai['icon'] ?>" style="font-size:12px;color:<?= $ai['color'] ?>;"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:13px;color:#1f2937;line-height:1.4;"><?= htmlspecialchars($entry['description']) ?></div>
                                    <div style="font-size:11px;color:#9ca3af;margin-top:3px;">
                                        <?= date('M j, Y H:i', strtotime($entry['created_at'])) ?>
                                        <?php if ($entry['actor_name']): ?>
                                            &middot; <?= htmlspecialchars($entry['actor_name']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Emails Tab -->
            <div id="logTabEmails" style="display:none;">
                <?php if (empty($emailLog)): ?>
                    <p style="color:#9ca3af;font-size:13px;text-align:center;padding:16px 0;">No emails logged yet.</p>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:0;">
                        <?php foreach ($emailLog as $i => $email): ?>
                            <div style="padding:10px 0;<?= $i > 0 ? 'border-top:1px solid #f3f4f6;' : '' ?>">
                                <div style="display:flex;align-items:flex-start;gap:10px;">
                                    <i class="fas fa-envelope" style="color:#6b7280;font-size:13px;margin-top:3px;flex-shrink:0;"></i>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-size:13px;font-weight:600;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            <?= htmlspecialchars($email['subject']) ?>
                                        </div>
                                        <?php if ($email['preview']): ?>
                                            <div style="font-size:12px;color:#6b7280;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                <?= htmlspecialchars($email['preview']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">
                                            <?= date('M j, Y H:i', strtotime($email['sent_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Actions -->
    <div class="sidebar-column">
        <!-- Status Actions -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-sliders"></i> Account Status
            </div>

            <?php $bStatus = $business['business_status'] ?? 'active'; ?>

            <?php if ($bStatus === 'pending'): ?>
                <!-- Approve -->
                <form method="POST" action="<?= url('admin/business-accounts/approve') ?>" style="margin-bottom:10px;">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check-circle"></i> Approve Application
                    </button>
                </form>
                <!-- Reject -->
                <form method="POST" action="<?= url('admin/business-accounts/reject') ?>" onsubmit="return confirmReject(this)">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <div class="form-group" style="margin-bottom:10px;">
                        <label>Rejection Reason (optional)</label>
                        <textarea name="rejection_reason" id="rejectionReasonInput" rows="3"
                            style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;font-family:inherit;resize:vertical;"
                            placeholder="Explain why the application is being rejected..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-times-circle"></i> Reject Application
                    </button>
                </form>
            <?php elseif ($bStatus === 'active'): ?>
                <form method="POST" action="<?= url('admin/business-accounts/suspend') ?>" onsubmit="return confirm('Are you sure you want to suspend this business account?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-ban"></i> Suspend Account
                    </button>
                </form>
            <?php else: ?>
                <form method="POST" action="<?= url('admin/business-accounts/activate') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check"></i> Activate Account
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Tier Management -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-star"></i> Account Tier
            </div>
            <form method="POST" action="<?= url('admin/business-accounts/update-tier') ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">

                <div class="form-group">
                    <label>Account Tier</label>
                    <select name="tier">
                        <option value="standard" <?= ($business['account_tier'] ?? 'standard') === 'standard' ? 'selected' : '' ?>>Standard</option>
                        <option value="approved" <?= ($business['account_tier'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="premium" <?= ($business['account_tier'] ?? '') === 'premium' ? 'selected' : '' ?>>Premium</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Credit Limit (CAD)</label>
                    <input type="number"
                           name="credit_limit"
                           step="0.01"
                           min="0"
                           value="<?= $business['credit_limit'] ?? 0 ?>"
                           placeholder="0.00">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Update Tier
                </button>
            </form>
        </div>

        <?php
        $plans = $plans ?? [];
        $qualification = $qualification ?? ['qualified' => false, 'order_count' => 0, 'account_age_days' => 0];
        $creditEvents = $creditEvents ?? [];
        $paymentTerms = $business['payment_terms'] ?? 'prepay';
        $creditReviewStatus = $business['credit_review_status'] ?? 'not_required';
        $isSuspended = !empty($business['net30_suspended_at']);
        $hasCardOnFile = !empty($business['stripe_payment_method_id']);
        ?>
        <!-- Distribution Plan & Net-30 Credit (Sec 5) -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Distribution Plan &amp; Net-30 Credit
            </div>

            <form method="POST" action="<?= url('admin/business-accounts/update-plan') ?>" style="margin-bottom:16px;">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                <div class="form-group">
                    <label>Distribution Plan</label>
                    <select name="distribution_plan_id">
                        <?php foreach ($plans as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= ((int)($business['distribution_plan_id'] ?? 0) === (int)$p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                                (<?= $p['monthly_fee'] > 0 ? '$' . number_format($p['monthly_fee'], 2) . '/mo, ' : 'free, ' ?><?= $p['is_negotiated'] ? 'negotiated limit' : '$' . number_format($p['credit_limit'], 2) . ' limit' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Credit Limit (CAD) <small style="opacity:.7;">— only used for negotiated/Enterprise plans, otherwise auto-set from the plan</small></label>
                    <input type="number" name="credit_limit" step="0.01" min="0" value="<?= $business['credit_limit'] ?? 0 ?>" placeholder="0.00">
                </div>
                <button type="submit" class="btn btn-secondary btn-block"><i class="fas fa-save"></i> Update Plan</button>
            </form>

            <div class="form-group">
                <label>Qualification (Sec 5.1 — 3 paid orders + 30 days tenure)</label>
                <div class="info-value">
                    <?= $qualification['order_count'] ?> paid order(s), <?= $qualification['account_age_days'] ?> day(s) old —
                    <?php if ($qualification['qualified']): ?>
                        <span style="color:#16a34a;font-weight:600;">Qualified</span>
                    <?php else: ?>
                        <span style="color:#6b7280;">Not yet qualified</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Payment Terms / Credit Review Status</label>
                <div class="info-value">
                    <strong><?= strtoupper($paymentTerms) ?></strong> — credit review: <?= str_replace('_', ' ', $creditReviewStatus) ?>
                    <?php if ($isSuspended): ?>
                        <br><span style="color:#dc2626;">Suspended <?= date('M j, Y', strtotime($business['net30_suspended_at'])) ?>: <?= htmlspecialchars($business['net30_suspension_reason'] ?? '') ?></span>
                    <?php endif; ?>
                    <br>Card on file: <?= $hasCardOnFile ? htmlspecialchars(($business['card_brand'] ?? '') . ' ****' . ($business['card_last4'] ?? '')) : '<span style="color:#dc2626;">none</span>' ?>
                </div>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <?php if ($creditReviewStatus === 'not_required' || $creditReviewStatus === 'admin_waived'): ?>
                <form method="POST" action="<?= url('admin/business-accounts/credit-check') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search-dollar"></i> Run Credit Check</button>
                </form>
                <?php endif; ?>

                <?php if ($creditReviewStatus === 'pending_manual_review'): ?>
                <form method="POST" action="<?= url('admin/business-accounts/waive-credit-check') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <input type="hidden" name="notes" value="Waived by admin - no live bureau integration configured.">
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-user-check"></i> Waive (Manual Approval)</button>
                </form>
                <?php endif; ?>

                <?php if ($paymentTerms !== 'net30' && $qualification['qualified'] && in_array($creditReviewStatus, ['bureau_approved', 'admin_approved', 'admin_waived'], true)): ?>
                <form method="POST" action="<?= url('admin/business-accounts/approve-net30') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check-circle"></i> Approve Net-30</button>
                </form>
                <?php elseif ($paymentTerms !== 'net30' && $qualification['qualified']): ?>
                    <span style="align-self:center;color:#6b7280;font-size:13px;">Run/waive credit check before approving net-30.</span>
                <?php endif; ?>

                <?php if ($paymentTerms === 'net30' && !$isSuspended): ?>
                <form method="POST" action="<?= url('admin/business-accounts/suspend-net30') ?>" onsubmit="return confirm('Suspend net-30 for this account?');">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <input type="hidden" name="reason" value="Manual suspension by admin.">
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> Suspend Net-30</button>
                </form>
                <?php endif; ?>

                <?php if ($isSuspended): ?>
                <form method="POST" action="<?= url('admin/business-accounts/reinstate-net30') ?>">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Clear Suspension (requires fresh review)</button>
                </form>
                <?php endif; ?>
            </div>

            <?php if (!empty($creditEvents)): ?>
            <div style="margin-top:16px;">
                <label style="font-weight:600;font-size:13px;">Credit Event History</label>
                <div style="max-height:200px;overflow-y:auto;font-size:12px;margin-top:6px;">
                    <?php foreach ($creditEvents as $ev): ?>
                        <div style="padding:6px 0;border-bottom:1px solid #f0f0f0;">
                            <strong><?= htmlspecialchars($ev['event_type']) ?></strong> (<?= htmlspecialchars($ev['changed_by_type']) ?>)
                            — <?= date('M j, Y g:ia', strtotime($ev['created_at'])) ?>
                            <?php if (!empty($ev['details'])): ?><br><span style="color:#6b7280;"><?= htmlspecialchars($ev['details']) ?></span><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Request Document -->
        <div class="card">
            <div class="card-title">
                <i class="fas fa-file-upload"></i> Request Document
            </div>
            <form method="POST" action="<?= url('admin/business-accounts/documents/request') ?>" id="reqDocForm">
                <?= csrfField() ?>
                <input type="hidden" name="business_id" value="<?= $business['business_id'] ?? 0 ?>">

                <div class="form-group">
                    <label>Document Type</label>
                    <select name="doc_type" onchange="toggleCustomLabel(this)" style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:14px;">
                        <option value="doc_declaration">Déclaration d'immatriculation</option>
                        <option value="doc_certificate">Certificate of Incorporation</option>
                        <option value="other">Other (custom)</option>
                    </select>
                </div>

                <div class="form-group" id="customLabelGroup" style="display:none;">
                    <label>Document Name</label>
                    <input type="text" name="custom_label" placeholder="e.g. Proof of Address" style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:14px;">
                </div>

                <div class="form-group">
                    <label>Deadline (optional)</label>
                    <input type="date" name="deadline" min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                           style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:14px;">
                </div>

                <div class="form-group">
                    <label>Message (optional)</label>
                    <textarea name="message" rows="2" placeholder="Add context for the business..."
                        style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:6px;font-size:14px;font-family:inherit;resize:vertical;"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> Send Request
                </button>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="card danger-zone">
            <div class="card-title" style="color: #dc2626;">
                <i class="fas fa-exclamation-triangle"></i> Danger Zone
            </div>
            <p style="font-size: 13px; color: #666; margin-bottom: 16px;">
                Permanently delete this business account. This action cannot be undone.
            </p>
            <form method="POST" action="<?= url('admin/business-accounts/delete') ?>" onsubmit="return confirm('Are you absolutely sure? This will permanently delete this business account and all associated data.')">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= $business['business_id'] ?? 0 ?>">

                <div style="margin-bottom:10px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Reason / Raison</label>
                    <select name="delete_reason" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;color:#1f2937;background:#fff;">
                        <option value="voluntary">Voluntary departure / Départ volontaire</option>
                        <option value="inactive">Inactivity / Inactivité</option>
                        <option value="terms_violation">Terms violation / Violation des conditions</option>
                        <option value="business_conduct">Business conduct / Conduite commerciale</option>
                        <option value="test">Test account / Compte de test</option>
                        <option value="other">Other / Autre</option>
                    </select>
                </div>
                <div style="margin-bottom:10px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Notes (optional / optionnel)</label>
                    <textarea name="delete_notes" rows="2" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;resize:vertical;"></textarea>
                </div>
                <div style="margin-bottom:14px;display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="can_rejoin" id="bizCanRejoin" value="1" checked style="width:15px;height:15px;accent-color:#dc2626;">
                    <label for="bizCanRejoin" style="font-size:12px;color:#374151;cursor:pointer;">Allow re-registration (uncheck = banned)</label>
                </div>

                <button type="submit" class="btn btn-danger btn-block">
                    <i class="fas fa-trash"></i> Delete Account
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmReject(form) {
    return confirm('Are you sure you want to reject this application? The applicant will be notified.');
}

function switchLogTab(tab) {
    document.getElementById('logTabActivity').style.display = tab === 'activity' ? 'block' : 'none';
    document.getElementById('logTabEmails').style.display   = tab === 'emails'   ? 'block' : 'none';
    document.getElementById('tabBtnActivity').style.color        = tab === 'activity' ? '#00b207' : '#666';
    document.getElementById('tabBtnActivity').style.borderBottomColor = tab === 'activity' ? '#00b207' : 'transparent';
    document.getElementById('tabBtnActivity').style.fontWeight   = tab === 'activity' ? '600' : '500';
    document.getElementById('tabBtnEmails').style.color          = tab === 'emails'   ? '#00b207' : '#666';
    document.getElementById('tabBtnEmails').style.borderBottomColor   = tab === 'emails'   ? '#00b207' : 'transparent';
    document.getElementById('tabBtnEmails').style.fontWeight     = tab === 'emails'   ? '600' : '500';
}

function toggleCustomLabel(sel) {
    document.getElementById('customLabelGroup').style.display = sel.value === 'other' ? 'block' : 'none';
}

function showRejectForm(docId) {
    document.getElementById('rejectForm_' + docId).style.display = 'block';
}
function hideRejectForm(docId) {
    document.getElementById('rejectForm_' + docId).style.display = 'none';
}

// ── Messaging panel ──────────────────────────────────────────────
const BIZ_ID = <?= (int)($business['business_id'] ?? 0) ?>;

function renderMessage(msg) {
    const isAdmin    = msg.sender_type === 'admin';
    const name       = isAdmin ? ((msg.admin_first_name + ' ' + msg.admin_last_name).trim() || 'Admin') : 'Business';
    const dt         = new Date(msg.created_at.replace(' ', 'T'));
    const timeStr    = dt.toLocaleString('en-CA', {month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});
    const bgColor    = isAdmin ? '#dbeafe' : '#f3f4f6';
    const align      = isAdmin ? 'flex-end' : 'flex-start';
    const nameColor  = isAdmin ? '#1d4ed8' : '#374151';
    return `<div style="display:flex;flex-direction:column;align-items:${align};max-width:80%;align-self:${align};">
        <div style="font-size:11px;color:${nameColor};font-weight:600;margin-bottom:3px;">${name}</div>
        <div style="background:${bgColor};padding:10px 14px;border-radius:10px;font-size:13px;color:#1f2937;line-height:1.5;">${escHtml(msg.message)}</div>
        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">${timeStr}</div>
    </div>`;
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

function loadMessages() {
    fetch(`<?= url('admin/business-accounts/messages') ?>?business_id=${BIZ_ID}`, {credentials:'same-origin'})
        .then(r => r.json())
        .then(data => {
            const thread = document.getElementById('messageThread');
            if (!data.success || !data.messages.length) {
                thread.innerHTML = '<p style="text-align:center;color:#9ca3af;font-size:13px;">No messages yet.</p>';
                return;
            }
            thread.innerHTML = data.messages.map(renderMessage).join('');
            thread.scrollTop = thread.scrollHeight;
            const unread = data.messages.filter(m => m.sender_type === 'business' && m.is_read == 0).length;
            const badge  = document.getElementById('unreadBadge');
            if (unread > 0) { badge.textContent = unread + ' unread'; badge.style.display = 'inline'; }
            else { badge.style.display = 'none'; }
        })
        .catch(() => {
            document.getElementById('messageThread').innerHTML = '<p style="text-align:center;color:#ef4444;font-size:13px;">Failed to load messages.</p>';
        });
}

function sendAdminMessage() {
    const input = document.getElementById('adminMessageInput');
    const msg   = input.value.trim();
    if (!msg) return;

    fetch('<?= url('admin/business-accounts/messages/send') ?>', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': '<?= generateCsrfToken() ?>'},
        body: JSON.stringify({business_id: BIZ_ID, message: msg, _csrf_token: '<?= generateCsrfToken() ?>'})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages();
        } else {
            alert(data.error || 'Failed to send message.');
        }
    })
    .catch(() => alert('Network error. Please try again.'));
}

document.getElementById('adminMessageInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) sendAdminMessage();
});

document.addEventListener('DOMContentLoaded', loadMessages);
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
