<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

// TODO C1 (SV tự làm): tạo constructor nhận PDO và method all(): array.
final class CategoryRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $sql = 'SELECT id, name, slug
                FROM categories
                ORDER BY id DESC';

        return $this->pdo
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, slug FROM categories WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    public function exists(int $id): bool
    {
        return $this->findById($id) !== null;
    }

    public function countProducts(int $id): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM products WHERE category_id = :id'
        );
        $stmt->execute(['id' => $id]);

        return (int) $stmt->fetchColumn();
    }

    public function create(string $name, string $slug): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (name, slug) VALUES (:name, :slug)'
        );
        $stmt->execute(['name' => $name, 'slug' => $slug]);
    }

    public function update(int $id, string $name, string $slug): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE categories SET name = :name, slug = :slug WHERE id = :id'
        );
        $stmt->execute(['name' => $name, 'slug' => $slug, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }


}
