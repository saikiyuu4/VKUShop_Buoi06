<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT p.id, p.name, p.price,
            p.stock,
            c.name AS category_name
            FROM products p
            JOIN categories c
            ON c.id = p.category_id
            ORDER BY p.id DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }
    public function exists(int $id): bool
    {
        return $this->findById($id) !== null;
    }
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    public function create(string $name, int $price, int $stock, int $categoryId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (name, price, stock, category_id)
             VALUES (:name, :price, :stock, :category_id)'
        );
        $stmt->execute([
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'category_id' => $categoryId,
        ]);
    }

    public function update(int $id, string $name, int $price, int $stock, int $categoryId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products
             SET name = :name, price = :price, stock = :stock, category_id = :category_id
             WHERE id = :id'
        );
        $stmt->execute([
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'category_id' => $categoryId,
            'id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
