<?php
/**
 * Auth Popup Modal Component
 * Shows when non-authenticated users try to interact with products/stores
 * Include this in footer.php or at end of body in layout files
 */

// Only show if user is NOT logged in
if (!function_exists('isLoggedIn') || !isLoggedIn()):
?>
<!-- Auth Required Popup Modal -->
<div id="authPopupOverlay" class="auth-popup-overlay" style="display: none;">
  <div class="auth-popup-modal">
    <!-- Close Button -->
    <button class="auth-popup-close" onclick="closeAuthPopup()" aria-label="Close">
      <i class="fas fa-times"></i>
    </button>

    <!-- Header -->
    <div class="auth-popup-header">
      <div class="auth-popup-icon">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <h2>Join OCSAPP</h2>
      <p>Sign in or create an account to continue shopping</p>
    </div>

    <!-- Options -->
    <div class="auth-popup-options">
      <!-- Buyer Option -->
      <div class="auth-option buyer-option">
        <div class="option-icon">
          <i class="fas fa-user"></i>
        </div>
        <div class="option-content">
          <h3><a href="<?= url('login') ?>" class="option-title-link">Buyer</a> Account</h3>
          <p>Approved purchasing on the platform</p>
          <div class="option-buttons">
            <a href="<?= url('login') ?>" class="btn-auth btn-signin">Sign In</a>
            <a href="<?= url('register') ?>" class="btn-auth btn-create">Create Account</a>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="auth-divider">
        <span>OR</span>
      </div>

      <!-- Seller Option -->
      <div class="auth-option seller-option">
        <div class="option-icon">
          <i class="fas fa-store"></i>
        </div>
        <div class="option-content">
          <h3><a href="<?= url('register?role=seller') ?>" class="option-title-link">Seller</a> Account</h3>
          <p>Canadian or Quebec registered business approved for shop management and product selling on OCSAPP platform</p>
          <div class="option-buttons">
            <a href="<?= url('login') ?>" class="btn-auth btn-signin">Sign In</a>
            <a href="<?= url('register?role=seller') ?>" class="btn-auth btn-create">Create Account</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="auth-popup-footer">
      <p>By continuing, you agree to our <a href="<?= url('terms') ?>">Terms of Service</a> and <a href="<?= url('privacy') ?>">Privacy Policy</a></p>
    </div>
  </div>
</div>

<!-- Auth Popup Styles -->
<style>
.auth-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.auth-popup-overlay.show {
  opacity: 1;
}

.auth-popup-modal {
  background: white;
  border-radius: 20px;
  max-width: 480px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  transform: scale(0.9) translateY(20px);
  transition: transform 0.3s ease;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.auth-popup-overlay.show .auth-popup-modal {
  transform: scale(1) translateY(0);
}

.auth-popup-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 36px;
  height: 36px;
  border: none;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  transition: all 0.2s;
  z-index: 10;
}

.auth-popup-close:hover {
  background: rgba(255,255,255,0.3);
}

.auth-popup-header {
  text-align: center;
  padding: 40px 30px 24px;
  background: linear-gradient(135deg, #00b207 0%, #009206 100%);
  border-radius: 20px 20px 0 0;
  color: white;
}

.auth-popup-icon {
  width: 70px;
  height: 70px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  font-size: 28px;
}

.auth-popup-header h2 {
  margin: 0 0 8px;
  font-size: 24px;
  font-weight: 700;
}

.auth-popup-header p {
  margin: 0;
  opacity: 0.9;
  font-size: 14px;
}

.auth-popup-options {
  padding: 24px;
}

.auth-option {
  display: flex;
  gap: 16px;
  padding: 20px;
  border: 2px solid #e5e7eb;
  border-radius: 16px;
  transition: all 0.2s;
}

.auth-option:hover {
  border-color: #00b207;
  background: #f0fdf4;
}

.seller-option:hover {
  border-color: #f59e0b;
  background: #fffbeb;
}

.buyer-option .option-icon {
  background: linear-gradient(135deg, #00b207 0%, #009206 100%);
}

.seller-option .option-icon {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.option-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
  flex-shrink: 0;
}

.option-content {
  flex: 1;
}

.option-content h3 {
  margin: 0 0 4px;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.option-title-link {
  color: #00b207;
  text-decoration: none;
  font-weight: 700;
}

.option-title-link:hover {
  text-decoration: underline;
}

.seller-option .option-title-link {
  color: #d97706;
}

.option-content p {
  margin: 0 0 12px;
  font-size: 13px;
  color: #6b7280;
  line-height: 1.4;
}

.option-buttons {
  display: flex;
  gap: 8px;
}

.btn-auth {
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-signin {
  background: #f3f4f6;
  color: #374151;
}

.btn-signin:hover {
  background: #e5e7eb;
}

.btn-create {
  background: #00b207;
  color: white;
}

.btn-create:hover {
  background: #009206;
}

.seller-option .btn-create {
  background: #f59e0b;
}

.seller-option .btn-create:hover {
  background: #d97706;
}

.auth-divider {
  display: flex;
  align-items: center;
  margin: 16px 0;
}

.auth-divider::before,
.auth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #e5e7eb;
}

.auth-divider span {
  padding: 0 12px;
  color: #9ca3af;
  font-size: 12px;
  font-weight: 600;
}

.auth-popup-footer {
  padding: 16px 24px 24px;
  text-align: center;
  border-top: 1px solid #f3f4f6;
}

.auth-popup-footer p {
  margin: 0;
  font-size: 12px;
  color: #9ca3af;
}

.auth-popup-footer a {
  color: #00b207;
  text-decoration: none;
}

.auth-popup-footer a:hover {
  text-decoration: underline;
}

/* Mobile Responsive */
@media (max-width: 480px) {
  .auth-popup-modal {
    margin: 10px;
    max-height: calc(100vh - 20px);
  }

  .auth-popup-header {
    padding: 30px 20px 20px;
  }

  .auth-popup-header h2 {
    font-size: 20px;
  }

  .auth-popup-options {
    padding: 16px;
  }

  .auth-option {
    flex-direction: column;
    text-align: center;
    padding: 16px;
  }

  .option-icon {
    margin: 0 auto;
  }

  .option-buttons {
    justify-content: center;
  }
}
</style>

<!-- Auth Popup JavaScript -->
<script>
// Auth Popup Functions
function showAuthPopup() {
  const overlay = document.getElementById('authPopupOverlay');
  if (overlay) {
    overlay.style.display = 'flex';
    // Trigger animation
    setTimeout(() => overlay.classList.add('show'), 10);
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
  }
}

function closeAuthPopup() {
  const overlay = document.getElementById('authPopupOverlay');
  if (overlay) {
    overlay.classList.remove('show');
    setTimeout(() => {
      overlay.style.display = 'none';
      document.body.style.overflow = '';
    }, 300);
  }
}

// Close on overlay click (not modal)
document.addEventListener('DOMContentLoaded', function() {
  const overlay = document.getElementById('authPopupOverlay');
  if (overlay) {
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) {
        closeAuthPopup();
      }
    });
  }

  // Close on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeAuthPopup();
    }
  });
});

// Intercept actions that require auth
function requireAuth(event) {
  event.preventDefault();
  event.stopPropagation();
  showAuthPopup();
  return false;
}

// Auto-attach to add-to-cart buttons and wishlist buttons for guests
document.addEventListener('DOMContentLoaded', function() {
  // Add to cart buttons
  document.querySelectorAll('.add-to-cart').forEach(function(btn) {
    const originalOnclick = btn.onclick;
    btn.onclick = function(e) {
      requireAuth(e);
    };
  });

  // Wishlist buttons
  document.querySelectorAll('.wishlist-btn').forEach(function(btn) {
    const originalOnclick = btn.onclick;
    btn.onclick = function(e) {
      requireAuth(e);
    };
  });

  // Product card clicks (optional - can enable if you want to block product viewing)
  // document.querySelectorAll('.product-card a').forEach(function(link) {
  //   link.addEventListener('click', requireAuth);
  // });
});
</script>
<?php endif; ?>
