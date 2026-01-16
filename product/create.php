<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// HANDLE PREFLIGHT (WAJIB)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/database.php";

// Ambil data JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validasi
$category_id = $data['category_id'] ?? null;
$name        = $data['name'] ?? '';
$description = $data['description'] ?? '';
$price       = $data['price'] ?? 0;
$stock       = $data['stock'] ?? 0;

if (!$category_id || !$name || !$price || !$stock) {
    echo json_encode([
        "status" => false,
        "message" => "Data produk tidak lengkap"
    ]);
    exit;
}

// Insert ke database
$stmt = $pdo->prepare("
  INSERT INTO products (category_id, name, description, price, stock)
  VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
  $category_id,
  $name,
  $description,
  $price,
  $stock,
]);

echo json_encode([
  "status" => true,
  "message" => "Produk berhasil ditambahkan"
]);
