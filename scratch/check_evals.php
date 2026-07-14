<?php
require_once 'src/Config.php';
$stmt = $pdo->query("SELECT alternative_id, COUNT(*) as count FROM evaluations GROUP BY alternative_id");
print_r($stmt->fetchAll());
?>
