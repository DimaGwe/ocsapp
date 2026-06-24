<?php
/**
 * Admin Profile Page
 * File: app/Views/admin/profile.php
 */

$pageTitle = 'Profile';
$currentPage = 'profile';

$currentLang = $_SESSION['language'] ?? 'fr';

$translations = [
    'en' => [
        'profile' => 'My Profile',
        'member_since' => 'Member since',
        'activity_stats' => 'Activity Stats',
        'total_activities' => 'Total Activities',
        'tasks_assigned' => 'Tasks Assigned',
        'tasks_completed' => 'Tasks Completed',
        'comments_made' => 'Comments Made',
        'notification_preferences' => 'Notification Preferences',
        'notification_desc' => 'Choose how you want to be notified about team planner activity.',
        'notification_type' => 'Notification Type',
        'in_app' => 'In-App',
        'email' => 'Email',
        'save_preferences' => 'Save Preferences',
        'task_assigned' => 'Task Assigned',
        'task_assigned_desc' => 'When someone assigns a task to you',
        'task_completed' => 'Task Completed',
        'task_completed_desc' => 'When someone completes a task you created',
        'task_comment' => 'Task Comment',
        'task_comment_desc' => 'When someone comments on your task',
        'note_comment' => 'Note Comment',
        'note_comment_desc' => 'When someone comments on your note',
        'mention' => '@Mention',
        'mention_desc' => 'When someone mentions you in a note or comment',
    ],
    'fr' => [
        'profile' => 'Mon Profil',
        'member_since' => 'Membre depuis',
        'activity_stats' => 'Statistiques d\'Activité',
        'total_activities' => 'Activités Totales',
        'tasks_assigned' => 'Tâches Assignées',
        'tasks_completed' => 'Tâches Complétées',
        'comments_made' => 'Commentaires',
        'notification_preferences' => 'Préférences de Notification',
        'notification_desc' => 'Choisissez comment vous souhaitez être notifié de l\'activité du planificateur.',
        'notification_type' => 'Type de Notification',
        'in_app' => 'In-App',
        'email' => 'Courriel',
        'save_preferences' => 'Enregistrer',
        'task_assigned' => 'Tâche Assignée',
        'task_assigned_desc' => 'Quand quelqu\'un vous assigne une tâche',
        'task_completed' => 'Tâche Complétée',
        'task_completed_desc' => 'Quand quelqu\'un complète une tâche que vous avez créée',
        'task_comment' => 'Commentaire de Tâche',
        'task_comment_desc' => 'Quand quelqu\'un commente votre tâche',
        'note_comment' => 'Commentaire de Note',
        'note_comment_desc' => 'Quand quelqu\'un commente votre note',
        'mention' => '@Mention',
        'mention_desc' => 'Quand quelqu\'un vous mentionne dans une note ou un commentaire',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
.profile-container {
    max-width: 900px;
    margin: 0 auto;
}

/* User Info Card */
.profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 40px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 32px;
}

.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00b207, #009206);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(0,178,7,0.25);
}

.profile-info h2 {
    margin: 0 0 6px;
    font-size: 24px;
    color: #1f2937;
}

.profile-role-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
    margin-bottom: 8px;
}

.role-super_admin { background: #fef3c7; color: #92400e; }
.role-senior_admin { background: #dbeafe; color: #1e40af; }
.role-admin { background: #dcfce7; color: #166534; }
.role-junior_admin { background: #f3e8ff; color: #6b21a8; }

.profile-meta {
    display: flex;
    gap: 20px;
    color: #6b7280;
    font-size: 14px;
    margin-top: 4px;
}

.profile-meta i {
    margin-right: 5px;
    color: #9ca3af;
}

/* Stats Grid */
.stats-section {
    margin-bottom: 24px;
}

.stats-section h3 {
    font-size: 18px;
    color: #1f2937;
    margin: 0 0 16px;
    font-weight: 600;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #f3f4f6;
}

.stat-card .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 20px;
}

.stat-card .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-card .stat-label {
    font-size: 13px;
    color: #6b7280;
}

.stat-icon.blue { background: #dbeafe; color: #2563eb; }
.stat-icon.green { background: #dcfce7; color: #16a34a; }
.stat-icon.purple { background: #f3e8ff; color: #9333ea; }
.stat-icon.amber { background: #fef3c7; color: #d97706; }

/* Notification Preferences */
.prefs-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    overflow: hidden;
}

.prefs-header {
    padding: 28px 32px 20px;
    border-bottom: 1px solid #f3f4f6;
}

.prefs-header h3 {
    font-size: 18px;
    color: #1f2937;
    margin: 0 0 4px;
    font-weight: 600;
}

.prefs-header p {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

.prefs-table {
    width: 100%;
    border-collapse: collapse;
}

.prefs-table th {
    padding: 12px 32px;
    font-size: 12px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: left;
    background: #f9fafb;
}

.prefs-table th:not(:first-child) {
    text-align: center;
    width: 100px;
}

.prefs-table td {
    padding: 16px 32px;
    border-top: 1px solid #f3f4f6;
}

.prefs-table td:not(:first-child) {
    text-align: center;
}

.pref-type-label {
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 2px;
}

.pref-type-desc {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

/* Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #d1d5db;
    border-radius: 24px;
    transition: 0.25s;
}

.toggle-slider::before {
    content: '';
    position: absolute;
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: #fff;
    border-radius: 50%;
    transition: 0.25s;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #00b207;
}

.toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(20px);
}

.prefs-footer {
    padding: 20px 32px;
    border-top: 1px solid #f3f4f6;
    display: flex;
    justify-content: flex-end;
}

.btn-save {
    padding: 10px 28px;
    background: linear-gradient(135deg, #00b207, #009206);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-save:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .profile-card { flex-direction: column; text-align: center; padding: 28px; }
    .profile-meta { justify-content: center; flex-wrap: wrap; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .prefs-table th, .prefs-table td { padding: 12px 16px; }
}
</style>

<div class="profile-container">

    <!-- User Info Card -->
    <div class="profile-card">
        <div class="profile-avatar">
            <?= strtoupper(substr($user['first_name'] ?? 'A', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
        </div>
        <div class="profile-info">
            <h2><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></h2>
            <span class="profile-role-badge role-<?= htmlspecialchars($user['role'] ?? 'admin') ?>">
                <?= htmlspecialchars(str_replace('_', ' ', $user['role'] ?? 'admin')) ?>
            </span>
            <div class="profile-meta">
                <span><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($user['email'] ?? '') ?></span>
                <span><i class="fa-solid fa-calendar"></i> <?= $t['member_since'] ?> <?= date('M Y', strtotime($user['created_at'] ?? 'now')) ?></span>
            </div>
        </div>
    </div>

    <!-- Activity Stats -->
    <div class="stats-section">
        <h3><?= $t['activity_stats'] ?></h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fa-solid fa-chart-line"></i></div>
                <div class="stat-value"><?= (int)($activityStats['total_activities'] ?? 0) ?></div>
                <div class="stat-label"><?= $t['total_activities'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon amber"><i class="fa-solid fa-list-check"></i></div>
                <div class="stat-value"><?= (int)($taskStats['assigned_tasks'] ?? 0) ?></div>
                <div class="stat-label"><?= $t['tasks_assigned'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                <div class="stat-value"><?= (int)($taskStats['completed_tasks'] ?? 0) ?></div>
                <div class="stat-label"><?= $t['tasks_completed'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fa-solid fa-comments"></i></div>
                <div class="stat-value"><?= (int)($activityStats['comment_activities'] ?? 0) ?></div>
                <div class="stat-label"><?= $t['comments_made'] ?></div>
            </div>
        </div>
    </div>

    <!-- Notification Preferences -->
    <form method="POST" action="<?= url('admin/profile/update-preferences') ?>">
        <div class="prefs-card">
            <div class="prefs-header">
                <h3><i class="fa-solid fa-bell" style="color: #00b207; margin-right: 8px;"></i><?= $t['notification_preferences'] ?></h3>
                <p><?= $t['notification_desc'] ?></p>
            </div>

            <table class="prefs-table">
                <thead>
                    <tr>
                        <th><?= $t['notification_type'] ?></th>
                        <th><?= $t['in_app'] ?></th>
                        <th><?= $t['email'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preferences as $type => $pref): ?>
                    <tr>
                        <td>
                            <div class="pref-type-label"><?= htmlspecialchars($t[$type] ?? $pref['label']) ?></div>
                            <div class="pref-type-desc"><?= htmlspecialchars($t[$type . '_desc'] ?? $pref['description']) ?></div>
                        </td>
                        <td>
                            <label class="toggle-switch">
                                <input type="checkbox" name="prefs[<?= $type ?>][in_app]" value="1" <?= $pref['in_app'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <td>
                            <label class="toggle-switch">
                                <input type="checkbox" name="prefs[<?= $type ?>][email]" value="1" <?= $pref['email'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="prefs-footer">
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-check" style="margin-right: 6px;"></i><?= $t['save_preferences'] ?>
                </button>
            </div>
        </div>
    </form>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
