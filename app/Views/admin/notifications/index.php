<?php
$currentPage = 'notifications';
ob_start();

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';
$t = $currentLang === 'fr' ? [
    'notifications' => 'Notifications',
    'all_types' => 'Tous les types',
    'account_lockouts' => 'Blocages de compte',
    'new_users' => 'Nouveaux utilisateurs',
    'seller_applications' => 'Demandes vendeurs',
    'new_orders' => 'Nouvelles commandes',
    'low_stock' => 'Stock faible',
    'system' => 'Système',
    'task_assigned' => 'Tâche Assignée',
    'task_completed' => 'Tâche Complétée',
    'task_comment' => 'Commentaire Tâche',
    'note_comment' => 'Commentaire Note',
    'mention' => '@Mention',
    'all_status' => 'Tous les statuts',
    'unread_only' => 'Non lus',
    'read_only' => 'Lus',
    'no_notifications' => 'Aucune notification',
    'mark_all_read' => 'Tout marquer lu',
    'view' => 'Voir',
    'mark_read' => 'Marquer lu',
    'read_by' => 'Lu par'
] : [
    'notifications' => 'Notifications',
    'all_types' => 'All Types',
    'account_lockouts' => 'Account Lockouts',
    'new_users' => 'New Users',
    'seller_applications' => 'Seller Applications',
    'new_orders' => 'New Orders',
    'low_stock' => 'Low Stock',
    'system' => 'System',
    'task_assigned' => 'Task Assigned',
    'task_completed' => 'Task Completed',
    'task_comment' => 'Task Comment',
    'note_comment' => 'Note Comment',
    'mention' => '@Mention',
    'all_status' => 'All Status',
    'unread_only' => 'Unread Only',
    'read_only' => 'Read Only',
    'no_notifications' => 'No notifications found',
    'mark_all_read' => 'Mark All Read',
    'view' => 'View',
    'mark_read' => 'Mark Read',
    'read_by' => 'Read by'
];

// Icon mappings
$typeIcons = [
    'account_lockout' => 'lock',
    'new_user' => 'user-plus',
    'seller_application' => 'store',
    'seller_verified' => 'check-circle',
    'new_order' => 'shopping-cart',
    'low_stock' => 'exclamation-triangle',
    'system' => 'cog',
    'security' => 'shield-halved',
    'task_assigned' => 'user-check',
    'task_completed' => 'circle-check',
    'task_comment' => 'comment',
    'note_comment' => 'comment-dots',
    'mention' => 'at',
];
?>

<style>
.notifications-page {
    max-width: 1000px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.page-header h1 {
    margin: 0;
    font-size: 24px;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header h1 i {
    color: var(--primary);
}

.filters-row {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    background: white;
    min-width: 150px;
}

.mark-all-btn {
    padding: 8px 16px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}

.mark-all-btn:hover {
    background: var(--primary-600);
}

.notification-full-list {
    display: flex;
    flex-direction: column;
    gap: 1px;
    background: var(--border);
    border-radius: 12px;
    overflow: hidden;
}

.notification-row {
    display: flex;
    align-items: flex-start;
    padding: 16px 20px;
    background: white;
    gap: 16px;
    transition: background 0.2s;
}

.notification-row:hover {
    background: var(--gray-50);
}

.notification-row.unread {
    background: #eff6ff;
    border-left: 4px solid var(--primary);
}

.notification-row.unread:hover {
    background: #dbeafe;
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 20px;
}

.notification-icon.icon-account_lockout {
    background: #fee2e2;
    color: #dc2626;
}

.notification-icon.icon-new_user {
    background: #dcfce7;
    color: #16a34a;
}

.notification-icon.icon-seller_application,
.notification-icon.icon-seller_verified {
    background: #dbeafe;
    color: #2563eb;
}

.notification-icon.icon-new_order {
    background: #f0fdf4;
    color: #15803d;
}

.notification-icon.icon-low_stock {
    background: #fef3c7;
    color: #d97706;
}

.notification-icon.icon-system,
.notification-icon.icon-security {
    background: var(--gray-100);
    color: var(--gray-600);
}

.notification-icon.icon-task_assigned { background: #dbeafe; color: #2563eb; }
.notification-icon.icon-task_completed { background: #dcfce7; color: #16a34a; }
.notification-icon.icon-task_comment { background: #fef3c7; color: #d97706; }
.notification-icon.icon-note_comment { background: #f3e8ff; color: #9333ea; }
.notification-icon.icon-mention { background: #fce7f3; color: #db2777; }

.notification-details {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
}

.notification-message {
    font-size: 14px;
    color: var(--gray-600);
    line-height: 1.5;
    margin-bottom: 8px;
}

.notification-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.notification-meta span {
    font-size: 12px;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 4px;
}

.notification-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--primary);
    color: white;
    border: none;
}

.btn-primary:hover {
    background: var(--primary-600);
}

.btn-outline {
    background: white;
    color: var(--gray-600);
    border: 1px solid var(--border);
}

.btn-outline:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-500);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
    opacity: 0.5;
}

.empty-state p {
    font-size: 16px;
    margin: 0;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
}

.page-link {
    padding: 8px 14px;
    border: 1px solid var(--border);
    border-radius: 6px;
    text-decoration: none;
    color: var(--gray-700);
    font-size: 14px;
    transition: all 0.2s;
}

.page-link:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.page-link.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

@media (max-width: 768px) {
    .notification-row {
        flex-direction: column;
    }

    .notification-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .filters-row {
        width: 100%;
    }

    .filter-select {
        flex: 1;
    }
}
</style>

<div class="notifications-page">
    <div class="page-header">
        <h1><i class="fa-solid fa-bell"></i> <?= $t['notifications'] ?></h1>

        <div class="filters-row">
            <form method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value=""><?= $t['all_types'] ?></option>
                    <option value="account_lockout" <?= ($typeFilter ?? '') === 'account_lockout' ? 'selected' : '' ?>><?= $t['account_lockouts'] ?></option>
                    <option value="new_user" <?= ($typeFilter ?? '') === 'new_user' ? 'selected' : '' ?>><?= $t['new_users'] ?></option>
                    <option value="seller_application" <?= ($typeFilter ?? '') === 'seller_application' ? 'selected' : '' ?>><?= $t['seller_applications'] ?></option>
                    <option value="new_order" <?= ($typeFilter ?? '') === 'new_order' ? 'selected' : '' ?>><?= $t['new_orders'] ?></option>
                    <option value="low_stock" <?= ($typeFilter ?? '') === 'low_stock' ? 'selected' : '' ?>><?= $t['low_stock'] ?></option>
                    <option value="system" <?= ($typeFilter ?? '') === 'system' ? 'selected' : '' ?>><?= $t['system'] ?></option>
                    <option value="task_assigned" <?= ($typeFilter ?? '') === 'task_assigned' ? 'selected' : '' ?>><?= $t['task_assigned'] ?></option>
                    <option value="task_completed" <?= ($typeFilter ?? '') === 'task_completed' ? 'selected' : '' ?>><?= $t['task_completed'] ?></option>
                    <option value="task_comment" <?= ($typeFilter ?? '') === 'task_comment' ? 'selected' : '' ?>><?= $t['task_comment'] ?></option>
                    <option value="note_comment" <?= ($typeFilter ?? '') === 'note_comment' ? 'selected' : '' ?>><?= $t['note_comment'] ?></option>
                    <option value="mention" <?= ($typeFilter ?? '') === 'mention' ? 'selected' : '' ?>><?= $t['mention'] ?></option>
                </select>
                <select name="read" class="filter-select" onchange="this.form.submit()">
                    <option value=""><?= $t['all_status'] ?></option>
                    <option value="unread" <?= ($readFilter ?? '') === 'unread' ? 'selected' : '' ?>><?= $t['unread_only'] ?></option>
                    <option value="read" <?= ($readFilter ?? '') === 'read' ? 'selected' : '' ?>><?= $t['read_only'] ?></option>
                </select>
            </form>

            <?php
            $hasUnread = false;
            foreach ($notifications as $n) {
                if (!$n['is_read']) {
                    $hasUnread = true;
                    break;
                }
            }
            ?>
            <?php if ($hasUnread): ?>
                <button onclick="markAllRead()" class="mark-all-btn">
                    <i class="fa-solid fa-check-double"></i> <?= $t['mark_all_read'] ?>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding: 0;">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <i class="fa-regular fa-bell-slash"></i>
                    <p><?= $t['no_notifications'] ?></p>
                </div>
            <?php else: ?>
                <div class="notification-full-list">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-row <?= $notification['is_read'] ? '' : 'unread' ?>" id="notification-<?= $notification['id'] ?>">
                            <div class="notification-icon icon-<?= htmlspecialchars($notification['type']) ?>">
                                <i class="fa-solid fa-<?= htmlspecialchars($notification['icon'] ?? 'bell') ?>"></i>
                            </div>
                            <div class="notification-details">
                                <div class="notification-title"><?= htmlspecialchars($notification['title']) ?></div>
                                <div class="notification-message"><?= htmlspecialchars($notification['message']) ?></div>
                                <div class="notification-meta">
                                    <span>
                                        <i class="fa-regular fa-clock"></i>
                                        <?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?>
                                    </span>
                                    <?php if ($notification['is_read'] && !empty($notification['read_by_name'])): ?>
                                        <span>
                                            <i class="fa-regular fa-eye"></i>
                                            <?= $t['read_by'] ?> <?= htmlspecialchars($notification['read_by_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="notification-actions">
                                <?php if (!empty($notification['link'])): ?>
                                    <a href="<?= htmlspecialchars($notification['link']) ?>" class="btn-sm btn-primary" onclick="markRead(<?= $notification['id'] ?>)">
                                        <i class="fa-solid fa-arrow-right"></i> <?= $t['view'] ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (!$notification['is_read']): ?>
                                    <button onclick="markRead(<?= $notification['id'] ?>)" class="btn-sm btn-outline">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                            <a href="?page=<?= $i ?>&type=<?= urlencode($typeFilter ?? '') ?>&read=<?= urlencode($readFilter ?? '') ?>"
                               class="page-link <?= $page == $i ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
async function markRead(id) {
    try {
        const response = await fetch('<?= url('api/admin/notifications/mark-read') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: id })
        });
        const data = await response.json();
        if (data.success) {
            // Update UI
            const row = document.getElementById('notification-' + id);
            if (row) {
                row.classList.remove('unread');
                // Remove mark read button
                const markBtn = row.querySelector('.btn-outline');
                if (markBtn) markBtn.remove();
            }
        }
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
}

async function markAllRead() {
    try {
        const response = await fetch('<?= url('api/admin/notifications/mark-all-read') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        }
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
}
</script>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layout.php';
?>
