<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CategoryRepository;

// TODO C3 (SV tự làm): constructor CategoryRepository và method index().
final class CategoryController
{
    public function __construct(
        private CategoryRepository $categories
    ) {}

    public function index(): void
    {
        $categories = $this->categories->all();

        require dirname(__DIR__, 2)
            . '/views/categories/index.php';
    }

    public function create(): void
    {
        $name = $slug = '';
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $slug = trim((string) ($_POST['slug'] ?? ''));
            if ($name === '') $errors['name'] = 'Vui lòng nhập tên danh mục.';
            elseif (!preg_match('/^.{1,100}$/us', $name)) $errors['name'] = 'Tên danh mục tối đa 100 ký tự.';
            if (strlen($slug) > 120 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) $errors['slug'] = 'Slug tối đa 120 ký tự, chỉ gồm a-z, 0-9 và dấu gạch ngang.';
            if (!$errors) {
                try {
                    $this->categories->create($name, $slug);
                    redirect('categories.php?created=1');
                } catch (PDOException $exception) {
                    if (is_duplicate_key($exception)) $errors['slug'] = 'Slug đã tồn tại.';
                    else throw $exception;
                }
            }
        }
        require dirname(__DIR__, 2)
            . '/views/categories/create.php';
    }
    public function edit(): void
    {
        $id = positive_id($_GET['id'] ?? null);
        if (!$id) page_not_found('ID danh mục không hợp lệ.');

        $category = $this->categories->findById($id);
        if (!$category) page_not_found('Danh mục không tồn tại.');

        $name = (string) $category['name'];
        $slug = (string) $category['slug'];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $slug = trim((string) ($_POST['slug'] ?? ''));
            if ($name === '') $errors['name'] = 'Vui lòng nhập tên danh mục.';
            elseif (!preg_match('/^.{1,100}$/us', $name)) $errors['name'] = 'Tên danh mục tối đa 100 ký tự.';
            if (strlen($slug) > 120 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) $errors['slug'] = 'Slug tối đa 120 ký tự, chỉ gồm a-z, 0-9 và dấu gạch ngang.';
            if (!$errors) {
                try {
                    $this->categories->update($id, $name, $slug);
                    redirect('categories.php?updated=1');
                } catch (PDOException $exception) {
                    if (is_duplicate_key($exception)) $errors['slug'] = 'Slug đã tồn tại.';
                    else throw $exception;
                }
            }
        }

        require dirname(__DIR__, 2)
            . '/views/categories/edit.php';
    }

    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Chỉ hỗ trợ POST.');
        }

        $id = positive_id($_POST['id'] ?? null);
        if (!$id) page_not_found('ID danh mục không hợp lệ.');
        if (!$this->categories->exists($id)) page_not_found('Danh mục không tồn tại.');
        if ($this->categories->countProducts($id) > 0) redirect('categories.php?error=in_use');

        try {
            $this->categories->delete($id);
        } catch (PDOException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1451) redirect('categories.php?error=in_use');
            throw $exception;
        }

        redirect('categories.php?deleted=1');
    }
}
