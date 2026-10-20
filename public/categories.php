<?php
// BUỔI 4: entry vừa lấy dữ liệu Category vừa render.
// TODO BUỔI 6: triển khai CategoryRepository + CategoryController + View,
// sau đó giữ entry chỉ làm bootstrap và gọi Controller.
require __DIR__ . '/../includes/bootstrap.php';
use App\Repositories\CategoryRepository;
use App\Controllers\CategoryController;

if(!class_exists(CategoryRepository::class)) {
   http_response_code(503);
   exit('CategoryRepository class not found. Please check your autoloading and file structure.');
}

$controller = new CategoryController(new CategoryRepository($pdo));
$controller->index();
?>