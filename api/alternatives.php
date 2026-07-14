<?php
// api/alternatives.php
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

// Mendapatkan method HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Mengambil data dari body request (untuk POST, PUT)
$data = json_decode(file_get_contents("php://input"));

switch($method) {
    case 'GET':
        // READ: Ambil semua data alternatif
        try {
            $query = "SELECT id, code, name, is_ideal FROM alternatives ORDER BY id DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            $alternatives = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Cast tipe data agar sesuai dengan model di Android
                $alternatives[] = [
                    "id" => (int)$row['id'],
                    "code" => $row['code'],
                    "name" => $row['name'],
                    "is_ideal" => (bool)$row['is_ideal']
                ];
            }
            
            echo json_encode([
                "status" => "success",
                "message" => "Data retrieved successfully",
                "data" => $alternatives
            ]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to get data: " . $e->getMessage()]);
        }
        break;

    case 'POST':
        // CREATE: Tambah data alternatif baru
        if(isset($data->code) && isset($data->name)) {
            try {
                $query = "INSERT INTO alternatives (code, name, is_ideal) VALUES (:code, :name, :is_ideal)";
                $stmt = $conn->prepare($query);
                
                $code = htmlspecialchars(strip_tags($data->code));
                $name = htmlspecialchars(strip_tags($data->name));
                $is_ideal = isset($data->is_ideal) && $data->is_ideal == true ? 1 : 0;
                
                $stmt->bindParam(":code", $code);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":is_ideal", $is_ideal);
                
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
            echo json_encode(["status" => "error", "message" => "Incomplete data. Code and name are required."]);
        }
        break;

    case 'PUT':
        // UPDATE: Ubah data alternatif
        if(isset($data->id) && isset($data->code) && isset($data->name)) {
            try {
                $query = "UPDATE alternatives SET code = :code, name = :name, is_ideal = :is_ideal WHERE id = :id";
                $stmt = $conn->prepare($query);
                
                $id = (int) $data->id;
                $code = htmlspecialchars(strip_tags($data->code));
                $name = htmlspecialchars(strip_tags($data->name));
                $is_ideal = isset($data->is_ideal) && $data->is_ideal == true ? 1 : 0;
                
                $stmt->bindParam(":code", $code);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":is_ideal", $is_ideal);
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
            echo json_encode(["status" => "error", "message" => "Incomplete data. ID, code, and name are required."]);
        }
        break;

    case 'DELETE':
        // DELETE: Hapus data alternatif
        // Biasanya parameter ID dikirim lewat GET parameter (api.php?id=...) atau via body JSON
        $id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($data->id) ? (int) $data->id : 0);
        
        if($id > 0) {
            try {
                $query = "DELETE FROM alternatives WHERE id = :id";
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
