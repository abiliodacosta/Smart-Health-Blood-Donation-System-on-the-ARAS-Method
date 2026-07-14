<?php
require_once 'src/Config.php';
$stmt = $pdo->query("SELECT id, code, name, is_ideal FROM alternatives");
print_r($stmt->fetchAll());
?>
