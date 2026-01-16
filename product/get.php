<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once "../config/database.php";

$stmt = $pdo->query("
  SELECT p.*, c.name AS category_name
  FROM products p
  JOIN categories c ON p.category_id = c.id
  ORDER BY p.created_at DESC
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  "status" => true,
  "data" => $products
]);
