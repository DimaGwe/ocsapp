<?php
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$stmt = $db->prepare("INSERT INTO translations (`key`, en, fr, category) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE en=VALUES(en), fr=VALUES(fr)");

$stmt->execute(['sl_login_portal_note', 'This is the <strong>Supplier Portal</strong>. Looking for a buyer or seller account?', 'Ceci est le <strong>Portail Fournisseur</strong>. Vous cherchez un compte acheteur ou vendeur?', 'supplier']);
$stmt->execute(['sl_login_portal_note_link', 'Log in here', 'Connectez-vous ici', 'supplier']);

echo "Portal note translations added.\n";
