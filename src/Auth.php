<?php

class Auth {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($pdo, $username, $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active'] == 0) {
                return "locked"; // Return a specific status for locked accounts
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['level'] = $user['level'];
            $_SESSION['foto'] = $user['foto'];
            return true;
        }
        return false;
    }

    public static function logout() {
        self::init();
        session_destroy();
        header("Location: login");
        exit();
    }

    public static function check() {
        self::init();
        if (!isset($_SESSION['user_id'])) {
            header("Location: login");
            exit();
        }
        
        // Final check if still active
        require_once 'Config.php';
        global $pdo;
        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        if ($stmt->fetchColumn() == 0) {
            self::logout();
        }
    }

    public static function checkRole($allowed_roles) {
        self::check();
        if (!in_array($_SESSION['level'], $allowed_roles)) {
            $base = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
            header("Location: {$base}index.php?err=akses_negadu");
            exit;
        }
    }
}
?>
