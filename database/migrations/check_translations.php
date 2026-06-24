<?php
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

// Check login translations
$stmt = $db->prepare("SELECT `key`, en, fr FROM translations WHERE `key` LIKE ?");
$stmt->execute(['sl_login%']);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== Login translations ===\n";
foreach ($rows as $r) {
    echo $r['key'] . ' | FR: ' . substr($r['fr'], 0, 60) . "\n";
}
echo 'Total login keys: ' . count($rows) . "\n\n";

// Check total translations
$stmt2 = $db->query("SELECT COUNT(*) as cnt FROM translations");
echo 'Total translations in DB: ' . $stmt2->fetch(PDO::FETCH_ASSOC)['cnt'] . "\n";

// Test getTranslations for FR
$t = getTranslations('fr');
echo "\ngetTranslations('fr') keys matching sl_login: \n";
foreach ($t as $key => $val) {
    if (strpos($key, 'sl_login') === 0) {
        echo "  $key = " . substr($val, 0, 60) . "\n";
    }
}
