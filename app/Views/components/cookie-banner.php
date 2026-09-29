<?php
/**
 * Cookie consent banner (shared).
 * Marketplace pages get it through components/header.php; ecosystem pages
 * (landing, Centrals, legal, contact, login...) include it before </body>.
 * Only cookie_consent=accepted turns on VisitorTracker (visitor_id cookie),
 * see app/Helpers/VisitorTracker.php and the Cookie Policy (/cookies, /temoins).
 */
if (!empty($_COOKIE['cookie_consent']) || !empty($GLOBALS['__cookieBannerRendered'])) {
    return;
}
$GLOBALS['__cookieBannerRendered'] = true;

$_cbLang = $_SESSION['language'] ?? 'fr';
$_cbFr   = ($_cbLang === 'fr');
$_cbText = t('cookie_banner_prefix', $_cbLang, $_cbFr
    ? 'Nous utilisons des témoins essentiels au fonctionnement du site. Avec votre accord, nous utilisons aussi un témoin de mesure d’audience interne (aucun tiers). Pour en savoir plus, consultez notre'
    : 'We use cookies that the site needs to work. With your permission, we also use one internal audience-measurement cookie (no third parties). To learn more, see our');
$_cbLink    = t('cookie_policy_link_text', $_cbLang, $_cbFr ? 'Politique relative aux témoins' : 'Cookie Policy');
$_cbAccept  = t('cookie_accept', $_cbLang, $_cbFr ? 'Accepter' : 'Accept');
$_cbDecline = t('cookie_decline', $_cbLang, $_cbFr ? 'Refuser' : 'Decline');
?>
<!-- Cookie Consent Banner -->
<div id="cookieBanner" role="region" aria-label="<?= $_cbFr ? 'Consentement aux témoins' : 'Cookie consent' ?>" style="position:fixed;bottom:0;left:0;right:0;z-index:9999;background:#1a1a1a;color:#fff;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;box-shadow:0 -4px 16px rgba(0,0,0,.3);font-family:inherit;">
    <p style="margin:0;font-size:14px;line-height:1.5;flex:1;min-width:200px;color:#fff;">
        <?= $_cbText ?> <a href="<?= url('cookies') ?>" style="color:#00b207;"><?= $_cbLink ?></a>.
    </p>
    <div style="display:flex;gap:10px;flex-shrink:0;">
        <button type="button" onclick="setCookieConsent('accepted')" style="padding:10px 24px;background:#00b207;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;"><?= $_cbAccept ?></button>
        <button type="button" onclick="setCookieConsent('declined')" style="padding:10px 16px;background:transparent;color:#ccc;border:1px solid #555;border-radius:8px;font-size:14px;cursor:pointer;"><?= $_cbDecline ?></button>
    </div>
</div>
<script>
function setCookieConsent(choice) {
    var secure = location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = "cookie_consent=" + choice + "; max-age=" + (365*24*3600) + "; path=/; SameSite=Lax" + secure;
    document.getElementById('cookieBanner').style.display = 'none';
}
// Kept for any older markup that still calls these
function acceptCookies() { setCookieConsent('accepted'); }
function declineCookies() { setCookieConsent('declined'); }
</script>
