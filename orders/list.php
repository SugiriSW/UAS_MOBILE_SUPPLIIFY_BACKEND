<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

include "../config/db.php";

$status = $_GET['status'] ?? 'all';

$sql = "SELECT o.id, o.qty, o.status, o.created_at,
               u.username,
               p.name AS product_name, p.price
        FROM orders o
        JOIN users u ON u.id = o.user_id
        JOIN products p ON p.id = o.product_id";

if ($status !== 'all') {
  $sql .= " WHERE o.status = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $status);
  $stmt->execute();
  $res = $stmt->get_result();
} else {
  $res = $conn->query($sql);
}

$data = [];
while($row = $res->fetch_assoc()) $data[] = $row;

echo json_encode(["status"=>"success","data"=>$data]);
