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
        <li><a href="<?= url('seller-central') ?>"><?= $t['seller_central'] ?? 'Seller Central' ?></a></li>
        <li><a href="<?= url('vendor-central') ?>"><?= $t['vendor_central'] ?? 'Vendor Central' ?></a></li>
      </ul>
    </div>
    <div>
      <h4><?= $t['connect_with_us'] ?? 'Connect with Us' ?></h4>
      <ul>
        <li><a href="#">Facebook</a></li>
        <li><a href="#">Twitter</a></li>
        <li><a href="#">Instagram</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>OCSAPP © <?= date('Y') ?>. <?= $t['all_rights'] ?? 'All rights reserved' ?></p>
    <div class="footer-links">
      <a href="<?= url('privacy') ?>"><?= $t['privacy'] ?? 'Privacy' ?></a>
      <a href="<?= url('terms') ?>"><?= $t['terms'] ?? 'Terms' ?></a>
      <a href="<?= url('cookies') ?>"><?= $t['cookies'] ?? 'Cookies' ?></a>
    </div>
  </div>
</footer>
