<?php
// Entry: chỉ bootstrap, khởi tạo Repository + Controller và gọi delete().
require __DIR__ . '/../includes/bootstrap.php';

use App\Controllers\CategoryController;
use App\Repositories\CategoryRepository;

$controller = new CategoryController(
    new CategoryRepository($pdo)
);
$controller->edit();