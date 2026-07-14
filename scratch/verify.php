<?php
require_once 'src/Config.php';
$stmt = $pdo->query("SELECT * FROM alternatives WHERE is_ideal = 1");
print_r($stmt->fetch());
?>
