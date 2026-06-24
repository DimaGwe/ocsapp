<?php
// Include auth popup for non-logged-in users
include __DIR__ . '/auth-popup.php';

// Shared newsletter signup band (multi-list, bilingual)
include __DIR__ . '/newsletter-signup.php';
?>

<footer class="footer">
  <div class="footer-grid">
    <div>
      <h4><?= $t['get_to_know'] ?? 'Get to Know Us' ?></h4>
      <ul>
        <li><a href="<?= url('about') ?>"><?= $t['about_us'] ?? 'About Us' ?></a></li>
        <li><a href="<?= url('contact') ?>"><?= $t['contact_us'] ?? 'Contact Us' ?></a></li>
      </ul>
    </div>
    <div>
      <h4><?= $t['promote_with_us'] ?? 'Promote with Us' ?></h4>
      <ul>
        <li><a href="<?= url('buyer-central') ?>"><?= $t['buyer_central'] ?? 'Buyer Central' ?></a></li>
        <li><a href="<?= url('seller-central') ?>"><?= $t['seller_central'] ?? 'Seller Central' ?></a></li>
        <li><a href="<?= url('supplier-central') ?>"><?= $t['supplier_central'] ?? 'Supplier Portal' ?></a></li>
        <li><a href="<?= url('driver-central') ?>"><?= $t['driver_central'] ?? 'Driver Central' ?></a></li>
        <li><a href="<?= url('distribution') ?>"><?= $t['business_central'] ?? 'Business Central' ?></a></li>
      </ul>
    </div>
    <div>
      <h4><?= $t['connect_with_us'] ?? 'Connect with Us' ?></h4>
      <ul>
        <li><a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener">Facebook</a></li>
        <li><a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener">Instagram</a></li>
        <li><a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener">LinkedIn</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>OCSAPP © <?= date('Y') ?>. <?= $t['all_rights'] ?? 'All rights reserved' ?></p>
    <div class="footer-links">
      <a href="<?= url('privacy') ?>"><?= $t['privacy'] ?? 'Privacy' ?></a>
      <a href="<?= url('terms') ?>"><?= $t['terms'] ?? 'Terms' ?></a>
      <a href="<?= url('cookies') ?>"><?= $t['cookies'] ?? 'Cookies' ?></a>
      <a href="<?= url('returns') ?>"><?= $t['returns'] ?? 'Returns' ?></a>
      <a href="<?= url('accessibility') ?>"><?= ($currentLang ?? 'en') === 'fr' ? 'Accessibilité' : 'Accessibility' ?></a>
    </div>
  </div>
</footer>
<script>
document.querySelectorAll('[data-auto-dismiss]').forEach(function(el) {
    setTimeout(function() {
        el.style.opacity = '0';
        setTimeout(function() { el.style.display = 'none'; }, 600);
    }, 4000);
});
</script>
