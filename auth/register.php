<?php
// auth/register.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../config/db.php";

// Debug: log input
error_log("Register request received");
error_log("POST data: " . print_r($_POST, true));

// ambil data POST
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'client'; // default client

// validasi sederhana
if(empty($username) || empty($password)){
    echo json_encode([
        "status" => "failed",
        "message" => "Username atau password kosong",
        "received_data" => [
            "username" => $username,
            "password_length" => strlen($password),
            "role" => $role
        ]
    ]);
    exit;
}

// cek username sudah ada atau belum
$check = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($check);
if(!$stmt){
    echo json_encode([
        "status" => "failed",
        "message" => "Database prepare error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    echo json_encode([
        "status" => "failed",
        "message" => "Username '$username' sudah terdaftar"
    ]);
    exit;
}

// Hash password (jika ingin lebih secure)
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// insert user baru
$insert = "INSERT INTO users (username, password, role) VALUES (?,?,?)";
$stmt = $conn->prepare($insert);
if(!$stmt){
    echo json_encode([
        "status" => "failed",
        "message" => "Database prepare error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("sss", $username, $password, $role); // Gunakan $hashedPassword jika hash

if($stmt->execute()){
    echo json_encode([
        "status" => "success",
        "message" => "Registrasi berhasil",
        "user_id" => $stmt->insert_id,
        "username" => $username,
        "role" => $role
    ]);
}else{
    echo json_encode([
        "status" => "failed",
        "message" => "Gagal registrasi: " . $stmt->error,
        "error_details" => $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>