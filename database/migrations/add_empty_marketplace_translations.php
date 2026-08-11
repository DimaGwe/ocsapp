<?php
/**
 * Adds translation keys for the homepage empty-marketplace state
 * (shown when there are no active seller shops yet).
 * Run manually: php database/migrations/add_empty_marketplace_translations.php
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$rows = [
    [
        'key' => 'empty_marketplace_title',
        'en' => 'New shops joining soon!',
        'fr' => 'De nouvelles boutiques arrivent bientot !',
        'description' => 'Homepage empty-state heading shown when no seller shops are active yet',
    ],
    [
        'key' => 'empty_marketplace_desc',
        'en' => "We're onboarding independent local sellers right now. Check back soon, or if you run a local business, join OCSAPP and be one of our first sellers.",
        'fr' => "Nous accueillons actuellement des vendeurs locaux independants. Revenez bientot, ou si vous dirigez une entreprise locale, rejoignez OCSAPP et devenez l'un de nos premiers vendeurs.",
        'description' => 'Homepage empty-state body text shown when no seller shops are active yet',
    ],
    [
        'key' => 'empty_marketplace_cta',
        'en' => 'Become a Seller',
        'fr' => 'Devenir vendeur',
        'description' => 'Homepage empty-state CTA button linking to seller-central',
    ],
];

$stmt = $db->prepare("
    INSERT INTO translations (`key`, category, en, fr, description, is_html, created_at, updated_at)
    VALUES (:key, 'homepage', :en, :fr, :description, 0, NOW(), NOW())
    ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr), description = VALUES(description), updated_at = NOW()
");

foreach ($rows as $row) {
    $stmt->execute($row);
    echo "Upserted: {$row['key']}\n";
}

echo "Done.\n";
