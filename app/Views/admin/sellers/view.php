<?php
/**
 * OCS Admin Seller Details View
 * File: app/Views/admin/sellers/view.php
 */

$pageTitle = 'Seller Details';
$currentPage = 'sellers';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'seller_details' => 'Seller Details',
        'back_to_sellers' => 'Back to Sellers',
        'overview' => 'Overview',
        'shops' => 'Shops',
        'products' => 'Products',
        'total_stock' => 'Total Stock',
        'contact_information' => 'Contact Information',
        'email' => 'Email',
        'phone' => 'Phone',
        'status' => 'Status',
        'joined_date' => 'Joined Date',
        'seller_shops' => 'Seller Shops',
        'shop_name' => 'Shop Name',
        'shop_status' => 'Status',
        'created' => 'Created',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'recent_inventory' => 'Recent Inventory',
        'product' => 'Product',
        'shop' => 'Shop',
        'stock' => 'Stock',
        'price' => 'Price',
        'no_shops' => 'This seller has no shops yet',
        'no_inventory' => 'This seller has no inventory yet',
        'view_all' => 'View All',
    ],
    'fr' => [
        'seller_details' => 'Détails du Vendeur',
        'back_to_sellers' => 'Retour aux Vendeurs',
        'overview' => 'Aperçu',
        'shops' => 'Boutiques',
        'products' => 'Produits',
        'total_stock' => 'Stock Total',
        'contact_information' => 'Informations de Contact',
        'email' => 'Email',
        'phone' => 'Téléphone',
        'status' => 'Statut',
        'joined_date' => 'Date d\'inscription',
        'seller_shops' => 'Boutiques du Vendeur',
        'shop_name' => 'Nom de la Boutique',
        'shop_status' => 'Statut',
        'created' => 'Créé',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'recent_inventory' => 'Inventaire Récent',
        'product' => 'Produit',
        'shop' => 'Boutique',
        'stock' => 'Stock',
        'price' => 'Prix',
        'no_shops' => 'Ce vendeur n\'a pas encore de boutiques',
        'no_inventory' => 'Ce vendeur n\'a pas encore d\'inventaire',
        'view_all' => 'Voir Tout',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  .view-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .view-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    color: var(--gray-700);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all var(--transition-base);
  }

  .back-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 8px;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--primary);
  }

  .content-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 24px;
    margin-bottom: 32px;
  }

  .info-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
  }

  .info-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 20px;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
  }

  .info-row:last-child {
    border-bottom: none;
  }

  .info-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-600);
  }

  .info-value {
    font-size: 14px;
    color: var(--dark);
  }

  .section-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .section-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  td {
    padding: 16px;
    border-top: 1px solid var(--border);
    font-size: 14px;
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.active { background: #dcfce7; color: #166534; }
  .badge.inactive { background: var(--gray-200); color: var(--gray-700); }
  .badge.suspended { background: #fee2e2; color: #991b1b; }

  .empty-state {
    text-align: center;
    padding: 48px 24px;
    color: var(--gray-500);
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    color: var(--gray-300);
  }

  @media (max-width: 768px) {
    .content-grid {
      grid-template-columns: 1fr;
    }

    .view-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
    }
  }
</style>

<!-- Page Header -->
<div class="view-header">
  <h1><?= $t['seller_details'] ?></h1>
  <a href="<?= url('admin/sellers') ?>" class="back-btn">
    <i class="fas fa-arrow-left"></i> <?= $t['back_to_sellers'] ?>
  </a>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label"><?= $t['shops'] ?></div>
    <div class="stat-value"><?= $stats['total_shops'] ?? 0 ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $t['products'] ?></div>
    <div class="stat-value"><?= $stats['total_products'] ?? 0 ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $t['total_stock'] ?></div>
    <div class="stat-value"><?= number_format($stats['total_stock'] ?? 0) ?></div>
  </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
  <!-- Contact Information -->
  <div class="info-card">
    <h3><?= $t['contact_information'] ?></h3>
    <?php if (!empty($seller['avatar'])): ?>
      <div style="text-align:center;margin-bottom:20px;">
        <img src="<?= htmlspecialchars('https://ocsapp.ca/' . ltrim($seller['avatar'], '/')) ?>"
             alt="<?= htmlspecialchars($seller['first_name']) ?>"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
      </div>
    <?php else: ?>
      <div style="text-align:center;margin-bottom:20px;">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;margin:0 auto;">
          <?= strtoupper(substr($seller['first_name'], 0, 1)) ?>
        </div>
      </div>
    <?php endif; ?>
    <div class="info-row">
      <span class="info-label"><?= $t['email'] ?></span>
      <span class="info-value"><?= htmlspecialchars($seller['email']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label"><?= $t['phone'] ?></span>
      <span class="info-value"><?= htmlspecialchars($seller['phone'] ?? 'N/A') ?></span>
    </div>
    <div class="info-row">
      <span class="info-label"><?= $t['status'] ?></span>
      <span class="info-value">
        <span class="badge <?= $seller['status'] ?>">
          <?= ucfirst($seller['status']) ?>
        </span>
      </span>
    </div>
    <div class="info-row">
      <span class="info-label"><?= $t['joined_date'] ?></span>
      <span class="info-value"><?= formatDate($seller['created_at'], 'M d, Y') ?></span>
    </div>
  </div>

  <!-- Overview -->
  <div class="info-card">
    <h3><?= $t['overview'] ?></h3>
    <div class="info-row">
      <span class="info-label">Name</span>
      <span class="info-value">
        <?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?>
      </span>
    </div>
    <div class="info-row">
      <span class="info-label">Active Shops</span>
      <span class="info-value"><?= $stats['active_shops'] ?? 0 ?> / <?= $stats['total_shops'] ?? 0 ?></span>
    </div>
  </div>
</div>

<!-- Seller Shops -->
<div class="section-card">
  <div class="section-header">
    <h3><?= $t['seller_shops'] ?></h3>
  </div>

  <?php if (!empty($shops)): ?>
    <table>
      <thead>
        <tr>
          <th><?= $t['shop_name'] ?></th>
          <th><?= $t['shop_status'] ?></th>
          <th><?= $t['created'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($shops as $shop): ?>
          <tr>
            <td><?= htmlspecialchars($shop['name']) ?></td>
            <td>
              <span class="badge <?= $shop['is_active'] ? 'active' : 'inactive' ?>">
                <?= $shop['is_active'] ? $t['active'] : $t['inactive'] ?>
              </span>
            </td>
            <td><?= formatDate($shop['created_at'], 'M d, Y') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-store"></i>
      <p><?= $t['no_shops'] ?></p>
    </div>
  <?php endif; ?>
</div>

<!-- Recent Inventory -->
<div class="section-card">
  <div class="section-header">
    <h3><?= $t['recent_inventory'] ?></h3>
  </div>

  <?php if (!empty($inventory)): ?>
    <table>
      <thead>
        <tr>
          <th><?= $t['product'] ?></th>
          <th><?= $t['shop'] ?></th>
          <th><?= $t['stock'] ?></th>
          <th><?= $t['price'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($inventory as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= htmlspecialchars($item['shop_name']) ?></td>
            <td><?= number_format($item['stock_quantity']) ?></td>
            <td>$<?= number_format($item['price'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-box"></i>
      <p><?= $t['no_inventory'] ?></p>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
