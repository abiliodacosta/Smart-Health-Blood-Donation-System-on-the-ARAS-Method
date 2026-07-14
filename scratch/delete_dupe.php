<?php
require_once 'src/Config.php';
try {
    $pdo->exec("DELETE FROM alternatives WHERE id = 7");
    echo "Deleted ID 7";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
