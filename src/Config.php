<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'dss_aras_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If database doesn't exist, try to connect to MySQL and create it
    try {
        $temp_pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $sql = file_get_contents(__DIR__ . '/../db.sql');
        $temp_pdo->exec($sql);
        
        // Reconnect to the newly created database
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        die("Database connection failed: " . $ex->getMessage());
    }
}

// Always check if users table exists and has data
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if (!$stmt->fetch()) {
        // Table doesn't exist, run full script
        $sql = file_get_contents(__DIR__ . '/../db.sql');
        $pdo->exec($sql);
    } else {
        // Table exists, ensure admin user has the correct password
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute(['admin']);
        if (!$stmt->fetch()) {
            // Admin doesn't exist, create it
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)");
            $stmt->execute(['admin', $admin_pass, 'Administrator']);
        } else {
            // Admin exists, update password to ensure it matches 'admin123'
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$admin_pass, 'admin']);
        }
    }
} catch (PDOException $e) {
    // Handle error if query fails
}
?>
