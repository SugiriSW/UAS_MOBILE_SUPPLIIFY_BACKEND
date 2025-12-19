<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

include "../config/db.php";

/**
 * 1) Ambil input JSON (kalau Flutter kirim JSON)
 */
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);
if (is_array($input)) {
  $_POST = array_merge($_POST, $input); // gabungkan JSON ke POST
}

/**
 * 2) Ambil data dari POST (bisa dari form / json yang sudah digabung)
 */
$order_id = intval($_POST['order_id'] ?? 0);
$status   = strtolower(trim($_POST['status'] ?? ''));

/**
 * 3) Validasi
 */
if ($order_id <= 0 || $status === '') {
  echo json_encode([
    "status" => "failed",
    "message" => "Order ID atau status kosong",
    "debug" => [
      "post" => $_POST,
      "raw" => $raw
    ]
  ]);
  exit;
}

$valid_status = ['pending', 'approved', 'shipping', 'done', 'rejected'];
if (!in_array($status, $valid_status, true)) {
  echo json_encode([
    "status" => "failed",
    "message" => "Status tidak valid",
    "allowed" => $valid_status
  ]);
  exit;
}

/**
 * 4) Update
 */
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
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
    "message" => "Gagal update status",
    "error" => $stmt->error
  ]);
}

$stmt->close();
$conn->close();
