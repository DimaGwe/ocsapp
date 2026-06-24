<?php
require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

$db = Database::getConnection();
$r = $db->query("DESCRIBE suppliers id");
print_r($r->fetch());
