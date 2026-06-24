<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'My Profile' ?> - OCS</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/profile.css') ?>">
</head>
<body>
    <!-- Navigation -->
    <div class="nav-bar">
        <a href="<?= url('/') ?>" class="nav-brand">
            <i class="fas fa-shopping-bag"></i>
            OCS
        </a>
        <div class="nav-links">
            <a href="<?= url('dashboard') ?>">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            <a href="<?= url('profile') ?>" class="active">
                <i class="fas fa-user"></i>
                Profile
            </a>
            <a href="<?= url('logout') ?>">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>

    <div class="profile-container">
        <!-- Header -->
        <div class="profile-header">
            <h1>
                <i class="fas fa-user-circle"></i>
                My Profile
            </h1>
            <p>Manage your account information and preferences</p>
        </div>

        <!-- Flash Messages -->
        <?php if (hasFlash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div><?= getFlash('success') ?></div>
            </div>
        <?php endif; ?>

        <?php if (hasFlash('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div><?= getFlash('error') ?></div>
            </div>
        <?php endif; ?>

        <!-- Profile Information -->
        <div class="profile-section">
            <h2 class="section-title">
                <i class="fas fa-id-card"></i>
                Profile Information
            </h2>
            
            <form action="<?= url('profile/update') ?>" method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>
                
                <!-- Avatar Section -->
                <div class="avatar-section">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= asset('uploads/avatars/' . $user['avatar']) ?>" alt="Avatar" class="avatar-preview">
                    <?php else: ?>
                        <?php 
                            $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                            $userName = $userName ?: ($user['name'] ?? 'User');
                        ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&size=100&background=00b207&color=fff" alt="Avatar" class="avatar-preview">
                    <?php endif; ?>
                    
                    <div class="avatar-info">
                        <h4>Profile Picture</h4>
                        <p>Upload a professional photo to personalize your account</p>
                        <label for="avatar" class="avatar-upload">
                            <i class="fas fa-camera"></i>
                            Change Photo
                            <input type="file" id="avatar" name="avatar" accept="image/*">
                        </label>
                        <small style="display: block; margin-top: 8px;">
                            Max size: 5MB • Formats: JPG, PNG, GIF
                        </small>
                    </div>
                </div>

                <!-- Name Fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">
                            <i class="fas fa-user"></i>
                            First Name *
                        </label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required placeholder="Enter first name">
                    </div>

                    <div class="form-group">
                        <label for="last_name">
                            <i class="fas fa-user"></i>
                            Last Name *
                        </label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required placeholder="Enter last name">
                    </div>
                </div>

                <!-- Contact Fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Email Address *
                        </label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required placeholder="your@email.com">
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            <i class="fas fa-phone"></i>
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="(809) 123-4567">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="profile-section">
            <h2 class="section-title">
                <i class="fas fa-lock"></i>
                Change Password
            </h2>
            
            <form action="<?= url('profile/change-password') ?>" method="POST">
                <?= csrfField() ?>
                
                <div class="form-group">
                    <label for="current_password">
                        <i class="fas fa-key"></i>
                        Current Password *
                    </label>
                    <input type="password" id="current_password" name="current_password" required placeholder="Enter current password">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">
                            <i class="fas fa-lock"></i>
                            New Password *
                        </label>
                        <input type="password" id="new_password" name="new_password" required placeholder="Enter new password" minlength="8">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Minimum 8 characters
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">
                            <i class="fas fa-check-circle"></i>
                            Confirm New Password *
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password" minlength="8">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-shield-alt"></i>
                        Change Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Information -->
        <div class="profile-section">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i>
                Account Information
            </h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-check-circle"></i>
                        Account Status
                    </p>
                    <p class="info-value status-<?= $user['status'] ?? 'active' ?>">
                        <?= ucfirst($user['status'] ?? 'Active') ?>
                    </p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-user-tag"></i>
                        Account Type
                    </p>
                    <p class="info-value">
                        <?= ucfirst($user['role'] ?? $user['user_type'] ?? 'User') ?>
                    </p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-calendar-alt"></i>
                        Member Since
                    </p>
                    <p class="info-value">
                        <?php if (!empty($user['created_at'])): ?>
                            <?= date('M d, Y', strtotime($user['created_at'])) ?>
                        <?php else: ?>
                            Unknown
                        <?php endif; ?>
                    </p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-clock"></i>
                        Last Login
                    </p>
                    <p class="info-value">
                        <?php if (!empty($user['last_login_at'])): ?>
                            <?= date('M d, Y', strtotime($user['last_login_at'])) ?>
                        <?php else: ?>
                            Never
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password confirmation validation
        document.querySelector('form[action*="change-password"]').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match. Please try again.');
                return false;
            }
            
            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
        });

        // Avatar preview
        document.getElementById('avatar')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.querySelector('.avatar-preview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>