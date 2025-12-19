<?php
// db.php - koneksi database MySQL

$host = "localhost";       // server database
$user = "root";            // user MySQL
$pass = "restucs27";                // password MySQL, default kosong di XAMPP
$dbname = "simple_order_db"; // nama database

// membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// cek koneksi
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $conn->connect_error
    ]));
}

// set karakter UTF-8 (optional tapi bagus)
$conn->set_charset("utf8");
?>
