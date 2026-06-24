<?php
$currentPage = 'planner';
ob_start();
?>

<style>
    .db-container {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: calc(100vh - 200px);
        padding: 28px 32px 48px 32px;
    }

    .db-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }

    .db-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: white;
        transition: all 0.2s;
    }
    .db-back:hover { color: #00b207; border-color: #00b207; background: #f0fdf4; }

    .db-welcome {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .db-avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00b207 0%, #009906 100%);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 20px;
        box-shadow: 0 4px 12px rgba(124,58,237,0.3);
    }
    .db-welcome-text h1 {
        font-size: 22px; font-weight: 700; color: #1e293b;
        margin: 0 0 2px 0;
    }
    .db-welcome-text p { font-size: 13px; color: #94a3b8; margin: 0; }

    .db-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: start;
    }

    .db-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .db-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px 16px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .db-card-header h3 {
        font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .db-count-badge {
        font-size: 12px; font-weight: 700;
        background: #f1f5f9; color: #64748b;
        padding: 2px 8px; border-radius: 10px;
    }

    .db-card-body { padding: 20px 24px; }

    /* Quick add task */
    .quick-add-task {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
    }
    .quick-add-task input {
        flex: 1;
        padding: 11px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        background: #fafafa;
        transition: all 0.2s;
    }
    .quick-add-task input:focus { outline: none; border-color: #00b207; background: white; box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
    .quick-add-task select {
        padding: 11px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 13px;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.2s;
    }
    .quick-add-task select:focus { outline: none; border-color: #00b207; }

    /* Task items */
    .db-task-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s;
    }
    .db-task-item:last-child { border-bottom: none; }
    .db-task-item:hover { background: #fafbfc; margin: 0 -24px; padding: 14px 24px; }

    .db-task-check {
        width: 20px; height: 20px;
        margin-top: 2px;
        cursor: pointer;
        accent-color: #00b207;
        flex-shrink: 0;
    }
    .db-task-content { flex: 1; min-width: 0; }
    .db-task-text {
        font-size: 14px; color: #334155; line-height: 1.5; margin-bottom: 5px;
        word-break: break-word;
    }
    .db-task-text.done { text-decoration: line-through; color: #9ca3af; }
    .db-task-meta { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
    .db-task-del {
        background: none; border: none; color: #d1d5db;
        cursor: pointer; font-size: 16px; padding: 2px 6px;
        border-radius: 4px; flex-shrink: 0; margin-top: 2px;
        transition: all 0.15s;
    }
    .db-task-del:hover { color: #ef4444; background: #fef2f2; }

    /* Priority badges (reuse from planner) */
    .priority-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; border-radius: 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .priority-badge.urgent { background: #fee2e2; color: #b91c1c; }
    .priority-badge.high   { background: #ffedd5; color: #c2410c; }
    .priority-badge.medium { background: #dbeafe; color: #1d4ed8; }
    .priority-badge.low    { background: #f1f5f9; color: #64748b; }

    .assigned-by { font-size: 11px; color: #94a3b8; }

    /* Notes compact */
    .db-note-item {
        padding: 12px 0;
        border-bottom: 1px solid #f8fafc;
        font-size: 13px; color: #475569; line-height: 1.6;
    }
    .db-note-item:last-child { border-bottom: none; }
    .db-note-item-footer {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 6px;
    }
    .db-note-time { font-size: 11px; color: #94a3b8; }
    .db-note-del {
        background: none; border: none; color: #d1d5db; cursor: pointer;
        font-size: 13px; padding: 2px 6px; border-radius: 4px; transition: all 0.15s;
    }
    .db-note-del:hover { color: #ef4444; background: #fef2f2; }

    /* Quick note */
    .quick-note-form { margin-bottom: 16px; }
    .quick-note-input {
        width: 100%; padding: 10px 12px;
        border: 2px solid #e5e7eb; border-radius: 10px;
        font-size: 13px; font-family: inherit;
        background: #fafafa; resize: vertical; min-height: 70px;
        box-sizing: border-box; transition: all 0.2s;
    }
    .quick-note-input:focus { outline: none; border-color: #00b207; background: white; box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }

    /* Activity */
    .db-activity-item {
        padding: 10px 12px;
        border-left: 3px solid #e5e7eb;
        background: #fafbfc;
        border-radius: 0 6px 6px 0;
        margin-bottom: 8px;
        font-size: 13px; color: #475569;
    }
    .db-activity-time { font-size: 11px; color: #94a3b8; margin-top: 3px; }

    /* Buttons */
    .btn-db-primary {
        padding: 10px 18px; border: none; border-radius: 8px; cursor: pointer;
        font-size: 13px; font-weight: 600; color: white;
        background: linear-gradient(135deg, #00b207, #009906);
        box-shadow: 0 3px 8px rgba(0,178,7,0.25);
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-db-primary:hover { background: linear-gradient(135deg, #009906, #008005); transform: translateY(-1px); }
    .btn-db-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .btn-db-purple {
        padding: 10px 18px; border: none; border-radius: 8px; cursor: pointer;
        font-size: 13px; font-weight: 600; color: white;
        background: linear-gradient(135deg, #00b207 0%, #009906 100%);
        box-shadow: 0 3px 8px rgba(0,178,7,0.25);
        transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
        width: 100%; justify-content: center;
    }
    .btn-db-purple:hover { background: linear-gradient(135deg, #009906, #008005); transform: translateY(-1px); }
    .btn-db-purple:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .db-empty { text-align: center; padding: 28px 16px; color: #94a3b8; font-size: 13px; }

    @media (max-width: 768px) {
        .db-container { padding: 16px 12px 32px 12px; }
        .db-grid { grid-template-columns: 1fr; }
        .db-topbar { flex-direction: column; align-items: flex-start; gap: 14px; }
        .quick-add-task { flex-wrap: wrap; }
    }
</style>

<div class="db-container">

    <div class="db-topbar">
        <a href="<?= url('admin/planner') ?>" class="db-back">← Team Planner</a>
        <div class="db-welcome">
            <div class="db-avatar" id="dbAvatar"></div>
            <div class="db-welcome-text">
                <h1 id="dbWelcomeTitle">My Dashboard</h1>
                <p id="dbWelcomeSub">Your personal workspace</p>
            </div>
        </div>
    </div>

    <div class="db-grid">

        <!-- Left: My Tasks -->
        <div>
            <div class="db-card">
                <div class="db-card-header">
                    <h3>✓ My Tasks</h3>
                    <span class="db-count-badge" id="myTaskCount">-</span>
                </div>
                <div class="db-card-body">
                    <div class="quick-add-task">
                        <input type="text" id="myTaskInput" placeholder="New task for yourself..."
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addMyTask();}">
                        <select id="myTaskPriority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <button class="btn-db-primary" onclick="addMyTask()">Add</button>
                    </div>

                    <div id="myTasksList"><div class="db-empty">Loading...</div></div>
                </div>
            </div>
        </div>

        <!-- Right: Notes + Activity -->
        <div>
            <div class="db-card">
                <div class="db-card-header">
                    <h3>🔒 Personal Notes</h3>
                    <span class="db-count-badge" id="myNoteCount">-</span>
                </div>
                <div class="db-card-body">
                    <div class="quick-note-form">
                        <textarea id="myNoteInput" class="quick-note-input" placeholder="Private note — only you can see this..."></textarea>
                        <button class="btn-db-purple" onclick="addMyNote()" id="addMyNoteBtn" style="margin-top:8px;">
                            Save Note
                        </button>
                    </div>
                    <div id="myNotesList"><div class="db-empty">Loading...</div></div>
                </div>
            </div>

            <div class="db-card">
                <div class="db-card-header">
                    <h3>📊 My Activity</h3>
                </div>
                <div class="db-card-body" style="padding-top:12px;">
                    <div id="myActivityList"><div class="db-empty">Loading...</div></div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const API_URL = '<?= url('api/planner') ?>';
    const currentUser = {
        id:        <?= (int)($_SESSION['user']['id'] ?? 0) ?>,
        firstName: '<?= htmlspecialchars($_SESSION['user']['first_name'] ?? '', ENT_QUOTES) ?>',
        lastName:  '<?= htmlspecialchars($_SESSION['user']['last_name'] ?? '', ENT_QUOTES) ?>'
    };

    const priorityLabels = { urgent: '🔴 Urgent', high: '🟠 High', medium: '🔵 Medium', low: '⚪ Low' };

    function getInitials(name) {
        if (!name) return '?';
        return name.split(' ').map(p => p[0]).join('').toUpperCase().substring(0, 2);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '';
        const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
        if (diff < 60) return 'just now';
        if (diff < 3600) return Math.floor(diff/60) + 'm ago';
        if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
        return Math.floor(diff/86400) + 'd ago';
    }

    function fetchOpts(method, body) {
        return {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: body ? JSON.stringify(body) : undefined
        };
    }

    // ── Bootstrap ──────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const fullName = `${currentUser.firstName} ${currentUser.lastName}`.trim();
        document.getElementById('dbAvatar').textContent = getInitials(fullName);
        document.getElementById('dbWelcomeTitle').textContent = `Hi, ${currentUser.firstName || 'there'}`;
        document.getElementById('dbWelcomeSub').textContent = 'Your personal workspace';
        loadMyTasks();
        loadMyNotes();
        loadMyActivity();
    });

    // ── My Tasks ───────────────────────────────────────────────
    async function loadMyTasks() {
        try {
            const res = await fetch(`${API_URL}/todos?mine=1`);
            const todos = await res.json();
            renderMyTasks(todos);
        } catch (e) {
            document.getElementById('myTasksList').innerHTML = '<div class="db-empty">Failed to load tasks.</div>';
        }
    }

    function renderMyTasks(todos) {
        const active = todos.filter(t => !t.is_completed);
        const done   = todos.filter(t =>  t.is_completed);

        document.getElementById('myTaskCount').textContent = `${active.length} active`;

        if (todos.length === 0) {
            document.getElementById('myTasksList').innerHTML = '<div class="db-empty">No tasks yet — add one above!</div>';
            return;
        }

        const renderItem = t => {
            const priority = t.priority || 'medium';
            const createdByMe = parseInt(t.user_id) === currentUser.id;
            const assignedLabel = !createdByMe
                ? `<span class="assigned-by">from ${escapeHtml(t.creator_name)}</span>`
                : (t.assigned_name ? `<span class="assigned-by">self-assigned</span>` : '');
            return `
                <div class="db-task-item" id="dbtask-${t.id}">
                    <input type="checkbox" class="db-task-check" ${t.is_completed ? 'checked' : ''} onchange="toggleMyTask(${t.id})">
                    <div class="db-task-content">
                        <div class="db-task-text ${t.is_completed ? 'done' : ''}">${escapeHtml(t.task)}</div>
                        <div class="db-task-meta">
                            <span class="priority-badge ${priority}">${priorityLabels[priority]}</span>
                            ${assignedLabel}
                            <span style="font-size:11px;color:#94a3b8;">${timeAgo(t.created_at)}</span>
                        </div>
                    </div>
                    <button class="db-task-del" onclick="deleteMyTask(${t.id})" title="Delete">×</button>
                </div>`;
        };

        let html = active.map(renderItem).join('');
        if (done.length > 0) {
            html += `<div style="font-size:12px;font-weight:600;color:#94a3b8;margin:16px 0 8px;">Completed (${done.length})</div>`;
            html += done.map(renderItem).join('');
        }
        document.getElementById('myTasksList').innerHTML = html;
    }

    async function addMyTask() {
        const input = document.getElementById('myTaskInput');
        const task = input.value.trim();
        if (!task) return;

        const priority = document.getElementById('myTaskPriority').value || 'medium';
        try {
            const res = await fetch(`${API_URL}/todos`, fetchOpts('POST', {
                user_id:     currentUser.id,
                task:        task,
                priority:    priority,
                assigned_to: currentUser.id
            }));
            if (res.ok) {
                input.value = '';
                document.getElementById('myTaskPriority').value = 'medium';
                loadMyTasks();
            }
        } catch (e) { console.error(e); }
    }

    async function toggleMyTask(id) {
        try {
            await fetch(`${API_URL}/todos`, fetchOpts('PUT', { id, user_id: currentUser.id }));
            loadMyTasks();
        } catch (e) { console.error(e); }
    }

    async function deleteMyTask(id) {
        if (!confirm('Delete this task?')) return;
        try {
            await fetch(`${API_URL}/todos`, fetchOpts('DELETE', { id, user_id: currentUser.id }));
            loadMyTasks();
        } catch (e) { console.error(e); }
    }

    // ── Personal Notes ─────────────────────────────────────────
    async function loadMyNotes() {
        try {
            const res = await fetch(`${API_URL}/notes?archived=0&scope=personal`);
            const data = await res.json();
            const notes = data.notes || [];
            document.getElementById('myNoteCount').textContent = `${notes.length}`;
            if (notes.length === 0) {
                document.getElementById('myNotesList').innerHTML = '<div class="db-empty">No personal notes yet.</div>';
                return;
            }
            document.getElementById('myNotesList').innerHTML = notes.map(n => `
                <div class="db-note-item">
                    <div>${escapeHtml(n.content).replace(/\n/g, '<br>')}</div>
                    <div class="db-note-item-footer">
                        <span class="db-note-time">${timeAgo(n.created_at)}</span>
                        <button class="db-note-del" onclick="deleteMyNote(${n.id})" title="Delete">×</button>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            document.getElementById('myNotesList').innerHTML = '<div class="db-empty">Failed to load notes.</div>';
        }
    }

    async function addMyNote() {
        const input = document.getElementById('myNoteInput');
        const content = input.value.trim();
        if (!content) return;

        const btn = document.getElementById('addMyNoteBtn');
        btn.disabled = true;

        try {
            const res = await fetch(`${API_URL}/notes`, fetchOpts('POST', {
                user_id: currentUser.id,
                content: content,
                scope:   'personal'
            }));
            if (res.ok) {
                input.value = '';
                loadMyNotes();
            }
        } catch (e) { console.error(e); }
        finally { btn.disabled = false; }
    }

    async function deleteMyNote(id) {
        if (!confirm('Delete this note?')) return;
        try {
            await fetch(`${API_URL}/notes`, fetchOpts('DELETE', { id, user_id: currentUser.id }));
            loadMyNotes();
        } catch (e) { console.error(e); }
    }

    // ── My Activity ────────────────────────────────────────────
    async function loadMyActivity() {
        try {
            const res = await fetch(`${API_URL}/activity?mine=1`);
            const activity = await res.json();
            if (!activity.length) {
                document.getElementById('myActivityList').innerHTML = '<div class="db-empty">No activity yet.</div>';
                return;
            }
            document.getElementById('myActivityList').innerHTML = activity.map(a => `
                <div class="db-activity-item">
                    <div>You ${escapeHtml(a.description)}</div>
                    <div class="db-activity-time">${timeAgo(a.created_at)}</div>
                </div>
            `).join('');
        } catch (e) {
            document.getElementById('myActivityList').innerHTML = '<div class="db-empty">Failed to load activity.</div>';
        }
    }
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
