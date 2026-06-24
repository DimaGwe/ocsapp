<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$pageTitle = $t['sup_dashboard'] ?? 'Dashboard';
require __DIR__ . '/layout-header.php';
?>

<?php if ($isPendingVerification): ?>
<!-- ═══════════════════════════════════════════════════════ -->
<!--  PENDING SUPPLIER DASHBOARD                            -->
<!-- ═══════════════════════════════════════════════════════ -->
<style>
    .pending-hero {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 36px 40px;
        color: #14532d;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 28px;
    }
    .pending-hero-icon {
        width: 72px; height: 72px; border-radius: 50%;
        background: #bbf7d0; color: #15803d;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; flex-shrink: 0;
    }
    .pending-hero h1 { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: #14532d; }
    .pending-hero p  { font-size: 14px; line-height: 1.6; margin: 0; color: #166534; }
    .pending-status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: #bbf7d0; color: #15803d; padding: 5px 14px;
        border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 14px;
    }
    .pending-status-pill i { font-size: 10px; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

    .pending-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;
    }
    .steps-card, .info-card {
        background: white; border-radius: 14px;
        padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .steps-card h3, .info-card h3 {
        font-size: 15px; font-weight: 700; color: #111827;
        margin-bottom: 22px; display: flex; align-items: center; gap: 8px;
    }
    .steps-card h3 i, .info-card h3 i { color: #00b207; }

    .step-item { display: flex; gap: 16px; margin-bottom: 20px; }
    .step-item:last-child { margin-bottom: 0; }
    .step-number {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; flex-shrink: 0;
    }
    .step-number.done   { background: #d1fae5; color: #059669; }
    .step-number.active { background: #d1fae5; color: #059669; }
    .step-number.todo   { background: #f3f4f6; color: #9ca3af; }
    .step-connector { width: 2px; height: 16px; background: #e5e7eb; margin: 4px 0 4px 15px; }
    .step-body { flex: 1; }
    .step-title { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 3px; }
    .step-desc  { font-size: 12px; color: #6b7280; line-height: 1.5; }

    .info-row {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding: 12px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-key { color: #6b7280; font-size: 13px; }
    .info-val { font-weight: 600; color: #111827; text-align: right; max-width: 55%; word-break: break-word; }
    .info-val.status-pending {
        display: inline-flex; align-items: center; gap: 5px;
        background: #d1fae5; color: #059669;
        padding: 2px 10px; border-radius: 12px; font-size: 12px;
    }

    .docs-cta {
        background: white; border-radius: 14px; padding: 28px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 24px; margin-bottom: 24px;
    }
    .docs-cta-icon {
        width: 64px; height: 64px; border-radius: 14px;
        background: #f0fdf4; color: #00b207;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; flex-shrink: 0;
    }
    .docs-cta-body h3 { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 6px; }
    .docs-cta-body p  { font-size: 13px; color: #6b7280; margin-bottom: 14px; line-height: 1.5; }
    .btn-docs {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 22px; background: #00b207; color: white;
        border-radius: 8px; font-size: 14px; font-weight: 600;
        text-decoration: none; transition: background 0.2s;
    }
    .btn-docs:hover { background: #009906; }

    .contact-strip {
        background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 12px;
        padding: 16px 24px; display: flex; align-items: center;
        justify-content: space-between; gap: 16px; flex-wrap: wrap;
        font-size: 13px; color: #374151;
    }
    .contact-strip a { color: #00b207; font-weight: 600; text-decoration: none; }
    .contact-strip a:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        .pending-hero { flex-direction: column; text-align: center; padding: 28px 24px; }
        .pending-grid { grid-template-columns: 1fr; }
        .docs-cta { flex-direction: column; text-align: center; }
    }
</style>

<?php
$_daysLeft = $GLOBALS['_supplierDaysLeft'] ?? null;
$_daysUrgent = $_daysLeft !== null && $_daysLeft <= 7;
?>
<!-- Hero -->
<div class="pending-hero">
    <div class="pending-hero-icon"><i class="fas fa-hourglass-half"></i></div>
    <div style="flex:1;">
        <h1><?= $currentLang === 'fr' ? 'Bienvenue, ' : 'Welcome, ' ?><?= htmlspecialchars($supplier['company_name'] ?? ($supplier['first_name'] ?? '')) ?>!</h1>
        <p>
            <?= $currentLang === 'fr'
                ? 'Votre compte est en cours de vérification. Téléversez vos documents pour accélérer l\'approbation - vous recevrez un courriel une fois approuvé.'
                : 'Your account is under review. Upload your verification documents to speed up approval - you\'ll receive an email once approved.' ?>
        </p>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:14px;">
            <div class="pending-status-pill">
                <i class="fas fa-circle"></i>
                <?= $currentLang === 'fr' ? 'Vérification en cours' : 'Verification In Progress' ?>
            </div>
            <?php if ($_daysLeft !== null): ?>
            <div style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:<?= $_daysUrgent ? '#fef2f2' : '#bbf7d0' ?>;color:<?= $_daysUrgent ? '#dc2626' : '#15803d' ?>;">
                <i class="fas fa-calendar-alt"></i>
                <?= $_daysLeft ?> <?= $currentLang === 'fr' ? 'jours restants' : 'days left' ?>
            </div>
            <?php endif; ?>
            <a href="<?= url('supplier/documents') ?>" style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:#065f46;color:white;text-decoration:none;">
                <i class="fas fa-upload"></i>
                <?= $currentLang === 'fr' ? 'Télécharger mes documents' : 'Upload Documents' ?>
            </a>
            <a href="mailto:info@ocsapp.ca" style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:rgba(0,0,0,0.06);color:#14532d;text-decoration:none;">
                <i class="fas fa-envelope"></i> info@ocsapp.ca
            </a>
        </div>
    </div>
</div>

<!-- Two-column: Steps + Account Info -->
<div class="pending-grid">

    <div class="steps-card">
        <h3><i class="fas fa-list-check"></i> <?= $currentLang === 'fr' ? 'Ce qui se passe ensuite' : 'What Happens Next' ?></h3>

        <div class="step-item">
            <div><div class="step-number done"><i class="fas fa-check"></i></div><div class="step-connector"></div></div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Demande soumise' : 'Application Submitted' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Votre compte fournisseur a été créé avec succès.' : 'Your supplier account has been created successfully.' ?></div>
            </div>
        </div>

        <div class="step-item">
            <div><div class="step-number active"><i class="fas fa-search"></i></div><div class="step-connector"></div></div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Examen en cours' : 'Under Review' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Notre équipe examine votre dossier. Cela prend généralement 1 à 3 jours ouvrables.' : 'Our team is reviewing your file. This typically takes 1-3 business days.' ?></div>
            </div>
        </div>

        <div class="step-item">
            <div><div class="step-number todo">3</div><div class="step-connector"></div></div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Approbation du compte' : 'Account Approved' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Vous recevrez un courriel de confirmation avec vos accès complets.' : 'You\'ll receive a confirmation email with full portal access.' ?></div>
            </div>
        </div>

        <div class="step-item">
            <div><div class="step-number todo">4</div></div>
            <div class="step-body">
                <div class="step-title"><?= $currentLang === 'fr' ? 'Accès complet débloqué' : 'Full Access Unlocked' ?></div>
                <div class="step-desc"><?= $currentLang === 'fr' ? 'Ajoutez vos produits, gérez vos commandes et plus encore.' : 'Add products, manage purchase orders, and more.' ?></div>
            </div>
        </div>
    </div>

    <div class="info-card">
        <h3><i class="fas fa-store"></i> <?= $currentLang === 'fr' ? 'Informations du compte' : 'Account Information' ?></h3>

        <?php if (!empty($supplier['company_name'])): ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Entreprise' : 'Company' ?></span>
            <span class="info-val"><?= htmlspecialchars($supplier['company_name']) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Contact' : 'Contact' ?></span>
            <span class="info-val"><?= htmlspecialchars(trim(($supplier['first_name'] ?? '') . ' ' . ($supplier['last_name'] ?? ''))) ?></span>
        </div>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Courriel' : 'Email' ?></span>
            <span class="info-val"><?= htmlspecialchars($supplier['email'] ?? '') ?></span>
        </div>
        <?php if (!empty($supplier['phone'])): ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Téléphone' : 'Phone' ?></span>
            <span class="info-val"><?= htmlspecialchars($supplier['phone']) ?></span>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Membre depuis' : 'Member Since' ?></span>
            <span class="info-val"><?= date($currentLang === 'fr' ? 'd M Y' : 'M d, Y', strtotime($supplier['created_at'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Statut' : 'Status' ?></span>
            <span class="info-val">
                <span class="status-pending"><i class="fas fa-clock"></i> <?= $currentLang === 'fr' ? 'En attente' : 'Pending' ?></span>
            </span>
        </div>
        <?php
        $pkg = $supplier['subscription_package'] ?? 'Essential';
        $pkgColors = ['Essential'=>'#00b207','Experience'=>'#3b82f6','Prestige'=>'#7c3aed','Enterprise'=>'#1f2937'];
        $pkgColor  = $pkgColors[$pkg] ?? '#00b207';
        ?>
        <div class="info-row">
            <span class="info-key"><?= $currentLang === 'fr' ? 'Forfait' : 'Package' ?></span>
            <span class="info-val">
                <span style="display:inline-flex;align-items:center;gap:6px;background:<?= $pkgColor ?>18;color:<?= $pkgColor ?>;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;border:1px solid <?= $pkgColor ?>33;">
                    <i class="fas fa-star" style="font-size:10px;"></i> <?= htmlspecialchars($pkg) ?>
                </span>
            </span>
        </div>
    </div>

</div>


<?php else: ?>
<!-- ═══════════════════════════════════════════════════════ -->
<!--  NORMAL (APPROVED) DASHBOARD                           -->
<!-- ═══════════════════════════════════════════════════════ -->
<style>
  /* Stats Cards */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .stat-icon.primary { background: rgba(102, 126, 234, 0.1); color: var(--primary); }
  .stat-icon.success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
  .stat-icon.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-700);
  }

  .stat-label {
    font-size: 14px;
    color: var(--gray-600);
    margin-top: 4px;
  }

  /* Recent Orders */
  .card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
  }

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-700);
  }

  .btn-view-all {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
  }

  td {
    padding: 16px 12px;
    border-top: 1px solid var(--gray-200);
  }

  .badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge.draft { background: #e5e7eb; color: #374151; }
  .badge.sent { background: #dbeafe; color: #1e40af; }
  .badge.accepted { background: #fef3c7; color: #92400e; }
  .badge.preparing { background: #ede9fe; color: #5b21b6; }
  .badge.ready_for_pickup { background: #fff7ed; color: #c2410c; }
  .badge.picked_up { background: #ecfdf5; color: #065f46; }
  .badge.completed { background: #dcfce7; color: #166534; }
  .badge.cancelled { background: #fee2e2; color: #991b1b; }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-600);
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    color: var(--gray-400);
  }
</style>

<!-- Stats -->
      <div id="supplierDashboard" class="stats-grid">
        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?= $productStats['total'] ?? 0 ?></div>
              <div class="stat-label"><?= $t['sup_total_products'] ?? 'Total Products' ?></div>
            </div>
            <div class="stat-icon primary">
              <i class="fas fa-box"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?= $productStats['available'] ?? 0 ?></div>
              <div class="stat-label"><?= $t['sup_available'] ?? 'Available' ?></div>
            </div>
            <div class="stat-icon success">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?= count($recentOrders ?? []) ?></div>
              <div class="stat-label"><?= $t['sup_recent_orders'] ?? 'Recent Orders' ?></div>
            </div>
            <div class="stat-icon warning">
              <i class="fas fa-file-invoice"></i>
            </div>
          </div>
        </div>

        <?php
        $dashPkg = $supplier['subscription_package'] ?? 'Essential';
        $dashPkgColors = ['Essential'=>'#00b207','Experience'=>'#3b82f6','Prestige'=>'#7c3aed','Enterprise'=>'#1f2937'];
        $dashPkgColor  = $dashPkgColors[$dashPkg] ?? '#00b207';
        $dashCommission = $supplier['commission_rate'] ?? '12.00';
        $dashPkgLabel = ($currentLang === 'fr' && $dashPkg === 'Enterprise') ? 'Entreprise' : $dashPkg;
        ?>
        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value" style="font-size:16px;color:<?= $dashPkgColor ?>;"><?= htmlspecialchars($dashPkgLabel) ?></div>
              <div class="stat-label"><?= $currentLang === 'fr' ? 'Mon forfait' : 'My Plan' ?></div>
              <div style="font-size:11px;color:#6b7280;margin-top:4px;"><?= $dashCommission ?>% <?= $currentLang === 'fr' ? 'commission' : 'commission' ?></div>
            </div>
            <div class="stat-icon" style="background:<?= $dashPkgColor ?>18;color:<?= $dashPkgColor ?>;">
              <i class="fas fa-star"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title"><?= $t['sup_recent_orders'] ?? 'Recent Orders' ?></h2>
          <a href="<?= url('supplier/orders') ?>" class="btn-view-all"><?= $t['sup_view_all'] ?? 'View All' ?> &rarr;</a>
        </div>

        <?php if (!empty($recentOrders)): ?>
          <div style="overflow-x:auto;">
          <table>
            <thead>
              <tr>
                <th><?= $t['sup_order_number'] ?? 'Order #' ?></th>
                <th><?= $t['sup_date'] ?? 'Date' ?></th>
                <th><?= $t['sup_items'] ?? 'Items' ?></th>
                <th><?= $t['sup_total'] ?? 'Total' ?></th>
                <th><?= $t['sup_status'] ?? 'Status' ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentOrders as $order): ?>
                <tr>
                  <td>
                    <a href="<?= url('supplier/orders/view?id=' . $order['id']) ?>" style="color: var(--primary); font-weight: 600;">
                      <?= htmlspecialchars($order['po_number']) ?>
                    </a>
                  </td>
                  <td><?= date($currentLang === 'fr' ? 'd M Y' : 'M d, Y', strtotime($order['order_date'])) ?></td>
                  <td><?= $order['item_count'] ?> <?= $t['sup_items'] ?? 'items' ?></td>
                  <td>$<?= number_format($order['total_amount'], 2) ?></td>
                  <td>
                    <span class="badge <?= $order['status'] ?>">
                      <?= htmlspecialchars($t['po_status_' . $order['status']] ?? ucfirst($order['status'])) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p><?= $t['sup_no_orders'] ?? 'No recent orders' ?></p>
          </div>
        <?php endif; ?>
      </div>

<?php endif; // end pending vs approved dashboard ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
