<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$addresses = $addresses ?? [];
$cartCount = $cartCount ?? 0;
$user = user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Addresses - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #333; }
        .account-layout { display: flex; max-width: 1200px; margin: 40px auto; gap: 24px; padding: 0 16px; }
        .account-sidebar { width: 240px; flex-shrink: 0; }
        .account-main { flex: 1; min-width: 0; }
        .sidebar-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .sidebar-user { text-align: center; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0; margin-bottom: 12px; }
        .sidebar-avatar { width: 64px; height: 64px; border-radius: 50%; background: #00b207; color: #fff; font-size: 24px; font-weight: 600; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
        .sidebar-name { font-weight: 600; font-size: 15px; }
        .sidebar-email { font-size: 12px; color: #888; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #e8f5e9; color: #00b207; }
        .sidebar-nav a i { width: 18px; text-align: center; }
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .section-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .section-title { font-size: 18px; font-weight: 600; }
        .btn-add { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #00b207; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all .2s; }
        .btn-add:hover { background: #009206; }
        .address-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        .address-card { border: 2px solid #f0f0f0; border-radius: 12px; padding: 18px; position: relative; transition: all .2s; }
        .address-card.default { border-color: #00b207; }
        .address-type { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; }
        .default-badge { display: inline-block; background: #e8f5e9; color: #2e7d32; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px; margin-left: 6px; }
        .address-name { font-weight: 600; font-size: 15px; margin-bottom: 4px; }
        .address-line { font-size: 13px; color: #666; line-height: 1.5; }
        .address-phone { font-size: 13px; color: #888; margin-top: 6px; }
        .address-actions { display: flex; gap: 8px; margin-top: 14px; }
        .btn-sm { padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 500; border: none; cursor: pointer; transition: all .2s; }
        .btn-edit { background: #f5f5f5; color: #333; }
        .btn-edit:hover { background: #e3f2fd; color: #1565c0; }
        .btn-default { background: #e8f5e9; color: #2e7d32; }
        .btn-delete { background: #fce4ec; color: #c62828; }
        .btn-delete:hover { background: #c62828; color: #fff; }
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        .empty-state i { font-size: 56px; color: #ddd; display: block; margin-bottom: 16px; }
        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: #fff; border-radius: 16px; padding: 28px; width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto; }
        .modal-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #888; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 5px; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-check { display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .btn-submit { width: 100%; padding: 12px; background: #00b207; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 8px; transition: all .2s; }
        .btn-submit:hover { background: #009206; }
        @media (max-width: 768px) { .account-layout { flex-direction: column; } .account-sidebar { width: 100%; } .address-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="account-layout">
    <aside class="account-sidebar">
        <div class="sidebar-card">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?></div>
                <div class="sidebar-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                <div class="sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= url('account') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('account/orders') ?>"><i class="fas fa-box"></i> My Orders</a>
                <a href="<?= url('account/addresses') ?>" class="active"><i class="fas fa-map-marker-alt"></i> Addresses</a>
                <a href="<?= url('account/wishlist') ?>"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="<?= url('account/settings') ?>"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>
    </aside>

    <main class="account-main">
        <div id="flashMsg" style="display:none;" class="flash-msg"></div>

        <div class="section-card">
            <div class="section-top">
                <h2 class="section-title">My Addresses</h2>
                <button class="btn-add" onclick="openModal()"><i class="fas fa-plus"></i> Add Address</button>
            </div>

            <?php if (empty($addresses)): ?>
                <div class="empty-state">
                    <i class="fas fa-map-marker-alt"></i>
                    <p style="font-size:16px;font-weight:500;color:#555;margin-bottom:8px;">No addresses saved</p>
                    <p>Add your delivery address to speed up checkout.</p>
                    <button class="btn-add" style="margin-top:16px;" onclick="openModal()"><i class="fas fa-plus"></i> Add Address</button>
                </div>
            <?php else: ?>
                <div class="address-grid">
                    <?php foreach ($addresses as $addr): ?>
                        <div class="address-card <?= $addr['is_default'] ? 'default' : '' ?>">
                            <div class="address-type">
                                <i class="fas fa-<?= $addr['type'] === 'work' ? 'briefcase' : 'home' ?>"></i>
                                <?= ucfirst($addr['type'] ?? 'home') ?>
                                <?php if ($addr['is_default']): ?><span class="default-badge">Default</span><?php endif; ?>
                            </div>
                            <div class="address-name"><?= htmlspecialchars($addr['name']) ?></div>
                            <div class="address-line">
                                <?= htmlspecialchars($addr['address_line_1']) ?>
                                <?php if (!empty($addr['address_line_2'])): ?><br><?= htmlspecialchars($addr['address_line_2']) ?><?php endif; ?>
                                <br><?= htmlspecialchars($addr['city'] . ', ' . $addr['state'] . ' ' . $addr['postal_code']) ?>
                            </div>
                            <div class="address-phone"><i class="fas fa-phone" style="font-size:11px;"></i> <?= htmlspecialchars($addr['phone']) ?></div>
                            <div class="address-actions">
                                <button class="btn-sm btn-edit" onclick='editAddress(<?= json_encode($addr) ?>)'><i class="fas fa-pencil-alt"></i> Edit</button>
                                <?php if (!$addr['is_default']): ?>
                                    <button class="btn-sm btn-default" onclick="setDefault(<?= (int)$addr['id'] ?>)">Set Default</button>
                                <?php endif; ?>
                                <button class="btn-sm btn-delete" onclick="deleteAddress(<?= (int)$addr['id'] ?>)"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="addressModal">
    <div class="modal">
        <div class="modal-title">
            <span id="modalTitle">Add Address</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="addressForm">
            <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" id="addressId" name="id" value="">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" id="fieldName" required>
            </div>
            <div class="form-group">
                <label>Address Type</label>
                <select name="type" id="fieldType">
                    <option value="home">Home</option>
                    <option value="work">Work</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Address Line 1 *</label>
                <input type="text" name="address_line_1" id="fieldAddr1" required>
            </div>
            <div class="form-group">
                <label>Address Line 2</label>
                <input type="text" name="address_line_2" id="fieldAddr2">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>City *</label>
                    <input type="text" name="city" id="fieldCity" required>
                </div>
                <div class="form-group">
                    <label>Province</label>
                    <input type="text" name="state" id="fieldState">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" id="fieldPostal">
                </div>
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="tel" name="phone" id="fieldPhone" required>
                </div>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_default" id="fieldDefault" value="1">
                <label for="fieldDefault">Set as default address</label>
            </div>
            <button type="submit" class="btn-submit">Save Address</button>
        </form>
    </div>
</div>

<script>
const CSRF = '<?= htmlspecialchars(csrfToken()) ?>';

function showFlash(msg, type) {
    const el = document.getElementById('flashMsg');
    el.textContent = msg;
    el.style.display = 'block';
    el.style.background = type === 'success' ? '#e8f5e9' : '#fce4ec';
    el.style.color = type === 'success' ? '#2e7d32' : '#c62828';
    el.style.padding = '12px 16px';
    el.style.borderRadius = '8px';
    el.style.marginBottom = '16px';
    setTimeout(() => el.style.display = 'none', 4000);
}

function openModal() {
    document.getElementById('modalTitle').textContent = 'Add Address';
    document.getElementById('addressForm').reset();
    document.getElementById('addressId').value = '';
    document.getElementById('addressModal').classList.add('active');
}

function closeModal() {
    document.getElementById('addressModal').classList.remove('active');
}

function editAddress(addr) {
    document.getElementById('modalTitle').textContent = 'Edit Address';
    document.getElementById('addressId').value = addr.id;
    document.getElementById('fieldName').value = addr.name || '';
    document.getElementById('fieldType').value = addr.type || 'home';
    document.getElementById('fieldAddr1').value = addr.address_line_1 || '';
    document.getElementById('fieldAddr2').value = addr.address_line_2 || '';
    document.getElementById('fieldCity').value = addr.city || '';
    document.getElementById('fieldState').value = addr.state || '';
    document.getElementById('fieldPostal').value = addr.postal_code || '';
    document.getElementById('fieldPhone').value = addr.phone || '';
    document.getElementById('fieldDefault').checked = addr.is_default == 1;
    document.getElementById('addressModal').classList.add('active');
}

document.getElementById('addressForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = new FormData(this);
    const isEdit = data.get('id') !== '';
    const url = isEdit ? '<?= url('account/addresses/update') ?>' : '<?= url('account/addresses/store') ?>';
    try {
        const resp = await fetch(url, { method: 'POST', body: data });
        const json = await resp.json();
        if (json.success) {
            showFlash(json.message, 'success');
            closeModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showFlash(json.message || 'Error saving address', 'error');
        }
    } catch(err) {
        showFlash('Network error. Please try again.', 'error');
    }
});

async function setDefault(id) {
    const data = new FormData();
    data.append('id', id);
    data.append('<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>', CSRF);
    const resp = await fetch('<?= url('account/addresses/set-default') ?>', { method: 'POST', body: data });
    const json = await resp.json();
    if (json.success) { location.reload(); } else { showFlash(json.message, 'error'); }
}

async function deleteAddress(id) {
    if (!confirm('Delete this address?')) return;
    const data = new FormData();
    data.append('id', id);
    data.append('<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>', CSRF);
    const resp = await fetch('<?= url('account/addresses/delete') ?>', { method: 'POST', body: data });
    const json = await resp.json();
    if (json.success) { location.reload(); } else { showFlash(json.message, 'error'); }
}
</script>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
