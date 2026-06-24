<?php
/**
 * Migration: Add cookie consent banner translations (EN/FR)
 */
require_once __DIR__ . '/../../bootstrap/init.php';

$db = Database::getConnection();

$rows = [
    ['cookie_banner_prefix',   'We use cookies to improve your experience and ensure the site works properly. By continuing to use OCSAPP Marketplace, you agree to our',  'Nous utilisons des témoins pour améliorer votre expérience et assurer le bon fonctionnement du site. En continuant à utiliser OCSAPP Marketplace, vous acceptez notre'],
    ['cookie_policy_link_text','Cookie Policy',  'Politique des témoins'],
    ['cookie_accept',          'Accept',         'Accepter'],
    ['cookie_decline',         'Decline',        'Refuser'],
];

$stmt = $db->prepare("
    INSERT INTO translations (`key`, en, fr)
    VALUES (:key, :en, :fr)
    ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)
");

foreach ($rows as [$key, $en, $fr]) {
    $stmt->execute(['key' => $key, 'en' => $en, 'fr' => $fr]);
    echo "Upserted: $key\n";
}

echo "Done.\n";
