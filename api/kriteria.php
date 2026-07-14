<?php
// api/kriteria.php
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
        // READ: Ambil semua data kriteria
        try {
            $query = "SELECT id, code, name, weight, type FROM criteria ORDER BY id DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            $criterias = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $criterias[] = [
                    "id" => (int)$row['id'],
                    "code" => $row['code'],
                    "name" => $row['name'],
                    "weight" => (float)$row['weight'],
                    "type" => $row['type']
                ];
            }
            
            echo json_encode([
                "status" => "success",
                "message" => "Data retrieved successfully",
                "data" => $criterias
            ]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to get data: " . $e->getMessage()]);
        }
        break;

    case 'POST':
        // CREATE: Tambah data kriteria baru
        if(isset($data->code) && isset($data->name) && isset($data->weight) && isset($data->type)) {
            try {
                $query = "INSERT INTO criteria (code, name, weight, type) VALUES (:code, :name, :weight, :type)";
                $stmt = $conn->prepare($query);
                
                $code = htmlspecialchars(strip_tags($data->code));
                $name = htmlspecialchars(strip_tags($data->name));
                $weight = (float)$data->weight;
                $type = htmlspecialchars(strip_tags($data->type));
                
                $stmt->bindParam(":code", $code);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":weight", $weight);
                $stmt->bindParam(":type", $type);
                
                if($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Data added successfully."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["status" => "error", "message" => "Unable to add data."]);
                }
            } catch(Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to add data: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data. Code, name, weight, and type are required."]);
        }
        break;

    case 'PUT':
        // UPDATE: Ubah data kriteria
        if(isset($data->id) && isset($data->code) && isset($data->name) && isset($data->weight) && isset($data->type)) {
            try {
                $query = "UPDATE criteria SET code = :code, name = :name, weight = :weight, type = :type WHERE id = :id";
                $stmt = $conn->prepare($query);
                
                $id = (int)$data->id;
                $code = htmlspecialchars(strip_tags($data->code));
                $name = htmlspecialchars(strip_tags($data->name));
                $weight = (float)$data->weight;
                $type = htmlspecialchars(strip_tags($data->type));
                
                $stmt->bindParam(":code", $code);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":weight", $weight);
                $stmt->bindParam(":type", $type);
                $stmt->bindParam(":id", $id);
                
                if($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Data updated successfully."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["status" => "error", "message" => "Unable to update data."]);
                }
            } catch(Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to update data: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data. ID, code, name, weight, and type are required."]);
        }
        break;

    case 'DELETE':
        // DELETE: Hapus data kriteria
        $id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($data->id) ? (int) $data->id : 0);
        
        if($id > 0) {
            try {
                // Delete related evaluations first due to foreign key constraints (or cascade if set up, but let's be safe)
                $stmtEval = $conn->prepare("DELETE FROM evaluations WHERE criteria_id = :id");
                $stmtEval->bindParam(":id", $id);
                $stmtEval->execute();

                $query = "DELETE FROM criteria WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(":id", $id);
                
                if($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Data deleted successfully."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["status" => "error", "message" => "Unable to delete data."]);
                }
            } catch(Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to delete data: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid ID."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed."]);
        break;
}
?>
