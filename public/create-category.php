<?php
require __DIR__ . '/../includes/bootstrap.php';
use App\Repositories\CategoryRepository;
use App\Controllers\CategoryController;

if(!class_exists(CategoryRepository::class)) {
   http_response_code(503);
   exit('CategoryRepository class not found. Please check your autoloading and file structure.');
}

$controller = new CategoryController(new CategoryRepository($pdo));
$controller->create();
?>