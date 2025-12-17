<?php
// products/add_product.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../config/db.php";

$input = json_decode(file_get_contents('php://input'), true);

$name = $input['name'] ?? '';
$price = intval($input['price'] ?? 0);

// Validasi input
if (empty($name)) {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Nama produk tidak boleh kosong'
    ]);
    exit;
}

if ($price <= 0) {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Harga harus lebih dari 0'
    ]);
    exit;
}

// Cek apakah produk sudah ada
$checkStmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
if (!$checkStmt) {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Database prepare error: ' . $conn->error
    ]);
    exit;
}

$checkStmt->bind_param("s", $name);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Produk dengan nama ini sudah ada'
    ]);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Insert produk baru
$sql = "INSERT INTO products (name, price) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Database prepare error: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("si", $name, $price);

if ($stmt->execute()) {
    $productId = $stmt->insert_id;
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Produk berhasil ditambahkan',
        'product_id' => $productId,
        'product' => [
            'id' => $productId,
            'name' => $name,
            'price' => $price
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Gagal menambah produk: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>