<?php
// api/avaliasaun.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Koneksi Database
$host = "localhost";
$db_name = "dss_aras_db";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8");
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Connection error: " . $exception->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"));

switch($method) {
    case 'GET':
        // GET: Ambil daftar nilai evaluasi (beserta nama alternatif dan kriteria)
        try {
            if(isset($_GET['alternative_id'])) {
                // Ambil evaluasi untuk satu alternatif
                $alt_id = (int)$_GET['alternative_id'];
                $query = "SELECT e.id, e.alternative_id, e.criteria_id, c.name as criteria_name, e.value 
                          FROM evaluations e 
                          JOIN criteria c ON e.criteria_id = c.id 
                          WHERE e.alternative_id = :alt_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(":alt_id", $alt_id);
            } else {
                // Ambil semua
                $query = "SELECT e.id, e.alternative_id, a.name as alternative_name, e.criteria_id, c.name as criteria_name, e.value 
                          FROM evaluations e 
                          JOIN alternatives a ON e.alternative_id = a.id
                          JOIN criteria c ON e.criteria_id = c.id";
                $stmt = $conn->prepare($query);
            }
            $stmt->execute();
            
            $evaluations = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $evaluations[] = [
                    "id" => (int)$row['id'],
                    "alternative_id" => (int)$row['alternative_id'],
                    "alternative_name" => isset($row['alternative_name']) ? $row['alternative_name'] : null,
                    "criteria_id" => (int)$row['criteria_id'],
                    "criteria_name" => $row['criteria_name'],
                    "value" => (float)$row['value']
                ];
            }
            
            echo json_encode([
                "status" => "success",
                "message" => "Data retrieved successfully",
                "data" => $evaluations
            ]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to get data: " . $e->getMessage()]);
        }
        break;

    case 'POST':
        // CREATE / UPSERT
        if(isset($data->alternative_id) && isset($data->criteria_id) && isset($data->value)) {
            try {
                // Cek apakah sudah ada
                $checkQuery = "SELECT id FROM evaluations WHERE alternative_id = :alt_id AND criteria_id = :crit_id";
                $stmtCheck = $conn->prepare($checkQuery);
                $alt_id = (int)$data->alternative_id;
                $crit_id = (int)$data->criteria_id;
                $stmtCheck->bindParam(":alt_id", $alt_id);
                $stmtCheck->bindParam(":crit_id", $crit_id);
                $stmtCheck->execute();
                
                if($stmtCheck->rowCount() > 0) {
                    // Update
                    $updateQuery = "UPDATE evaluations SET value = :value WHERE alternative_id = :alt_id AND criteria_id = :crit_id";
                    $stmtUpdate = $conn->prepare($updateQuery);
                    $val = (float)$data->value;
                    $stmtUpdate->bindParam(":value", $val);
                    $stmtUpdate->bindParam(":alt_id", $alt_id);
                    $stmtUpdate->bindParam(":crit_id", $crit_id);
                    $stmtUpdate->execute();
                    echo json_encode(["status" => "success", "message" => "Data updated successfully."]);
                } else {
                    // Insert
                    $insertQuery = "INSERT INTO evaluations (alternative_id, criteria_id, value) VALUES (:alt_id, :crit_id, :value)";
                    $stmtInsert = $conn->prepare($insertQuery);
                    $val = (float)$data->value;
                    $stmtInsert->bindParam(":alt_id", $alt_id);
                    $stmtInsert->bindParam(":crit_id", $crit_id);
                    $stmtInsert->bindParam(":value", $val);
                    $stmtInsert->execute();
                    echo json_encode(["status" => "success", "message" => "Data added successfully."]);
                }
            } catch(Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to process data: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed."]);
        break;
}
?>
