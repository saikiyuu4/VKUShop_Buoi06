<?php
// Entry: chỉ bootstrap, khởi tạo Repository + Controller và gọi delete().
require __DIR__ . '/../includes/bootstrap.php';

use App\Controllers\ProductController;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

$controller = new ProductController(
    new ProductRepository($pdo),
    new CategoryRepository($pdo)
);
$controller->delete();