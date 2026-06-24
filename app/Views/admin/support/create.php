<div style="max-width:780px;margin:0 auto;">
  <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
    <a href="/admin/support" style="color:#6b7280;text-decoration:none;font-size:13px;"><i class="fa-solid fa-arrow-left"></i> Back to Inbox</a>
    <h1 style="font-size:20px;font-weight:700;color:#111827;margin:0;">New Support Ticket</h1>
  </div>

  <form method="POST" action="/admin/support/store">
    <?= csrfField() ?>

    <div style="background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:20px;">
      <div style="padding:16px 24px;border-bottom:1px solid #f3f4f6;font-weight:600;font-size:14px;color:#374151;">
        <i class="fa-solid fa-ticket" style="color:#00b207;margin-right:8px;"></i>Ticket Details
      </div>
      <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div style="grid-column:1/-1;">
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Subject *</label>
          <input type="text" name="subject" required placeholder="Brief description of the issue"
            style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;">
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Channel</label>
          <select name="channel" style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:white;">
            <option value="phone">📞 Phone Call</option>
            <option value="email">📧 Email</option>
            <option value="web_form">🌐 Web Form</option>
            <option value="walk_in">🚶 Walk-in</option>
            <option value="chat">💬 Chat</option>
          </select>
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Category</label>
          <select name="category" style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:white;">
            <option value="order_issue">Order Issue</option>
            <option value="payment">Payment</option>
            <option value="account">Account</option>
            <option value="delivery">Delivery</option>
            <option value="product">Product</option>
            <option value="billing">Billing</option>
            <option value="general" selected>General</option>
          </select>
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Priority</label>
          <select name="priority" style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:white;">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">🔴 Urgent</option>
          </select>
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Assign To</label>
          <select name="assigned_to" style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:white;">
            <option value="">— Unassigned —</option>
            <?php foreach ($agents as $ag): ?>
              <option value="<?= $ag['id'] ?>" <?= $ag['id'] == ($_SESSION['user']['id'] ?? 0) ? 'selected' : '' ?>>
                <?= htmlspecialchars($ag['first_name'] . ' ' . $ag['last_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="grid-column:1/-1;">
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Description / Issue Details</label>
          <textarea name="description" rows="4" placeholder="Describe the issue in detail..."
            style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;"></textarea>
        </div>

      </div>
    </div>

    <!-- Contact Info -->
    <div style="background:white;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:20px;">
      <div style="padding:16px 24px;border-bottom:1px solid #f3f4f6;font-weight:600;font-size:14px;color:#374151;">
        <i class="fa-solid fa-user" style="color:#3b82f6;margin-right:8px;"></i>Contact Information
      </div>
      <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Contact Type</label>
          <select name="contact_type" style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;background:white;">
            <option value="unknown">Unknown</option>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
            <option value="supplier">Supplier</option>
            <option value="driver">Driver</option>
            <option value="lead">Lead</option>
          </select>
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Contact Name</label>
          <input type="text" name="contact_name" placeholder="Full name"
            style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;">
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Email</label>
          <input type="email" name="contact_email" placeholder="contact@example.com"
            style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;">
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Phone</label>
          <input type="tel" name="contact_phone" placeholder="514-555-0100"
            style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;">
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">Related Order # (optional)</label>
          <input type="number" name="order_id" placeholder="1234"
            style="width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;">
        </div>

      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;">
      <a href="/admin/support" style="padding:10px 20px;background:#f3f4f6;color:#374151;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;">Cancel</a>
      <button type="submit" style="padding:10px 24px;background:#00b207;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
        <i class="fa-solid fa-ticket" style="margin-right:6px;"></i>Create Ticket
      </button>
    </div>

  </form>
</div>
