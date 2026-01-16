<?php
require_once '../config/database.php';
require_once '../models/Category.php';
require_once '../models/Product.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$resource = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));

// Categories endpoints
if ($resource == 'categories') {
    $category = new Category($db);
    
    if ($method == 'GET') {
        $stmt = $category->read();
        $num = $stmt->rowCount();
        
        if ($num > 0) {
            $categories_arr = array();
            $categories_arr["data"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $category_item = array(
                    "id" => $id,
                    "name" => $name,
                    "created_at" => $created_at
                );
                array_push($categories_arr["data"], $category_item);
            }
            
            http_response_code(200);
            echo json_encode($categories_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No categories found."));
        }
    }
}

// Products endpoints
if ($resource == 'products') {
    $product = new Product($db);
    
    if ($method == 'GET') {
        // Get products by category if category_id is provided
        if (isset($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
            $stmt = $product->readByCategory($category_id);
        } else {
            $stmt = $product->read();
        }
        
        $num = $stmt->rowCount();
        
        if ($num > 0) {
            $products_arr = array();
            $products_arr["data"] = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $product_item = array(
                    "id" => $id,
                    "category_id" => $category_id,
                    "category_name" => $category_name,
                    "name" => $name,
                    "description" => $description,
                    "price" => $price,
                    "stock" => $stock,
                    "photo" => $photo,
                    "created_at" => $created_at
                );
                array_push($products_arr["data"], $product_item);
            }
            
            http_response_code(200);
            echo json_encode($products_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No products found."));
        }
    }
}
?>