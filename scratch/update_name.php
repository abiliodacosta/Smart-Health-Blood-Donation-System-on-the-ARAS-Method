<?php
require_once 'src/Config.php';
try {
    $stmt = $pdo->prepare("UPDATE alternatives SET name = ? WHERE is_ideal = 1");
    $stmt->execute(['system']);
    echo "Success";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
