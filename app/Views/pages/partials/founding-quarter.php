<?php
/**
 * Founding Quarter sections for pages/founding.php. Renders one piece per include:
 * $fqSection = 'band' (countdown + total spots) | 'wall' (Founders' Wall) | 'scripts' (toast + JS).
 * Needs: $fr, $programs, $quarter ['founders' => FoundersWallHelper::founders(), 'end' => ISO date or null].
 * The countdown only renders with a real end date (setting founding_quarter_end); the wall only lists
 * founders who consented. Styles: css/pages/founding-quarter.css (fq- classes). No em dashes. FR is fr-CA.
 */
$fqRoles = [
    'buyer'    => [['Acheteur fondateur', 'Founding Buyer'],       ['Acheteurs', 'Buyers']],
    'seller'   => [['Vendeur fondateur', 'Founding Seller'],       ['Vendeurs', 'Sellers']],
    'supplier' => [['Fournisseur fondateur', 'Founding Supplier'], ['Fournisseurs', 'Suppliers']],
    'driver'   => [['Livreur fondateur', 'Founding Driver'],       ['Livreurs', 'Drivers']],
    'business' => [['Entreprise fondatrice', 'Founding Business'], ['Entreprises', 'Businesses']],
];
$fqT = fn(array $pair) => $fr ? $pair[0] : $pair[1];
$fqAgo = function (int $d) use ($fr): string {
    if ($d === 0) return $fr ? "aujourd'hui" : 'today';
    if ($d === 1) return $fr ? 'hier' : 'yesterday';
    return $fr ? "il y a $d jours" : "$d days ago";
};
$fqInitials = function (string $name): string {
    $words = array_filter(preg_split('/[\s\-]+/u', trim($name)), fn($w) => mb_strlen($w) > 2 || mb_substr($w, -1) === '.');
    $letters = array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)), array_slice(array_values($words), 0, 2));
    return implode('', $letters) ?: mb_strtoupper(mb_substr($name, 0, 1));
};
$fqFounders = $quarter['founders'] ?? [];
$fqTotal = array_sum(array_column($programs, 'total'));
$fqLeft  = array_sum(array_column($programs, 'remaining'));
$fqPct   = $fqTotal ? round(100 * ($fqTotal - $fqLeft) / $fqTotal) : 0;
$fqWeek  = count(array_filter($fqFounders, fn($f) => $f['daysAgo'] <= 6));
$fqEnd   = !empty($quarter['end']) ? new DateTime($quarter['end']) : null;
$fqEndLabel = !$fqEnd ? '' : ($fr
    ? $fqEnd->format('j') . ' ' . ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'][(int) $fqEnd->format('n') - 1] . ' ' . $fqEnd->format('Y')
    : $fqEnd->format('F j, Y'));
?>
<?php if ($fqSection === 'band'): ?>
<!-- FOUNDING QUARTER: countdown + total spots left -->
<section class="fq-band-sec" id="quarter">
  <div class="fd-wrap fq-hero-grid">
    <div>
      <div class="fd-eyebrow fq-eyebrow"><span class="fq-live-dot"></span><?= $fr ? 'TRIMESTRE FONDATEUR · EN COURS' : 'FOUNDING QUARTER · NOW OPEN' ?></div>
      <h2><?= $fr ? 'Les places fondatrices partent. Réservez la vôtre.' : 'Founding spots are going. Claim yours.' ?></h2>
      <p><?= $fr
        ? "Pendant le Trimestre fondateur, chaque rôle a un nombre limité de places. Une fois complet, le programme ferme pour de bon. Voyez qui a déjà réservé sa place et combien il en reste."
        : 'During the Founding Quarter, every role has a limited number of spots. Once a program is full, it closes for good. See who has already joined and how many spots are left.' ?></p>
      <?php if ($fqEnd): ?>
      <div class="fq-countdown" data-end="<?= htmlspecialchars($quarter['end']) ?>" aria-label="<?= $fr ? 'Temps restant au Trimestre fondateur' : 'Time left in the Founding Quarter' ?>">
        <div class="fq-cd-cell"><strong data-cd="d">--</strong><span><?= $fr ? 'jours' : 'days' ?></span></div>
        <div class="fq-cd-cell"><strong data-cd="h">--</strong><span><?= $fr ? 'heures' : 'hours' ?></span></div>
        <div class="fq-cd-cell"><strong data-cd="m">--</strong><span>min</span></div>
        <div class="fq-cd-cell"><strong data-cd="s">--</strong><span>sec</span></div>
      </div>
      <p class="fq-cd-note"><?= $fr ? 'Fin du Trimestre fondateur : ' : 'Founding Quarter ends ' ?><?= $fqEndLabel ?></p>
      <?php endif; ?>
    </div>
    <div class="fq-total-card">
      <div class="fq-total-num"><strong class="fq-count" data-from="<?= $fqTotal ?>" data-to="<?= $fqLeft ?>"><?= $fqLeft ?></strong><span>/ <?= $fqTotal ?></span></div>
      <div class="fq-total-label"><?= $fr ? 'places fondatrices restantes, tous rôles confondus' : 'founding spots left across all roles' ?></div>
      <div class="fq-bar fq-bar-lg"><span style="width:<?= $fqPct ?>%"></span></div>
      <div class="fq-total-meta">
        <span><?php if ($fqWeek > 0): ?><i class="fa-solid fa-user-plus"></i> <?= $fqWeek ?> <?= $fr ? ($fqWeek > 1 ? 'nouveaux fondateurs cette semaine' : 'nouveau fondateur cette semaine') : ($fqWeek > 1 ? 'new founders this week' : 'new founder this week') ?><?php endif; ?></span>
        <span><?= $fqPct ?><?= $fr ? ' % réservé' : '% claimed' ?></span>
      </div>
      <div class="fq-total-links">
        <a href="#programs"><?= $fr ? 'Voir les programmes' : 'See the programs' ?> ↓</a>
        <a href="#founders"><?= $fr ? 'Voir les fondateurs' : 'See the founders' ?> ↓</a>
      </div>
    </div>
  </div>
</section>

<?php elseif ($fqSection === 'wall'): ?>
<!-- FOUNDERS' WALL -->
<section class="fq-wall-sec" id="founders">
  <div class="fd-wrap">
    <div class="fq-wall-head">
      <div>
        <div class="fd-eyebrow"><?= $fr ? 'LE MUR DES FONDATEURS' : "THE FOUNDERS' WALL" ?></div>
        <h2><?= $fr ? 'Ils ont déjà réservé leur place.' : 'They already claimed their spot.' ?></h2>
        <p><?= $fr
          ? "Les membres fondateurs qui ont accepté d'être affichés. Les particuliers apparaissent avec leur prénom et l'initiale de leur nom."
          : 'Founding members who agreed to be shown. Individuals appear with their first name and last initial.' ?></p>
      </div>
      <div class="fq-filters" aria-label="<?= $fr ? 'Filtrer par rôle' : 'Filter by role' ?>">
        <button type="button" class="fq-chip active" data-filter="all"><?= $fr ? 'Tous' : 'All' ?> <em><?= count($fqFounders) ?></em></button>
        <?php foreach ($fqRoles as $role => [$name, $short]): ?>
        <button type="button" class="fq-chip" data-filter="<?= $role ?>"><?= htmlspecialchars($fqT($short)) ?> <em><?= count(array_filter($fqFounders, fn($f) => $f['role'] === $role)) ?></em></button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="fq-wall">
      <?php if (!$fqFounders): ?>
      <div class="fq-wall-empty">
        <i class="fa-solid fa-seedling"></i>
        <div><strong><?= $fr ? 'Le mur vous attend.' : 'The wall is waiting for you.' ?></strong>
          <?= $fr ? "Les premiers membres fondateurs qui acceptent d'être affichés apparaîtront ici." : 'The first founding members who agree to be shown will appear here.' ?></div>
      </div>
      <?php endif; ?>
      <?php foreach ($fqFounders as $f): ?>
      <div class="fq-founder fq-r-<?= $f['role'] ?><?= $f['daysAgo'] === 0 ? ' fq-new' : '' ?>" data-role="<?= $f['role'] ?>">
        <div class="fq-avatar"><?= htmlspecialchars($fqInitials($f['name'])) ?></div>
        <div class="fq-founder-body">
          <div class="fq-founder-name"><?= htmlspecialchars($f['name']) ?></div>
          <div class="fq-founder-role"><?= htmlspecialchars($fqT($fqRoles[$f['role']][0])) ?> <b>#<?= (int) $f['number'] ?></b></div>
          <div class="fq-founder-meta"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($f['city']) ?> · <?= $fqAgo($f['daysAgo']) ?></div>
        </div>
        <?php if ($f['daysAgo'] === 0): ?><span class="fq-new-tag"><?= $fr ? 'Nouveau' : 'New' ?></span><?php endif; ?>
      </div>
      <?php endforeach; ?>
      <a class="fq-founder fq-you" href="#programs">
        <div class="fq-avatar"><i class="fa-solid fa-plus"></i></div>
        <div class="fq-founder-body">
          <div class="fq-founder-name"><?= $fr ? 'Votre nom ici' : 'Your name here' ?></div>
          <div class="fq-founder-role"><?= $fr ? 'Choisissez votre programme' : 'Choose your program' ?> ↑</div>
        </div>
      </a>
    </div>
  </div>
</section>

<?php elseif ($fqSection === 'scripts'): ?>
<!-- "just joined" toast -->
<div class="fq-toast" id="fqToast" aria-live="polite" hidden>
  <div class="fq-avatar fq-avatar-sm" id="fqToastAvatar"></div>
  <div><strong id="fqToastName"></strong> <span id="fqToastText"></span><small id="fqToastWhen"></small></div>
</div>
<script>
(function () {
  var cd = document.querySelector('.fq-countdown');
  if (cd) {
    var end = new Date(cd.dataset.end).getTime();
    var pad = function (n) { return String(n).padStart(2, '0'); };
    var tick = function () {
      var s = Math.max(0, Math.floor((end - Date.now()) / 1000));
      cd.querySelector('[data-cd="d"]').textContent = Math.floor(s / 86400);
      cd.querySelector('[data-cd="h"]').textContent = pad(Math.floor(s % 86400 / 3600));
      cd.querySelector('[data-cd="m"]').textContent = pad(Math.floor(s % 3600 / 60));
      cd.querySelector('[data-cd="s"]').textContent = pad(s % 60);
    };
    tick(); setInterval(tick, 1000);
  }

  // Counters tick DOWN from the cohort size to the spots left when they scroll into view
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var run = function (el) {
    var from = +el.dataset.from, to = +el.dataset.to, t0 = null, dur = 1400;
    if (reduce || from === to) { el.textContent = to; return; }
    var step = function (ts) {
      if (!t0) t0 = ts;
      var k = Math.min(1, (ts - t0) / dur), e = 1 - Math.pow(1 - k, 3);
      el.textContent = Math.round(from - (from - to) * e);
      if (k < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { run(en.target); io.unobserve(en.target); } });
    }, { threshold: .4 });
    document.querySelectorAll('.fq-count').forEach(function (el) { el.textContent = el.dataset.from; io.observe(el); });
  }

  var chips = document.querySelectorAll('.fq-chip');
  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      chips.forEach(function (c) { c.classList.toggle('active', c === chip); });
      document.querySelectorAll('.fq-wall [data-role]').forEach(function (card) {
        card.hidden = chip.dataset.filter !== 'all' && card.dataset.role !== chip.dataset.filter;
      });
    });
  });

  var recent = <?= json_encode(array_map(fn($f) => [
      'name' => $f['name'], 'initials' => $fqInitials($f['name']),
      'text' => ($fr ? 'de ' . $f['city'] . ' a rejoint le programme ' : 'from ' . $f['city'] . ' joined as a ') . $fqT($fqRoles[$f['role']][0]),
      'when' => $fqAgo($f['daysAgo']),
  ], array_slice($fqFounders, 0, 6)), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>;
  var toast = document.getElementById('fqToast'), i = 0;
  if (toast && recent.length && !reduce) {
    var show = function () {
      var f = recent[i++ % recent.length];
      document.getElementById('fqToastAvatar').textContent = f.initials;
      document.getElementById('fqToastName').textContent = f.name;
      document.getElementById('fqToastText').textContent = f.text;
      document.getElementById('fqToastWhen').textContent = f.when;
      toast.hidden = false; toast.classList.add('show');
      setTimeout(function () { toast.classList.remove('show'); }, 4200);
    };
    setTimeout(function () { show(); setInterval(show, 9000); }, 2500);
  }
})();
</script>
<?php endif; ?>
