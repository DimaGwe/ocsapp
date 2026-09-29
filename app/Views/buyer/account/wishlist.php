<?php
/**
 * Buyer wishlist (/account/wishlist, AccountController::wishlist)
 * Updated 2026-09-28: Marché Central theme via the shared account partials, bilingual EN/FR.
 * NOTE: the controller is a stub (always passes an empty list, no wishlist table read), so only the
 * empty state shows today. The grid is kept for when it is built; the dead "Add to Cart" button
 * (no handler) became a link to the product.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$wishlist = $wishlist ?? [];
$user = $user ?? user() ?? [];
$accountActive = 'wishlist';

$acctTitle   = $fr ? 'Liste de souhaits' : 'Wishlist';
$acctHeading = $acctTitle;
$acctCrumb   = [$acctTitle, 'fa-heart'];
$acctSub     = $fr ? 'Les produits que vous voulez retrouver facilement.' : 'Products you want to find again easily.';
require __DIR__ . '/partials/account-top.php';
?>
                    <?php if (empty($wishlist)): ?>
                        <section class="acct-card acct-panel">
                            <div class="acct-empty">
                                <i class="fas fa-heart"></i>
                                <p><?= $fr ? 'Votre liste de souhaits est vide. Enregistrez les produits que vous aimez pour les retrouver plus tard.' : 'Your wishlist is empty. Save products you love to find them later.' ?></p>
                                <a href="<?= url('marketplace-central') ?>" class="acct-btn acct-btn-primary"><?= $fr ? 'Découvrir les produits' : 'Browse products' ?></a>
                            </div>
                        </section>
                    <?php else: ?>
                        <div class="acct-wish-grid">
                            <?php foreach ($wishlist as $item): ?>
                                <article class="acct-card acct-wish">
                                    <div class="acct-wish-img">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?= htmlspecialchars(url(ltrim($item['image_path'], '/'))) ?>" alt="<?= htmlspecialchars($item['name'] ?? '') ?>" loading="lazy">
                                        <?php else: ?>
                                            <i class="fas fa-image"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="acct-wish-body">
                                        <strong><?= htmlspecialchars($item['name'] ?? '') ?></strong>
                                        <span class="acct-wish-price"><?= acct_money($item['price'] ?? 0, $fr) ?></span>
                                        <div class="acct-wish-actions">
                                            <?php if (!empty($item['slug'])): ?>
                                                <a href="<?= url('product/' . rawurlencode($item['slug'])) ?>" class="acct-btn acct-btn-primary acct-btn-sm"><?= $fr ? 'Voir le produit' : 'View product' ?></a>
                                            <?php endif; ?>
                                            <form method="POST" action="<?= url('account/wishlist/remove') ?>">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                                <button type="submit" class="acct-btn acct-btn-danger acct-btn-sm" aria-label="<?= $fr ? 'Retirer' : 'Remove' ?>"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
<?php require __DIR__ . '/partials/account-bottom.php'; ?>
</body>
</html>
