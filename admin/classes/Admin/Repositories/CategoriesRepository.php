<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

/**
 * CategoriesRepository
 *
 * Doel:
 * Beheert database-operaties voor de categorieën-tabel.
 * Wordt o.a. gebruikt om de categorie-dropdown in het item-formulier te vullen.
 */
final class CategoriesRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * make()
     *
     * Doel:
     * Factory-methode om snel een instantie aan te maken met de standaard PDO connectie.
     */
    public static function make(): self
    {
        return new self(Database::getConnection());
    }

    /**
     * getAll()
     *
     * Doel:
     * Haalt alle categorieën op, alfabetisch gesorteerd.
     * Wordt gebruikt voor select-dropdowns in formulieren.
     *
     * Resultaat:
     * Array met per categorie: 'id' en 'name'.
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM categories ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
