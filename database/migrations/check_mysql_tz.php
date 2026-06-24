<?php
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$r = $db->query("SELECT CONVERT_TZ('2026-01-01 12:00:00', 'UTC', 'America/Toronto') as t");
$val = $r->fetch()['t'];
echo "CONVERT_TZ result: " . ($val ?? 'NULL (named TZ not supported)') . "\n";

$r = $db->query("SELECT @@global.time_zone as g, @@session.time_zone as s");
$tz = $r->fetch();
echo "MySQL global TZ: {$tz['g']}\n";
echo "MySQL session TZ: {$tz['s']}\n";
