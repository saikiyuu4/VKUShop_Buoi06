<?php
// BUỔI 4: PHP vừa query DB vừa điều phối và render HTML trong entry.
// TODO BUỔI 6: chuyển SQL sang ProductRepository::all(), điều phối sang
// ProductController::index(), chuyển toàn bộ HTML sang views/products/index.php.
require __DIR__ . '/../includes/bootstrap.php';
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Controllers\ProductController;

if(!class_exists(ProductRepository::class)) {
   http_response_code(503);
   exit('ProductRepository class not found. Please check your autoloading and file structure.');
}

$controller = new ProductController(new ProductRepository($pdo), new CategoryRepository($pdo));
$controller->index();
?>