<?php
/**
 * Beta Notice Component
 * Shows modal on first visit + persistent banner
 */

// Get translations
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
?>

<!-- Beta Notice CSS -->
<link rel="stylesheet" href="<?= asset('css/beta-notice.css') ?>">

<!-- Persistent Beta Banner -->
<div class="beta-banner">
    <div class="beta-banner-content">
        <span class="beta-banner-icon">⚠️</span>
        <div class="beta-banner-text">
            <strong>BETA VERSION</strong> -
            <?= $currentLang === 'fr'
                ? 'Site en test - Veuillez ne pas effectuer d\'achats réels pour le moment'
                : 'Site Under Testing - Please do not make real purchases at this time'
            ?>
            |
            <a href="mailto:info@ocsapp.ca" class="beta-banner-link">
                <?= $currentLang === 'fr' ? 'Signaler un problème' : 'Report Issues' ?>
            </a>
        </div>
    </div>
</div>

<!-- First Visit Modal -->
<div id="betaModalOverlay" class="beta-modal-overlay hidden">
    <div class="beta-modal">
        <div class="beta-modal-header">
            <div class="beta-modal-icon">🚧</div>
            <h2 class="beta-modal-title">
                <?= $currentLang === 'fr'
                    ? 'Bienvenue sur OCSAPP'
                    : 'Welcome to OCSAPP'
                ?>
            </h2>
            <p class="beta-modal-subtitle">
                <?= $currentLang === 'fr'
                    ? 'Version Bêta - Avant de continuer'
                    : 'Beta Version - Before You Continue'
                ?>
            </p>
        </div>

        <div class="beta-modal-body">
            <!-- Warning Notice -->
            <div class="beta-modal-notice">
                <h3 class="beta-modal-notice-title">
                    <span>⚠️</span>
                    <?= $currentLang === 'fr' ? 'Important Avis' : 'Important Notice' ?>
                </h3>
                <p class="beta-modal-notice-text">
                    <?= $currentLang === 'fr'
                        ? 'Ce site est actuellement en <strong>phase de test bêta</strong>. Certaines fonctionnalités peuvent ne pas fonctionner correctement et ne sont pas encore prêtes pour une utilisation publique.'
                        : 'This website is currently in <strong>beta testing phase</strong>. Some features may not work correctly and are not yet ready for public use.'
                    ?>
                </p>
            </div>

            <!-- What This Means -->
            <h4 style="margin: 0 0 12px; font-size: 16px; font-weight: 700; color: #1f2937;">
                <?= $currentLang === 'fr' ? 'Ce que cela signifie :' : 'What This Means:' ?>
            </h4>
            <ul class="beta-modal-list">
                <li>
                    <strong><?= $currentLang === 'fr' ? 'Ne faites PAS d\'achats réels' : 'DO NOT make real purchases' ?></strong> -
                    <?= $currentLang === 'fr'
                        ? 'Le traitement des paiements est en cours de test'
                        : 'Payment processing is being tested'
                    ?>
                </li>
                <li>
                    <strong><?= $currentLang === 'fr' ? 'Fonctionnalités en test' : 'Features are being tested' ?></strong> -
                    <?= $currentLang === 'fr'
                        ? 'Vous pouvez rencontrer des bugs ou des problèmes'
                        : 'You may encounter bugs or issues'
                    ?>
                </li>
                <li>
                    <strong><?= $currentLang === 'fr' ? 'Explorez librement' : 'Browse freely' ?></strong> -
                    <?= $currentLang === 'fr'
                        ? 'N\'hésitez pas à explorer et à tester les fonctionnalités'
                        : 'Feel free to explore and test features'
                    ?>
                </li>
            </ul>

            <!-- Launch Info -->
            <div class="beta-modal-info">
                <p>
                    <strong>
                        <?= $currentLang === 'fr'
                            ? '🚀 Lancement officiel bientôt!'
                            : '🚀 Official Launch Coming Soon!'
                        ?>
                    </strong><br>
                    <?= $currentLang === 'fr'
                        ? 'Nous travaillons dur pour vous offrir la meilleure expérience d\'achat. Merci de votre patience!'
                        : 'We\'re working hard to bring you the best shopping experience. Thank you for your patience!'
                    ?>
                </p>
            </div>

            <!-- Report Issues -->
            <p style="margin: 0; font-size: 14px; color: #6b7280; text-align: center;">
                <?= $currentLang === 'fr'
                    ? 'Des problèmes? Contactez-nous :'
                    : 'Found an issue? Contact us:'
                ?>
                <a href="mailto:info@ocsapp.ca" style="color: #00b207; font-weight: 600; text-decoration: none;">
                    info@ocsapp.ca
                </a>
            </p>
        </div>

        <div class="beta-modal-footer">
            <button id="betaAcknowledgeBtn" class="beta-modal-button">
                <?= $currentLang === 'fr'
                    ? '✓ Je Comprends, Continuer'
                    : '✓ I Understand, Continue'
                ?>
            </button>
            <p class="beta-modal-disclaimer">
                <?= $currentLang === 'fr'
                    ? 'En cliquant, vous reconnaissez que ce site est en version bêta'
                    : 'By clicking, you acknowledge this site is in beta version'
                ?>
            </p>
        </div>
    </div>
</div>

<!-- Beta Notice JavaScript -->
<script>
(function() {
    'use strict';

    const STORAGE_KEY = 'ocs_beta_acknowledged';
    const overlay = document.getElementById('betaModalOverlay');
    const acknowledgeBtn = document.getElementById('betaAcknowledgeBtn');

    // Check if user has already acknowledged
    function hasAcknowledged() {
        return localStorage.getItem(STORAGE_KEY) === 'true';
    }

    // Show modal if not acknowledged
    function checkAndShowModal() {
        if (!hasAcknowledged()) {
            // Small delay for better UX
            setTimeout(() => {
                overlay.classList.remove('hidden');
                // Prevent body scroll when modal is open
                document.body.style.overflow = 'hidden';
            }, 500);
        }
    }

    // Handle acknowledge button click
    if (acknowledgeBtn) {
        acknowledgeBtn.addEventListener('click', function() {
            // Store acknowledgment
            localStorage.setItem(STORAGE_KEY, 'true');

            // Hide modal with animation
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        });
    }

    // Prevent closing modal by clicking overlay (force acknowledgment)
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            // Only allow closing via the button
            if (e.target === overlay) {
                // Optional: Add shake animation to draw attention to button
                const modal = overlay.querySelector('.beta-modal');
                modal.style.animation = 'shake 0.5s ease';
                setTimeout(() => {
                    modal.style.animation = '';
                }, 500);
            }
        });
    }

    // Show modal on page load if needed
    checkAndShowModal();

    // Add shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    `;
    document.head.appendChild(style);
})();
</script>
