<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

/**
 * ItemsRepository
 *
 * Doel:
 * Beheert alle database-operaties voor de items tabel.
 * Gebruikt LEFT JOINs om gerelateerde categorie- en media-gegevens op te halen.
 */
final class ItemsRepository
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
     * Factory-methode die een nieuwe instantie aanmaakt met de standaard database-connectie.
     * Volgt hetzelfde patroon als PostsRepository en UsersRepository.
     */
    public static function make(): self
    {
        return new self(Database::getConnection());
    }

    /**
     * getAll()
     *
     * Doel:
     * Haalt alle items op met hun categorie-naam en media-bestandsnaam.
     *
     * Werking:
     * - LEFT JOIN op categories: zodat items zonder categorie ook getoond worden.
     * - LEFT JOIN op media: zodat items zonder afbeelding ook getoond worden.
     * - Sorteert op aanmaakdatum (nieuwste eerst).
     *
     * Resultaat:
     * Array met per item: id, name, brand, description, status, created_at,
     * category_name (kan NULL zijn), media_filename en media_path (kunnen NULL zijn).
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query('
            SELECT
                i.id,
                i.name,
                i.brand,
                i.description,
                i.status,
                i.created_at,
                c.name        AS category_name,
                m.filename    AS media_filename,
                m.path        AS media_path
            FROM items i
            LEFT JOIN categories c ON c.id = i.category_id
            LEFT JOIN media m      ON m.id = i.featured_media_id
            ORDER BY i.created_at DESC
        ');

        return $stmt->fetchAll();
    }

    /**
     * find()
     *
     * Doel:
     * Zoekt één item op basis van ID, inclusief gerelateerde categorie en media.
     *
     * Resultaat:
     * Associatieve array met item-data, of null als het item niet bestaat.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                i.id,
                i.name,
                i.brand,
                i.description,
                i.status,
                i.created_at,
                i.category_id,
                i.featured_media_id,
                c.name        AS category_name,
                m.filename    AS media_filename,
                m.path        AS media_path
            FROM items i
            LEFT JOIN categories c ON c.id = i.category_id
            LEFT JOIN media m      ON m.id = i.featured_media_id
            WHERE i.id = :id
        ');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
