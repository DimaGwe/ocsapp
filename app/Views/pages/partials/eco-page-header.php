<?php
/**
 * Shared eco-header + beta strip for standalone info pages (onboarding guides, founding).
 * Needs: $fr (bool), $navLinks (list of [url, label]; About is appended).
 */
$navLinks = array_merge($navLinks ?? [], [[url('about'), $fr ? 'À propos' : 'About']]);
?>
<header class="eco-header">
  <div class="eco-wrap eco-header-inner">
    <a class="eco-brand" href="<?= url('') ?>" aria-label="<?= $fr ? 'OCSAPP - Accueil' : 'OCSAPP - Home' ?>">
      <img src="<?= asset('images/logo.png') ?>" alt="Logo OCSAPP">
      <span class="eco-brand-text">OCSAPP</span>
    </a>
    <nav aria-label="<?= $fr ? 'Navigation principale' : 'Main navigation' ?>">
      <?php foreach ($navLinks as [$navUrl, $navLabel]): ?>
      <a class="eco-nav-link" href="<?= $navUrl ?>"><?= htmlspecialchars($navLabel) ?></a>
      <?php endforeach; ?>
      <div class="eco-lang" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
        <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>">FR</a>
        <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>">EN</a>
      </div>
      <a class="eco-btn eco-btn-secondary" href="<?= url('login') ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> <?= $fr ? 'Se connecter' : 'Sign in' ?></a>
      <button type="button" class="eco-mobile-toggle" id="navToggle" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenu">
        <i class="fa-solid fa-bars"></i>
      </button>
    </nav>
  </div>
  <div class="eco-wrap">
    <div class="eco-mobile-menu" id="mobileMenu">
      <?php foreach ($navLinks as [$navUrl, $navLabel]): ?>
      <a class="eco-mobile-menu-link" href="<?= $navUrl ?>"><?= htmlspecialchars($navLabel) ?></a>
      <?php endforeach; ?>
      <a class="eco-mobile-menu-link" href="<?= url('login') ?>"><?= $fr ? 'Se connecter' : 'Sign in' ?></a>
      <div class="eco-mobile-menu-lang">
        <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>">FR</a>
        <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>">EN</a>
      </div>
    </div>
  </div>
</header>

<div class="beta-strip"><p><?= $fr
  ? "Version bêta : veuillez ne pas effectuer d'achats réels pour le moment"
  : 'Beta version: please do not make real purchases at this time' ?></p></div>
