<?php
require_once 'src/Config.php';
$stmt = $pdo->query("SELECT * FROM evaluations WHERE alternative_id IN (1, 7)");
print_r($stmt->fetchAll());
?>
