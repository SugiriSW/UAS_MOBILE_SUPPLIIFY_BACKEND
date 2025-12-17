<?php
// Tambahkan header CORS untuk Flutter Web
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

header('Content-Type: application/json');
include "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ambil data POST (support JSON dan form)
$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? $_POST['username'] ?? '';
$password = $input['password'] ?? $_POST['password'] ?? '';
error_log("php://input: " . file_get_contents('php://input'));
error_log("POST: " . print_r($_POST, true));

// validasi sederhana
if(empty($username) || empty($password)){
    echo json_encode([
        "status" => "failed",
        "message" => "Username atau password kosong"
    ]);
    exit;
}

// query cek user
$query = "SELECT * FROM users WHERE username=? AND password=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "user_id" => $user['id'],
        "role" => $user['role']
    ]);
}else{
    echo json_encode([
        "status" => "failed",
        "message" => "Username atau password salah"
    ]);
}
?>
