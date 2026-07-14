<?php
// login_android.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Koneksaun ba Database 
$host = "localhost";
$db_name = "dss_aras_db"; // Naran database
$username = "root";
$password = ""; 

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->exec("set names utf8");
} catch(PDOException $exception) {
    echo json_encode(["status" => "error", "message" => "Connection error: " . $exception->getMessage()]);
    exit();
}

// Simu input JSON ou Form Data husi Android
$data = json_decode(file_get_contents("php://input"));
$user = isset($_POST['username']) ? $_POST['username'] : (isset($data->username) ? $data->username : die());
$pass = isset($_POST['password']) ? $_POST['password'] : (isset($data->password) ? $data->password : die());

// Buka iha tabela 'users' 
$query = "SELECT id, full_name, username, password FROM users WHERE username = :user LIMIT 0,1";
$stmt = $conn->prepare($query);

// Prepara data
$user = htmlspecialchars(strip_tags($user));
$pass = htmlspecialchars(strip_tags($pass)); 

$stmt->bindParam(":user", $user);
$stmt->execute();

$num = $stmt->rowCount();

// Fó fila resposta ba Android
if($num > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // 🟢 VERIFIKA PASSWORD HO BCRYPT (hanesan login web)
    if (password_verify($pass, $row['password'])) {
        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil.",
            "data" => [
                "id" => $row['id'],
                "nama" => $row['full_name'], 
                "username" => $row['username']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Username ka password sala."]);
    }
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Username ka password sala."]);
}
?>
