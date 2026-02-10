<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

final class ItemsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function make(): self
    {
        return new self(Database::getConnection());
    }

    public function getAll(): array
    {
        $sql = "SELECT 
                    i.id, 
                    i.category_id, 
                    i.featured_media_id, 
                    i.name, 
                    i.brand, 
                    i.description, 
                    i.status, 
                    i.created_at,
                    c.name as category_name, 
                    m.filename as image_filename,
                    m.path as image_path
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN media m ON i.featured_media_id = m.id
                ORDER BY i.created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * getFiltered()
     *
     * Doel:
     * Haalt items op met optionele filters voor categorie en status.
     * Bouwt dynamisch WHERE-clausules op basis van de meegegeven parameters.
     *
     * Parameters:
     * - $categoryId: optioneel categorie-filter (null = alle categorieën)
     * - $status: optioneel status-filter (null = alle statussen)
     *
     * Resultaat:
     * Array met items, inclusief categorie-naam en media-gegevens via JOINs.
     */
    public function getFiltered(?int $categoryId = null, ?string $status = null): array
    {
        $sql = "SELECT 
                    i.id, 
                    i.category_id, 
                    i.featured_media_id, 
                    i.name, 
                    i.brand, 
                    i.description, 
                    i.status, 
                    i.created_at,
                    c.name as category_name, 
                    m.filename as image_filename,
                    m.path as image_path
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN media m ON i.featured_media_id = m.id";

        // Dynamische WHERE-clausules opbouwen
        $conditions = [];
        $params     = [];

        if ($categoryId !== null) {
            $conditions[] = 'i.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        if ($status !== null && $status !== '') {
            $conditions[] = 'i.status = :status';
            $params['status'] = $status;
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY i.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }


    public function find(int $id): ?array
    {
        $sql = "SELECT 
                    i.*, 
                    c.name as category_name, 
                    m.filename as image_filename,
                    m.path as image_path
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN media m ON i.featured_media_id = m.id
                WHERE i.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getFeatured(): ?array
    {
        $sql = "SELECT 
                    i.*, 
                    c.name as category_name, 
                    m.filename as image_filename,
                    m.path as image_path
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN media m ON i.featured_media_id = m.id
                WHERE i.featured_media_id IS NOT NULL 
                  AND i.status = 'available'
                ORDER BY i.created_at DESC
                LIMIT 1";

        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getCategoryStats(): array
    {
        $sql = "SELECT c.name, COUNT(i.id) as count 
                FROM categories c 
                LEFT JOIN items i ON c.id = i.category_id 
                GROUP BY c.id, c.name
                ORDER BY count DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function create(string $name, ?string $brand, string $description, ?int $categoryId, string $status, ?int $featuredMediaId): int
    {
        $sql = "INSERT INTO items (name, brand, description, category_id, status, featured_media_id, created_at)
                VALUES (:name, :brand, :description, :cat_id, :status, :media_id, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name'        => $name,
            'brand'       => $brand,
            'description' => $description,
            'cat_id'      => $categoryId,
            'status'      => $status,
            'media_id'    => $featuredMediaId
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, ?string $brand, string $description, ?int $categoryId, string $status, ?int $featuredMediaId): void
    {
        $sql = "UPDATE items 
                SET name = :name, 
                    brand = :brand, 
                    description = :description, 
                    category_id = :cat_id, 
                    status = :status, 
                    featured_media_id = :media_id,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id'          => $id,
            'name'        => $name,
            'brand'       => $brand,
            'description' => $description,
            'cat_id'      => $categoryId,
            'status'      => $status,
            'media_id'    => $featuredMediaId
        ]);
    }

    public function delete(int $id): void
    {
        $sql = "DELETE FROM items WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
