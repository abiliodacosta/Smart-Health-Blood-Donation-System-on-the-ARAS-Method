<?php
require_once 'src/Config.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER level");
    echo "Database updated successfully: is_active column added.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
