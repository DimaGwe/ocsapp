<?php
$currentPage = 'training';
include __DIR__ . '/layout-header.php';
?>
<style>
.mod-breadcrumb { display:flex; align-items:center; gap:8px; font-size:13px; color:#6b7280; margin-bottom:20px; }
.mod-breadcrumb a { color:#3b82f6; text-decoration:none; font-weight:500; }
.mod-breadcrumb i { font-size:10px; }
.content-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:28px 32px; margin-bottom:24px; }
.content-card h2 { font-size:1.3rem; font-weight:800; color:#111827; margin:0 0 6px; }
.content-card .mod-meta { font-size:13px; color:#9ca3af; margin-bottom:20px; }
.content-body { font-size:15px; line-height:1.8; color:#374151; }
.content-body p { margin:0 0 14px; }
.content-body p:last-child { margin-bottom:0; }
.content-body h2 { font-size:1.1rem; font-weight:700; color:#111827; margin:28px 0 10px; padding-top:4px; border-top:1px solid #f3f4f6; }
.content-body h2:first-child { margin-top:0; border-top:none; padding-top:0; }
.content-body h3 { font-size:1rem; font-weight:700; color:#374151; margin:18px 0 8px; }
.content-body ul, .content-body ol { padding-left:22px; margin:0 0 14px; }
.content-body li { margin-bottom:7px; line-height:1.65; }
.content-body li:last-child { margin-bottom:0; }
.content-body strong { color:#111827; }
.content-body em { color:#4b5563; }
.content-body blockquote { border-left:4px solid #3b82f6; padding:12px 16px; background:#eff6ff; margin:18px 0; border-radius:0 8px 8px 0; color:#1e40af; font-size:14px; line-height:1.65; }
.read-done-bar { display:flex; justify-content:flex-end; padding-top:20px; border-top:1px solid #f3f4f6; margin-top:24px; }
/* Quiz styles */
.quiz-header { background:linear-gradient(135deg,#1d4ed8,#3b82f6); border-radius:14px; padding:22px 28px; color:#fff; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; gap:16px; }
.quiz-header h2 { font-size:1.1rem; font-weight:800; margin:0; }
.quiz-header p { font-size:13px; opacity:.85; margin:4px 0 0; }
.q-counter { font-size:2rem; font-weight:900; opacity:.6; }
.question-block { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:22px 24px; margin-bottom:16px; }
.q-num { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; margin-bottom:6px; }
.q-text { font-size:15px; font-weight:700; color:#111827; margin-bottom:16px; line-height:1.5; }
.option-label { display:flex; align-items:center; gap:12px; padding:11px 16px; border:2px solid #e5e7eb; border-radius:9px; cursor:pointer; font-size:14px; color:#374151; transition:all .15s; margin-bottom:8px; }
.option-label:hover { border-color:#93c5fd; background:#eff6ff; }
.option-label input[type=radio] { display:none; }
.option-label.selected { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; font-weight:600; }
.option-dot { width:20px; height:20px; border-radius:50%; border:2px solid #d1d5db; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .15s; }
.option-label.selected .option-dot { border-color:#3b82f6; background:#3b82f6; }
.option-label.selected .option-dot::after { content:''; width:8px; height:8px; border-radius:50%; background:#fff; }
.quiz-submit-bar { display:flex; justify-content:flex-end; gap:10px; margin-top:8px; }
/* Buttons */
.btn { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:all .15s; line-height:1; }
.btn-primary { background:#2563eb; color:#fff; }
.btn-primary:hover { background:#1d4ed8; }
.btn-primary:disabled { background:#93c5fd; cursor:not-allowed; }
.btn-secondary { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; }
.btn-secondary:hover { background:#e5e7eb; }
.btn-success { background:#16a34a; color:#fff; }
.btn-success:hover { background:#15803d; }
/* Results styles */
.result-hero { border-radius:14px; padding:28px 32px; color:#fff; text-align:center; margin-bottom:24px; }
.result-hero.passed { background:linear-gradient(135deg,#15803d,#22c55e); }
.result-hero.failed { background:linear-gradient(135deg,#b91c1c,#ef4444); }
.result-score { font-size:4rem; font-weight:900; line-height:1; margin:8px 0; }
.result-label { font-size:1rem; font-weight:700; opacity:.9; }
.result-sub { font-size:13px; opacity:.8; margin-top:4px; }
.q-result-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px 20px; margin-bottom:10px; }
.q-result-card.correct { border-left:4px solid #22c55e; }
.q-result-card.wrong { border-left:4px solid #ef4444; }
.q-result-icon { font-size:1rem; margin-right:6px; }
.q-result-text { font-size:14px; font-weight:700; color:#111827; margin-bottom:6px; }
.q-result-answer { font-size:13px; color:#6b7280; margin-bottom:4px; }
.q-explanation { font-size:13px; color:#92400e; background:#fef3c7; padding:8px 12px; border-radius:6px; margin-top:8px; }
</style>

<!-- Breadcrumb -->
<div class="mod-breadcrumb">
    <a href="<?= url('delivery/training') ?>"><i class="fas fa-graduation-cap"></i> <?php echo $fr ? 'Formation' : 'Training'; ?></a>
    <i class="fas fa-chevron-right"></i>
    <span><?php echo $fr ? 'Module' : 'Module'; ?> <?= $module['order_num'] ?>: <?= htmlspecialchars($module['title']) ?></span>
    <?php if ($phase === 'quiz'): ?>
        <i class="fas fa-chevron-right"></i>
        <span><?php echo $fr ? 'Quiz' : 'Quiz'; ?></span>
    <?php elseif ($phase === 'results'): ?>
        <i class="fas fa-chevron-right"></i>
        <span><?php echo $fr ? 'Résultats' : 'Results'; ?></span>
    <?php endif; ?>
</div>

<?php if ($phase === 'read'): ?>
<!-- ===================== READ PHASE ===================== -->
<div class="content-card">
    <h2><?= htmlspecialchars($module['title']) ?></h2>
    <div class="mod-meta">
        <i class="fas fa-book-open"></i> <?php echo $fr ? 'Matériel de formation' : 'Training Material'; ?> &nbsp;·&nbsp;
        <i class="fas fa-question-circle"></i> <?= count($questions) ?> <?php echo $fr ? 'questions de quiz après' : 'quiz questions after'; ?>
        &nbsp;·&nbsp;
        <i class="fas fa-bullseye"></i> <?php echo $fr ? 'Score de passage :' : 'Pass score:'; ?> <?= $module['pass_score'] ?>%
    </div>

    <?php if (!empty($module['content_html'])): ?>
        <div class="content-body">
            <?= $module['content_html'] ?>
        </div>
    <?php else: ?>
        <div style="text-align:center; padding:40px 0; color:#9ca3af;">
            <i class="fas fa-file-alt" style="font-size:2rem; margin-bottom:10px; display:block;"></i>
            <div style="font-weight:600; margin-bottom:4px;"><?php echo $fr ? 'Contenu à venir' : 'Content coming soon'; ?></div>
            <div style="font-size:13px;"><?php echo $fr ? 'L\'administrateur prépare encore ce module. Revenez plus tard.' : 'The admin is still writing this module. Check back later.'; ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($questions)): ?>
    <div class="read-done-bar">
        <a href="<?= url('delivery/training/module?id=' . $module['id'] . '&phase=quiz') ?>" class="btn btn-primary">
            <i class="fas fa-pencil-alt"></i> <?php echo $fr ? 'J\'ai lu - Passer le quiz' : 'I\'ve Read This - Take the Quiz'; ?>
        </a>
    </div>
    <?php else: ?>
    <div class="read-done-bar">
        <span style="font-size:13px; color:#9ca3af;"><?php echo $fr ? 'Pas encore de questions de quiz - revenez bientôt.' : 'No quiz questions yet - check back soon.'; ?></span>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($phase === 'quiz'): ?>
<!-- ===================== QUIZ PHASE ===================== -->
<div class="quiz-header">
    <div>
        <h2><i class="fas fa-pencil-alt"></i> Quiz — <?= htmlspecialchars($module['title']) ?></h2>
        <p><?php echo $fr ? 'Répondez à toutes les questions et soumettez. Vous avez besoin de ' . $module['pass_score'] . '% pour réussir.' : 'Answer all questions and submit. You need ' . $module['pass_score'] . '% to pass.'; ?></p>
    </div>
    <div class="q-counter"><?= count($questions) ?>Q</div>
</div>

<?php if (!empty($attemptsLeft)): ?>
<div style="background:#fef3c7; border-left:4px solid #f59e0b; color:#92400e; padding:11px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;">
    <i class="fas fa-exclamation-triangle"></i>
    <?php echo $fr ? $attemptsLeft . ' tentative' . ($attemptsLeft === 1 ? '' : 's') . ' restante' . ($attemptsLeft === 1 ? '' : 's') . ' pour ce module.' : $attemptsLeft . ' attempt' . ($attemptsLeft === 1 ? '' : 's') . ' remaining for this module.'; ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= url('delivery/training/quiz/submit') ?>" id="quizForm" novalidate onsubmit="return validateQuiz()">
    <?= csrfField() ?>
    <input type="hidden" name="module_id" value="<?= $module['id'] ?>">

    <?php foreach ($questions as $i => $q): ?>
    <div class="question-block" id="qblock-<?= $q['id'] ?>">
        <div class="q-num"><?php echo $fr ? 'Question ' . ($i + 1) . ' de ' . count($questions) : 'Question ' . ($i + 1) . ' of ' . count($questions); ?></div>
        <div class="q-text"><?= htmlspecialchars($q['question_text']) ?></div>
        <?php foreach (['a','b','c','d'] as $opt): ?>
            <?php if (!empty($q['option_' . $opt])): ?>
            <label class="option-label" onclick="selectOption(this)">
                <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $opt ?>" required>
                <span class="option-dot"></span>
                <span><?= strtoupper($opt) ?>. <?= htmlspecialchars($q['option_' . $opt]) ?></span>
            </label>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <div class="quiz-submit-bar">
        <a href="<?= url('delivery/training/module?id=' . $module['id']) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> <?php echo $fr ? 'Retour à la lecture' : 'Back to Reading'; ?>
        </a>
        <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-paper-plane"></i> <?php echo $fr ? 'Soumettre le quiz' : 'Submit Quiz'; ?>
        </button>
    </div>
</form>

<?php elseif ($phase === 'results'): ?>
<!-- ===================== RESULTS PHASE ===================== -->
<?php
$passed  = $results['passed'];
$score   = $results['score'];
$correct = $results['correct'];
$total   = $results['total'];
$answers = $results['answers']; // ['question_id' => 'a'/'b'/'c'/'d']
?>

<div class="result-hero <?= $passed ? 'passed' : 'failed' ?>">
    <div style="font-size:1rem; font-weight:700; opacity:.8; text-transform:uppercase; letter-spacing:.05em;">
        <?php echo $passed ? ($fr ? 'Félicitations !' : 'Congratulations!') : ($fr ? 'Pas tout à fait - Continuez !' : 'Not Quite - Keep Trying!'); ?>
    </div>
    <div class="result-score"><?= $score ?>%</div>
    <div class="result-label"><?php echo $fr ? $correct . ' de ' . $total . ' correctes' : $correct . ' of ' . $total . ' correct'; ?></div>
    <div class="result-sub"><?php echo $fr ? 'Score de passage :' : 'Pass score:'; ?> <?= $module['pass_score'] ?>%</div>
</div>

<?php if ($passed && !empty($results['next_module_id'])): ?>
<div style="background:#dcfce7; border-left:4px solid #22c55e; border-radius:8px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; gap:16px;">
    <div>
        <div style="font-weight:700; color:#15803d;"><i class="fas fa-unlock"></i> <?php echo $fr ? 'Module suivant déverrouillé !' : 'Next module unlocked!'; ?></div>
        <div style="font-size:13px; color:#166534; margin-top:2px;"><?php echo $fr ? 'Le module ' . $results['next_module_order'] . ' est maintenant disponible.' : 'Module ' . $results['next_module_order'] . ' is now available.'; ?></div>
    </div>
    <a href="<?= url('delivery/training/module?id=' . $results['next_module_id']) ?>" class="btn btn-success" style="flex-shrink:0;">
        <i class="fas fa-arrow-right"></i> <?php echo $fr ? 'Module suivant' : 'Next Module'; ?>
    </a>
</div>
<?php elseif ($passed && empty($results['next_module_id'])): ?>
<div style="background:#dcfce7; border-left:4px solid #22c55e; border-radius:8px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; gap:16px;">
    <div>
        <div style="font-weight:700; color:#15803d;"><i class="fas fa-certificate"></i> <?php echo $fr ? 'Tous les modules sont complétés - vous êtes certifié !' : 'All modules complete - you\'re certified!'; ?></div>
        <div style="font-size:13px; color:#166634; margin-top:2px;"><?php echo $fr ? 'Vous pouvez maintenant accepter des livraisons.' : 'You can now accept deliveries.'; ?></div>
    </div>
    <a href="<?= url('delivery/training/certificate') ?>" class="btn btn-success" style="flex-shrink:0;">
        <i class="fas fa-download"></i> <?php echo $fr ? 'Voir le certificat' : 'View Certificate'; ?>
    </a>
</div>
<?php elseif (!$passed && $results['attempts_left'] > 0): ?>
<div style="background:#fef3c7; border-left:4px solid #f59e0b; border-radius:8px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; gap:16px;">
    <div>
        <div style="font-weight:700; color:#92400e;"><i class="fas fa-redo"></i> <?php echo $fr ? 'Réessayer' : 'Try again'; ?></div>
        <div style="font-size:13px; color:#92400e; margin-top:2px;">
            <?php
            $al = $results['attempts_left'];
            echo $fr ? $al . ' tentative' . ($al === 1 ? '' : 's') . ' restante' . ($al === 1 ? '' : 's') . '.' : $al . ' attempt' . ($al === 1 ? '' : 's') . ' remaining.';
            ?>
        </div>
    </div>
    <a href="<?= url('delivery/training/module?id=' . $module['id'] . '&phase=quiz') ?>" class="btn btn-warning" style="flex-shrink:0;">
        <i class="fas fa-redo"></i> <?php echo $fr ? 'Réessayer le quiz' : 'Retry Quiz'; ?>
    </a>
</div>
<?php else: ?>
<div style="background:#fee2e2; border-left:4px solid #ef4444; border-radius:8px; padding:14px 18px; margin-bottom:20px;">
    <div style="font-weight:700; color:#991b1b;"><i class="fas fa-ban"></i> <?php echo $fr ? 'Nombre maximal de tentatives atteint' : 'Maximum attempts reached'; ?></div>
    <div style="font-size:13px; color:#991b1b; margin-top:2px;"><?php echo $fr ? 'Contactez votre administrateur pour réinitialiser ce module.' : 'Contact your admin to reset this module.'; ?></div>
</div>
<?php endif; ?>

<!-- Per-question breakdown -->
<div style="font-size:14px; font-weight:700; color:#374151; margin-bottom:10px;"><?php echo $fr ? 'Révision des questions' : 'Question Review'; ?></div>
<?php foreach ($questions as $i => $q):
    $givenAnswer = $answers[$q['id']] ?? null;
    $isCorrect   = $givenAnswer === $q['correct_option'];
?>
<div class="q-result-card <?= $isCorrect ? 'correct' : 'wrong' ?>">
    <div class="q-result-text">
        <i class="fas <?= $isCorrect ? 'fa-check-circle' : 'fa-times-circle' ?> q-result-icon" style="color:<?= $isCorrect ? '#22c55e' : '#ef4444' ?>;"></i>
        <?= ($i+1) ?>. <?= htmlspecialchars($q['question_text']) ?>
    </div>
    <?php if (!$isCorrect): ?>
    <div class="q-result-answer">
        <?php echo $fr ? 'Votre réponse :' : 'Your answer:'; ?> <strong><?= strtoupper($givenAnswer ?? '-') ?>. <?= htmlspecialchars($q['option_' . ($givenAnswer ?? 'a')] ?? ($fr ? 'Sans réponse' : 'No answer')) ?></strong>
    </div>
    <div class="q-result-answer" style="color:#15803d;">
        <?php echo $fr ? 'Bonne réponse :' : 'Correct answer:'; ?> <strong><?= strtoupper($q['correct_option']) ?>. <?= htmlspecialchars($q['option_' . $q['correct_option']]) ?></strong>
    </div>
    <?php if (!empty($q['explanation'])): ?>
    <div class="q-explanation"><i class="fas fa-lightbulb"></i> <?= htmlspecialchars($q['explanation']) ?></div>
    <?php endif; ?>
    <?php else: ?>
    <div class="q-result-answer" style="color:#15803d;">
        <i class="fas fa-check" style="color:#22c55e;"></i>
        <?= strtoupper($q['correct_option']) ?>. <?= htmlspecialchars($q['option_' . $q['correct_option']]) ?>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<div style="display:flex; justify-content:flex-end; margin-top:16px;">
    <a href="<?= url('delivery/training') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> <?php echo $fr ? 'Retour au tableau de formation' : 'Back to Training Dashboard'; ?>
    </a>
</div>

<?php endif; ?>

<script>
function selectOption(label) {
    const block = label.closest('.question-block');
    block.querySelectorAll('.option-label').forEach(l => l.classList.remove('selected'));
    label.classList.add('selected');
    label.querySelector('input[type=radio]').checked = true;
}

function validateQuiz() {
    const form = document.getElementById('quizForm');
    const blocks = form.querySelectorAll('.question-block');
    for (const block of blocks) {
        const radios = block.querySelectorAll('input[type=radio]');
        const answered = [...radios].some(r => r.checked);
        if (!answered) {
            block.scrollIntoView({ behavior: 'smooth', block: 'center' });
            block.style.borderColor = '#ef4444';
            block.style.boxShadow = '0 0 0 3px rgba(239,68,68,.15)';
            setTimeout(() => { block.style.borderColor = ''; block.style.boxShadow = ''; }, 2000);
            alert('<?php echo $fr ? 'Veuillez répondre à toutes les questions avant de soumettre.' : 'Please answer all questions before submitting.'; ?>');
            return false;
        }
    }
    // Defer disable so the browser commits the submit before the button state changes
    setTimeout(function() {
        var btn = document.getElementById('submitBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo $fr ? 'Envoi...' : 'Submitting...'; ?>';
        }
    }, 30);
    return true;
}
</script>

<?php include __DIR__ . '/layout-footer.php'; ?>
