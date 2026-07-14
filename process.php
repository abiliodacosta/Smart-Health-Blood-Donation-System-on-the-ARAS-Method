<?php
require_once 'src/Config.php';
require_once 'src/Auth.php';

Auth::check();

$action = $_GET['action'] ?? '';

if ($action == 'add_alternative') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $sexu = $_POST['sexu'];
    $tinan = $_POST['tinan'];
    $hela_fatin = $_POST['hela_fatin'];
    $telefone = $_POST['telefone'];
    $tipu_ran = $_POST['tipu_ran'];
    $scores = $_POST['scores']; // Array [crit_id => value]

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO alternatives (code, name, sexu, tinan, hela_fatin, telefone, tipu_ran) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$code, $name, $sexu, $tinan, $hela_fatin, $telefone, $tipu_ran]);
        $alt_id = $pdo->lastInsertId();

        if (isset($_POST['scores'])) {
            foreach ($_POST['scores'] as $crit_id => $val) {
                $stmt = $pdo->prepare("INSERT INTO evaluations (alternative_id, criteria_id, value) VALUES (?, ?, ?)");
                $stmt->execute([$alt_id, $crit_id, $val]);
            }
        }
        $pdo->commit();
        $redirect = $_POST['redirect'] ?? 'index';
        header("Location: " . $redirect . "?msg=Alternative added successfully");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}

if ($action == 'edit_alternative') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $code = $_POST['code'];
    $sexu = $_POST['sexu'];
    $tinan = $_POST['tinan'];
    $hela_fatin = $_POST['hela_fatin'];
    $telefone = $_POST['telefone'];
    $tipu_ran = $_POST['tipu_ran'];
    $scores = $_POST['scores'];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE alternatives SET code = ?, name = ?, sexu = ?, tinan = ?, hela_fatin = ?, telefone = ?, tipu_ran = ? WHERE id = ?");
        $stmt->execute([$code, $name, $sexu, $tinan, $hela_fatin, $telefone, $tipu_ran, $id]);

        if (isset($_POST['scores'])) {
            foreach ($_POST['scores'] as $crit_id => $val) {
                $stmt = $pdo->prepare("UPDATE evaluations SET value = ? WHERE alternative_id = ? AND criteria_id = ?");
                $stmt->execute([$val, $id, $crit_id]);
            }
        }
        $pdo->commit();
        $redirect = $_POST['redirect'] ?? 'index';
        header("Location: " . $redirect . "?msg=Alternative updated successfully");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}

if ($action == 'delete_alternative') {
    $id = $_GET['id'];
    $redirect = $_GET['redirect'] ?? 'index';
    $stmt = $pdo->prepare("DELETE FROM alternatives WHERE id = ? AND is_ideal = 0");
    $stmt->execute([$id]);
    header("Location: " . $redirect . "?msg=Alternative deleted successfully");
}

if ($action == 'add_criteria') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $weight = $_POST['weight'];
    $type = $_POST['type'];
    $redirect = $_POST['redirect'] ?? 'index';

    $stmt = $pdo->prepare("INSERT INTO criteria (code, name, weight, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$code, $name, $weight, $type]);
    header("Location: " . $redirect . "?msg=Criteria added successfully");
}

if ($action == 'edit_criteria') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $code = $_POST['code'];
    $weight = $_POST['weight'];
    $type = $_POST['type'];
    $redirect = $_POST['redirect'] ?? 'index';

    $stmt = $pdo->prepare("UPDATE criteria SET code = ?, name = ?, weight = ?, type = ? WHERE id = ?");
    $stmt->execute([$code, $name, $weight, $type, $id]);
    header("Location: " . $redirect . "?msg=Criteria updated successfully");
}

if ($action == 'delete_criteria') {
    $id = $_GET['id'];
    $redirect = $_GET['redirect'] ?? 'index';
    $stmt = $pdo->prepare("DELETE FROM criteria WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: " . $redirect . "?msg=Criteria deleted successfully");
}

if ($action == 'save_scores') {
    $alt_id = $_POST['alternative_id'];
    $scores = $_POST['scores'];
    $redirect = $_POST['redirect'] ?? 'index';

    $pdo->beginTransaction();
    try {
        foreach ($scores as $crit_id => $val) {
            // Check if evaluation already exists
            $stmt = $pdo->prepare("SELECT id FROM evaluations WHERE alternative_id = ? AND criteria_id = ?");
            $stmt->execute([$alt_id, $crit_id]);
            
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("UPDATE evaluations SET value = ? WHERE alternative_id = ? AND criteria_id = ?");
                $stmt->execute([$val, $alt_id, $crit_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO evaluations (alternative_id, criteria_id, value) VALUES (?, ?, ?)");
                $stmt->execute([$alt_id, $crit_id, $val]);
            }
        }
        $pdo->commit();
        header("Location: " . $redirect . "?msg=Avaliasaun rai ona ho susesu");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}

if ($action == 'delete_evaluation') {
    $alt_id = $_GET['id'];
    $redirect = $_GET['redirect'] ?? 'index';
    
    // Safeguard: Do not delete if it is the ideal alternative
    $stmt = $pdo->prepare("DELETE FROM evaluations WHERE alternative_id = ? AND alternative_id NOT IN (SELECT id FROM alternatives WHERE is_ideal = 1)");
    $stmt->execute([$alt_id]);
    
    header("Location: " . $redirect . "?msg=Avaliasaun hamos ona ho susesu");
}

if ($action == 'add_user') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $sexu = $_POST['sexu'];
    $level = $_POST['level'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $redirect = $_POST['redirect'] ?? 'index';

    // Handle File Upload
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . '_' . $username . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'assets/images/users/' . $foto);
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, full_name, sexu, password, level, foto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $full_name, $sexu, $password, $level, $foto]);
    header("Location: " . $redirect . "?msg=Utilisadór foun aumenta ona ho susesu");
}

if ($action == 'edit_user') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $sexu = $_POST['sexu'];
    $level = $_POST['level'];
    $redirect = $_POST['redirect'] ?? 'index';

    $pdo->beginTransaction();
    try {
        // Handle Password
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$password, $id]);
        }

        // Handle Foto
        if (!empty($_FILES['foto']['name'])) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = time() . '_' . $username . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], 'assets/images/users/' . $foto);
            $stmt = $pdo->prepare("UPDATE users SET foto = ? WHERE id = ?");
            $stmt->execute([$foto, $id]);
        }

        // Update Base Info
        $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, sexu = ?, level = ? WHERE id = ?");
        $stmt->execute([$username, $full_name, $sexu, $level, $id]);

        $pdo->commit();
        header("Location: " . $redirect . "?msg=Dadus utilisadór atualiza ona");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}

if ($action == 'toggle_status') {
    $id = $_GET['id'];
    $redirect = $_GET['redirect'] ?? 'index';
    
    // Get current status
    $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetchColumn();
    
    $new_status = ($current == 1) ? 0 : 1;
    $msg = ($new_status == 1) ? "Utilisadór loke fali ona" : "Utilisadór xave ona";
    
    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    header("Location: " . $redirect . "?msg=" . urlencode($msg));
}

if ($action == 'reset_password') {
    $id = $_GET['id'];
    $redirect = $_GET['redirect'] ?? 'index';
    $new_pass = password_hash('123456', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$new_pass, $id]);
    header("Location: " . $redirect . "?msg=Password reset ona ba '123456'");
}

if ($action == 'delete_user') {
    $id = $_GET['id'];
    $redirect = $_GET['redirect'] ?? 'index';
    
    // Prevent self-deletion if needed, but for now simple delete
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: " . $redirect . "?msg=Utilisadór hamos ona");
}
?>
