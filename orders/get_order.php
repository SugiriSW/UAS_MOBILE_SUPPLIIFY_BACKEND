<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit;

include "../config/db.php";

// Query dengan JOIN ke products dan users
$query = "
    SELECT 
        o.*,
        p.name as product_name,
        p.price as product_price,
        u.username as customer_name
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.id
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
";

$result = $conn->query($query);

if ($result) {
    $orders = [];
    while($row = $result->fetch_assoc()) {
        // Pastikan status tidak null
        $row['status'] = $row['status'] ?? 'pending';
        $orders[] = $row;
    }
    
    echo json_encode([
        "status" => "success",
        "orders" => $orders,
        "count" => count($orders)
    ]);
} else {
    echo json_encode([
        "status" => "failed",
        "message" => "Gagal mengambil data orders: " . $conn->error
    ]);
}

$conn->close();
?>