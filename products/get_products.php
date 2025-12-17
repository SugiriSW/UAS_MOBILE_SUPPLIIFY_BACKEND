<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include "../config/db.php";

// optional: filter by product_id
$product_id = $_GET['product_id'] ?? '';

if(!empty($product_id)){
    $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
}else{
    $stmt = $conn->prepare("SELECT id, name, price FROM products");
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];
while($row = $result->fetch_assoc()){
    $products[] = $row;
}

echo json_encode([
    "status" => "success",
    "products" => $products
]);
?>
