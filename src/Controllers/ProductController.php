<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

// TODO P3: Controller nhận ProductRepository qua constructor.
final class ProductController
{
    public function __construct(
        private ProductRepository $products,
        private CategoryRepository $categories
    ) {}

    public function index(): void
    {
        $products = $this->products->all();

        require dirname(__DIR__, 2)
            . '/views/products/index.php';
    }

    public function create(): void
    {
        $errors = [];
        $data = ['name' => '', 'price' => '', 'stock' => '', 'category_id' => ''];
        $categories = $this->categories->all();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach (array_keys($data) as $key) $data[$key] = trim((string) ($_POST[$key] ?? ''));
            if ($data['name'] === '') $errors['name'] = 'Vui lòng nhập tên sản phẩm.';
            elseif (!preg_match('/^.{1,150}$/us', $data['name'])) $errors['name'] = 'Tên sản phẩm tối đa 150 ký tự.';
            if ($data['price'] === '' || !ctype_digit($data['price']) || (int) $data['price'] <= 0 || strlen(ltrim($data['price'], '0')) > 12) $errors['price'] = 'Giá phải là số nguyên dương, tối đa 12 chữ số.';
            if ($data['stock'] === '' || !ctype_digit($data['stock']) || strlen(ltrim($data['stock'], '0')) > 10 || (float) $data['stock'] > 4294967295) $errors['stock'] = 'Tồn kho phải là số nguyên từ 0 đến 4294967295.';
            $categoryId = positive_id($data['category_id']);
            if (!$categoryId) {
                $errors['category_id'] = 'Vui lòng chọn danh mục hợp lệ.';
            } else {
                $category = $this->categories->findById($categoryId);
                if (!$category) $errors['category_id'] = 'Danh mục không tồn tại.';
            }
            if (!$errors) {
                $this->products->create(
                    $data['name'],
                    (int) $data['price'],
                    (int) $data['stock'],
                    $categoryId
                );
                redirect('products.php?created=1');
            }
        }
        require dirname(__DIR__, 2)
            . '/views/products/create.php';
    }
    public function edit(): void
    {
        $id = positive_id($_GET['id'] ?? null);
        if (!$id) page_not_found('ID sản phẩm không hợp lệ.');
        $product = $this->products->findById($id);
        if (!$product) page_not_found('Sản phẩm không tồn tại.');
        $categories = $this->categories->all();
        $errors = [];
        $data = ['name' => (string)$product['name'], 'price' => (string)$product['price'], 'stock' => (string)$product['stock'], 'category_id' => (string)$product['category_id']];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach (array_keys($data) as $key) $data[$key] = trim((string) ($_POST[$key] ?? ''));
            if ($data['name'] === '') $errors['name'] = 'Vui lòng nhập tên sản phẩm.';
            elseif (!preg_match('/^.{1,150}$/us', $data['name'])) $errors['name'] = 'Tên sản phẩm tối đa 150 ký tự.';
            if ($data['price'] === '' || !ctype_digit($data['price']) || (int) $data['price'] <= 0 || strlen(ltrim($data['price'], '0')) > 12) $errors['price'] = 'Giá phải là số nguyên dương, tối đa 12 chữ số.';
            if ($data['stock'] === '' || !ctype_digit($data['stock']) || strlen(ltrim($data['stock'], '0')) > 10 || (float) $data['stock'] > 4294967295) $errors['stock'] = 'Tồn kho phải là số nguyên từ 0 đến 4294967295.';
            $categoryId = positive_id($data['category_id']);
            if (!$categoryId) {
                $errors['category_id'] = 'Vui lòng chọn danh mục hợp lệ.';
            } else {
                $category = $this->categories->findById($categoryId);
                if (!$category) $errors['category_id'] = 'Danh mục không tồn tại.';
            }
            if (!$errors) {
                $this->products->update($id, $data['name'], (int)$data['price'], (int)$data['stock'], $categoryId);
                redirect('products.php?updated=1');
            }
        }
        require dirname(__DIR__, 2)
            . '/views/products/edit.php';
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Chỉ hỗ trợ POST.');
        }
        $id = positive_id($_POST['id'] ?? null);
        if (!$id) page_not_found('ID sản phẩm không hợp lệ.');
        if (!$this->products->exists($id)) page_not_found('Sản phẩm không tồn tại.');
        $this->products->delete($id);
        redirect('products.php?deleted=1');
    }
}
