<?php
require_once 'src/Config.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN sexu VARCHAR(10) AFTER full_name");
    $pdo->exec("ALTER TABLE users ADD COLUMN level VARCHAR(20) DEFAULT 'Admin' AFTER password");
    $pdo->exec("ALTER TABLE users ADD COLUMN foto VARCHAR(255) AFTER level");
    echo "Database updated successfully";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
