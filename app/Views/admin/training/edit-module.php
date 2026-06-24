<?php
$currentPage = 'training';
ob_start();
?>
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">

<style>
.edit-section { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:24px; margin-bottom:24px; }
.edit-section h3 { font-size:1rem; font-weight:700; color:#111827; margin:0 0 16px; padding-bottom:12px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:8px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input, .form-select, .form-textarea { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; transition:border-color .2s; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
.question-card { border:1px solid #e5e7eb; border-radius:8px; padding:14px 16px; margin-bottom:10px; background:#fafafa; }
.question-card:hover { background:#f5f5f5; }
.q-header { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
.q-text { font-size:14px; font-weight:600; color:#111827; flex:1; }
.q-options { font-size:13px; color:#6b7280; margin-top:6px; display:grid; grid-template-columns:1fr 1fr; gap:4px 16px; }
.q-correct { color:#16a34a; font-weight:600; }
.q-wrong { color:#6b7280; }
.add-question-form { border:2px dashed #d1d5db; border-radius:10px; padding:20px; margin-top:12px; }
.add-question-form.active { border-color:#3b82f6; background:#eff6ff; }
.option-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.correct-radio { display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; }
.correct-radio input { width:16px; height:16px; accent-color:#16a34a; }
#quill-editor { min-height:300px; font-size:15px; }
.ql-container { font-family: inherit; }
</style>

<!-- Breadcrumb -->
<div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; color:#6b7280;">
    <a href="<?= url('admin/training') ?>" style="color:#3b82f6; text-decoration:none; font-weight:500;">
        <i class="fas fa-graduation-cap"></i> Training
    </a>
    <i class="fas fa-chevron-right" style="font-size:10px;"></i>
    <span>Module <?= $module['order_num'] ?>: <?= htmlspecialchars($module['title']) ?></span>
</div>

<!-- Module Settings -->
<div class="edit-section">
    <h3><i class="fas fa-cog" style="color:#3b82f6;"></i> Module Settings</h3>
    <form method="POST" action="<?= url('admin/training/module/save') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="module_id" value="<?= $module['id'] ?>">

        <div style="margin-bottom:14px;">
            <label class="form-label">Module Title <span style="color:#ef4444;">*</span></label>
            <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($module['title']) ?>" required>
        </div>
        <div style="margin-bottom:14px;">
            <label class="form-label">Description (shown on training dashboard)</label>
            <textarea name="description" class="form-textarea" rows="2"><?= htmlspecialchars($module['description'] ?? '') ?></textarea>
        </div>
        <div class="form-grid" style="margin-bottom:16px;">
            <div>
                <label class="form-label">Pass Score (%)</label>
                <input type="number" name="pass_score" class="form-input" min="1" max="100" value="<?= $module['pass_score'] ?>">
                <p style="font-size:12px; color:#9ca3af; margin-top:4px;">Minimum percentage to pass (default: 80%)</p>
            </div>
            <div>
                <label class="form-label">Max Attempts</label>
                <input type="number" name="max_attempts" class="form-input" min="1" max="10" value="<?= $module['max_attempts'] ?>">
                <p style="font-size:12px; color:#9ca3af; margin-top:4px;">After this, driver must contact admin</p>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </form>
</div>

<!-- Reading Content Editor -->
<div class="edit-section">
    <h3><i class="fas fa-file-alt" style="color:#3b82f6;"></i> Reading Content</h3>
    <p style="font-size:13px; color:#6b7280; margin:-8px 0 16px;">
        Write the training material drivers must read before taking the quiz. Use headings, lists, and bold text to make it scannable.
    </p>
    <form id="contentForm" method="POST" action="<?= url('admin/training/module/save') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="module_id" value="<?= $module['id'] ?>">
        <input type="hidden" name="title" value="<?= htmlspecialchars($module['title']) ?>">
        <input type="hidden" name="description" value="<?= htmlspecialchars($module['description'] ?? '') ?>">
        <input type="hidden" name="pass_score" value="<?= $module['pass_score'] ?>">
        <input type="hidden" name="max_attempts" value="<?= $module['max_attempts'] ?>">
        <input type="hidden" name="content_html" id="contentHtmlInput">
        <div id="quill-editor"><?= $module['content_html'] ?? '' ?></div>
        <div style="margin-top:16px; display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary" onclick="document.getElementById('contentHtmlInput').value = quill.root.innerHTML;">
                <i class="fas fa-save"></i> Save Content
            </button>
        </div>
    </form>
</div>

<!-- Question Manager -->
<div class="edit-section">
    <h3><i class="fas fa-question-circle" style="color:#3b82f6;"></i> Quiz Questions (<?= count($questions) ?>)</h3>

    <?php if (empty($questions)): ?>
        <p style="color:#9ca3af; text-align:center; padding:20px 0;">No questions yet. Add your first question below.</p>
    <?php else: ?>
        <div id="questionList">
        <?php foreach ($questions as $i => $q): ?>
        <div class="question-card" id="qcard-<?= $q['id'] ?>">
            <div class="q-header">
                <div class="q-text"><?= ($i+1) ?>. <?= htmlspecialchars($q['question_text']) ?></div>
                <div style="display:flex; gap:6px; flex-shrink:0;">
                    <button type="button" onclick="editQuestion(<?= htmlspecialchars(json_encode($q)) ?>)"
                        style="background:#eff6ff; color:#3b82f6; border:1px solid #bfdbfe; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600;">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form method="POST" action="<?= url('admin/training/question/delete') ?>" onsubmit="return confirm('Delete this question?')" style="display:inline;">
                        <?= csrfField() ?>
                        <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                        <input type="hidden" name="module_id" value="<?= $module['id'] ?>">
                        <button type="submit" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="q-options">
                <?php foreach (['a','b','c','d'] as $opt): ?>
                    <?php if (!empty($q['option_' . $opt])): ?>
                    <div class="<?= $q['correct_option'] === $opt ? 'q-correct' : 'q-wrong' ?>">
                        <?= $q['correct_option'] === $opt ? '✓' : '○' ?> <?= strtoupper($opt) ?>: <?= htmlspecialchars($q['option_' . $opt]) ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($q['explanation'])): ?>
            <div style="margin-top:8px; font-size:12px; color:#6b7280; font-style:italic; border-top:1px solid #e5e7eb; padding-top:6px;">
                <i class="fas fa-lightbulb" style="color:#f59e0b;"></i> <?= htmlspecialchars($q['explanation']) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Add/Edit Question Form -->
    <div class="add-question-form" id="addQuestionForm">
        <h4 style="margin:0 0 14px; font-size:14px; font-weight:700; color:#1d4ed8;" id="formTitle">
            <i class="fas fa-plus-circle"></i> Add New Question
        </h4>
        <form method="POST" action="<?= url('admin/training/question/save') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="module_id" value="<?= $module['id'] ?>">
            <input type="hidden" name="question_id" id="editQuestionId" value="0">
            <input type="hidden" name="order_num" value="<?= count($questions) + 1 ?>">

            <div style="margin-bottom:12px;">
                <label class="form-label">Question <span style="color:#ef4444;">*</span></label>
                <textarea name="question_text" id="qText" class="form-textarea" rows="2" placeholder="Enter the quiz question..." required></textarea>
            </div>
            <div class="option-grid" style="margin-bottom:12px;">
                <div>
                    <label class="form-label">Option A <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="option_a" id="optA" class="form-input" placeholder="First option" required>
                </div>
                <div>
                    <label class="form-label">Option B <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="option_b" id="optB" class="form-input" placeholder="Second option" required>
                </div>
                <div>
                    <label class="form-label">Option C</label>
                    <input type="text" name="option_c" id="optC" class="form-input" placeholder="Third option (optional)">
                </div>
                <div>
                    <label class="form-label">Option D</label>
                    <input type="text" name="option_d" id="optD" class="form-input" placeholder="Fourth option (optional)">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label class="form-label">Correct Answer <span style="color:#ef4444;">*</span></label>
                <div style="display:flex; gap:20px; margin-top:4px;">
                    <?php foreach (['a' => 'Option A', 'b' => 'Option B', 'c' => 'Option C', 'd' => 'Option D'] as $val => $lbl): ?>
                    <label class="correct-radio">
                        <input type="radio" name="correct_option" id="correct_<?= $val ?>" value="<?= $val ?>" <?= $val === 'a' ? 'required' : '' ?>>
                        <?= $lbl ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">Explanation (shown after wrong answer)</label>
                <textarea name="explanation" id="qExplanation" class="form-textarea" rows="2" placeholder="Optional: explain why the correct answer is right..."></textarea>
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button type="button" onclick="resetQuestionForm()" class="btn btn-secondary btn-sm">Reset</button>
                <button type="submit" class="btn btn-primary btn-sm" id="saveQuestionBtn">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<script>
// Init Quill
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'header': [2, 3, false] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['clean']
        ]
    }
});

// Edit existing question
function editQuestion(q) {
    document.getElementById('editQuestionId').value = q.id;
    document.getElementById('qText').value = q.question_text;
    document.getElementById('optA').value = q.option_a || '';
    document.getElementById('optB').value = q.option_b || '';
    document.getElementById('optC').value = q.option_c || '';
    document.getElementById('optD').value = q.option_d || '';
    document.getElementById('correct_' + q.correct_option).checked = true;
    document.getElementById('qExplanation').value = q.explanation || '';
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Question';
    document.getElementById('saveQuestionBtn').innerHTML = '<i class="fas fa-save"></i> Save Changes';
    document.getElementById('addQuestionForm').classList.add('active');
    document.getElementById('addQuestionForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function resetQuestionForm() {
    document.getElementById('editQuestionId').value = '0';
    document.getElementById('qText').value = '';
    document.getElementById('optA').value = '';
    document.getElementById('optB').value = '';
    document.getElementById('optC').value = '';
    document.getElementById('optD').value = '';
    document.getElementById('correct_a').checked = true;
    document.getElementById('qExplanation').value = '';
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle"></i> Add New Question';
    document.getElementById('saveQuestionBtn').innerHTML = '<i class="fas fa-plus"></i> Add Question';
    document.getElementById('addQuestionForm').classList.remove('active');
}
</script>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
