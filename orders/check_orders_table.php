<?php
include "../config/db.php";

// Cek struktur tabel orders
$result = $conn->query("DESCRIBE orders");

echo "<h2>Struktur Tabel Orders</h2>";
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

while($row = $result->fetch_assoc()){
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Cek data sample
echo "<h2>5 Data Terbaru di Tabel Orders</h2>";
$sample = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
while($row = $sample->fetch_assoc()){
    echo "<pre>";
    print_r($row);
    echo "</pre>";
}

$conn->close();
?>