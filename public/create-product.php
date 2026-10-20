<?php
require __DIR__ . '/../includes/bootstrap.php';
use App\Repositories\ProductRepository;
use App\Controllers\ProductController;
use App\Repositories\CategoryRepository;

if(!class_exists(ProductRepository::class)) {
   http_response_code(503);
   exit('ProductRepository class not found. Please check your autoloading and file structure.');
}

$controller = new ProductController(new ProductRepository($pdo), new CategoryRepository($pdo));
$controller->create();
?>