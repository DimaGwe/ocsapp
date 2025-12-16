<?php
/**
 * Admin Vendor Invites Management - OCSAPP
 */
?>
<?php ob_start(); ?>

<style>
.invites-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.invites-header h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
}

.invite-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.invite-modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 32px;
    max-width: 500px;
    width: 90%;
}

.modal-header {
    margin-bottom: 24px;
}

.modal-header h3 {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    font-size: 14px;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-secondary {
    background: #f3f4f6;
    color: #4b5563;
}

.invites-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.invites-table table {
    width: 100%;
    border-collapse: collapse;
}

.invites-table thead {
    background: #f9fafb;
}

.invites-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #6b7280;
    text-transform: uppercase;
}

.invites-table td {
    padding: 16px;
    border-top: 1px solid #e5e7eb;
}

.invite-code {
    font-family: 'Courier New', monospace;
    background: #f3f4f6;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 16px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-expired {
    background: #fee2e2;
    color: #991b1b;
}

.status-used {
    background: #e5e7eb;
    color: #4b5563;
}

.copy-btn {
    padding: 4px 12px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
}

.copy-btn:hover {
    background: #2563eb;
}
</style>

<div>
    <div class="invites-header">
        <div>
            <a href="<?= url('admin/vendors') ?>" style="color: #6b7280; text-decoration: none; margin-bottom: 8px; display: inline-block;">
                <i class="fa-solid fa-arrow-left"></i> Back to Vendors
            </a>
            <h1><i class="fa-solid fa-envelope"></i> Vendor Invites</h1>
        </div>
        <button onclick="openInviteModal()" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Generate Invite Code
        </button>
    </div>

    <div class="invites-table">
        <table>
            <thead>
                <tr>
                    <th>Invite Code</th>
                    <th>Created By</th>
                    <th>Note</th>
                    <th>Uses</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th>Used By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invites)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #9ca3af;">
                        <i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                        No invite codes generated yet
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($invites as $invite): ?>
                <?php
                $isExpired = $invite['expires_at'] && strtotime($invite['expires_at']) < time();
                $isUsed = $invite['uses_count'] >= $invite['max_uses'];
                $status = $isUsed ? 'used' : ($isExpired ? 'expired' : 'active');
                ?>
                <tr>
                    <td>
                        <span class="invite-code"><?= htmlspecialchars($invite['code']) ?></span>
                    </td>
                    <td><?= htmlspecialchars($invite['created_by_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($invite['note'] ?: '-') ?></td>
                    <td><?= $invite['uses_count'] ?> / <?= $invite['max_uses'] ?></td>
                    <td>
                        <?php if ($invite['expires_at']): ?>
                            <?= date('M j, Y', strtotime($invite['expires_at'])) ?>
                        <?php else: ?>
                            Never
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $status ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($invite['used_by_vendor']): ?>
                            <?= htmlspecialchars($invite['used_by_vendor']) ?>
                            <br>
                            <small style="color: #6b7280;"><?= date('M j, Y', strtotime($invite['used_at'])) ?></small>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($status === 'active'): ?>
                            <button onclick="copyInviteUrl('<?= url('vendor/register?invite=' . $invite['code']) ?>')" class="copy-btn">
                                <i class="fa-solid fa-copy"></i> Copy Link
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Generate Invite Modal -->
<div class="invite-modal" id="inviteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Generate Invite Code</h3>
        </div>

        <form id="generateInviteForm">
            <?= csrfField() ?>

            <div class="form-group">
                <label>Note (optional)</label>
                <textarea name="note" rows="3" placeholder="e.g., For ABC Supply Company, Conference invite, etc."></textarea>
            </div>

            <div class="form-group">
                <label>Expires In (days)</label>
                <select name="expires_in_days">
                    <option value="7">7 days</option>
                    <option value="30" selected>30 days</option>
                    <option value="60">60 days</option>
                    <option value="90">90 days</option>
                    <option value="0">Never</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="closeInviteModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Code</button>
            </div>
        </form>
    </div>
</div>

<script>
function openInviteModal() {
    document.getElementById('inviteModal').classList.add('active');
}

function closeInviteModal() {
    document.getElementById('inviteModal').classList.remove('active');
    document.getElementById('generateInviteForm').reset();
}

document.getElementById('generateInviteForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);

    try {
        const response = await fetch('<?= url('admin/vendors/generate-invite') ?>', {
            method: 'POST',
            body: new URLSearchParams(formData)
        });

        const data = await response.json();

        if (data.success) {
            alert(`Invite code generated: ${data.code}\n\nInvite URL:\n${data.url}\n\nExpires: ${data.expires_at}`);
            location.reload();
        } else {
            alert(data.message || 'Failed to generate invite code');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
});

function copyInviteUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('Invite URL copied to clipboard!');
    }).catch(() => {
        prompt('Copy this URL:', url);
    });
}

// Close modal when clicking outside
document.getElementById('inviteModal').addEventListener('click', (e) => {
    if (e.target.id === 'inviteModal') {
        closeInviteModal();
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
