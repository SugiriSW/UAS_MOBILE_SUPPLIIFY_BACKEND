<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Tangani preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

include "../config/db.php";

// ambil data JSON POST
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? '';
$products = $data['products'] ?? []; // array of {product_id, qty}

if(empty($user_id) || empty($products)){
    echo json_encode([
        "status" => "failed",
        "message" => "User ID atau produk kosong"
    ]);
    exit;
}

$success = true;
$order_ids = []; // Untuk menyimpan ID order yang dibuat

foreach($products as $item){
    $product_id = $item['product_id'] ?? 0;
    $qty = $item['qty'] ?? 0;

    if($product_id <= 0 || $qty <= 0){
        $success = false;
        continue;
    }

    // TAMBAHKAN KOLOM STATUS DI SINI
    $status = 'pending'; // Default status
    
    // Jika tabel sudah ada kolom status:
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, qty, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $product_id, $qty, $status);
    
    // Jika tabel belum ada kolom status, gunakan ini saja:
    // $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, qty) VALUES (?, ?, ?)");
    // $stmt->bind_param("iii", $user_id, $product_id, $qty);
    
    if($stmt->execute()){
        $order_ids[] = $conn->insert_id; // Simpan ID order yang baru dibuat
    } else {
        $success = false;
        error_log("Error insert order: " . $stmt->error);
    }
    $stmt->close();
}

if($success){
    echo json_encode([
        "status" => "success",
        "message" => "Order berhasil dikirim",
        "order_ids" => $order_ids // Optional: kembalikan ID order
    ]);
} else {
    echo json_encode([
        "status" => "failed",
        "message" => "Terjadi kesalahan saat menyimpan order. Cek log untuk detail."
    ]);
}

$conn->close();
?>