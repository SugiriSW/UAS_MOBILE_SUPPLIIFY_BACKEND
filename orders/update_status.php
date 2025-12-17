<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit;

include "../config/db.php";

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? '';
$status = $input['status'] ?? '';

if (empty($order_id) || empty($status)) {
    echo json_encode([
        "status" => "failed",
        "message" => "Order ID atau status kosong"
    ]);
    exit;
}

$order_id = intval($order_id);
$status = strtolower(trim($status));
$valid_status = ['pending', 'approved', 'rejected'];

if (!in_array($status, $valid_status)) {
    echo json_encode([
        "status" => "failed",
        "message" => "Status tidak valid"
    ]);
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Status order berhasil diupdate",
        "order_id" => $order_id,
        "new_status" => $status
    ]);
} else {
    echo json_encode([
        "status" => "failed",
        "message" => "Gagal update status: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
