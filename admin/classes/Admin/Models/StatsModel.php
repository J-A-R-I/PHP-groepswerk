<?php
declare(strict_types=1);

namespace Admin\Models;

use Admin\Core\Database;
use PDO;

/**
 * StatsModel
 *
 * Doel:
 * Levert alle statistieken op voor het admin dashboard.
 * Elke methode voert een specifieke query uit op de database.
 */
class StatsModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * getStats()
     *
     * Doel:
     * Verzamelt alle dashboard-data in één keer en geeft ze terug als array.
     * De DashboardController roept deze methode aan en stuurt het resultaat naar de view.
     */
    public function getStats(): array
    {
        return [
            'totalItems'           => $this->getTotalItems(),
            'pendingReservations'  => $this->getPendingReservations(),
            'popularCategories'    => $this->getPopularCategories(),
            'recentActivity'       => $this->getRecentActivity(),
            'statusOverview'       => $this->getStatusOverview(),
        ];
    }

    /**
     * getTotalItems()
     *
     * Doel:
     * Telt het aantal rijen in de `items` tabel.
     *
     * Resultaat:
     * Een integer met het totaal aantal items.
     */
    private function getTotalItems(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) AS total FROM items');
        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * getPendingReservations()
     *
     * Doel:
     * Telt het aantal reserveringen met status 'pending' (openstaand).
     *
     * Resultaat:
     * Een integer met het aantal openstaande reserveringen.
     */
    private function getPendingReservations(): int
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) AS total FROM reservations WHERE status = 'pending'"
        );
        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    }

    /**
     * getPopularCategories()
     *
     * Doel:
     * Haalt de top 3 categorieën op, gesorteerd op het aantal items erin.
     *
     * Werking:
     * JOIN items op categories, GROUP BY categorie, tel het aantal items.
     *
     * Resultaat:
     * Een array met per categorie: 'name' en 'item_count'.
     */
    private function getPopularCategories(int $limit = 3): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.name, COUNT(i.id) AS item_count
            FROM categories c
            LEFT JOIN items i ON i.category_id = c.id
            GROUP BY c.id, c.name
            ORDER BY item_count DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * getRecentActivity()
     *
     * Doel:
     * Haalt de meest recente reserveringen op met de naam van de gebruiker en het item.
     *
     * Werking:
     * JOIN reservations → users en reservations → items.
     * Sorteert op aanmaakdatum (nieuwste eerst).
     *
     * Resultaat:
     * Een array met per activiteit: 'user_name', 'item_name', 'status' en 'created_at'.
     */
    private function getRecentActivity(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare('
            SELECT
                u.name  AS user_name,
                i.name  AS item_name,
                r.status,
                r.created_at
            FROM reservations r
            JOIN users u ON u.id = r.user_id
            JOIN items i ON i.id = r.item_id
            ORDER BY r.created_at DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * getStatusOverview()
     *
     * Doel:
     * Groepeert items per status en telt hoeveel er per status zijn.
     * Wordt gebruikt voor de donut-chart op het dashboard.
     *
     * Resultaat:
     * Een array met per status: 'status' en 'count'.
     * Bijv. [['status' => 'available', 'count' => 3], ['status' => 'maintenance', 'count' => 1]]
     */
    private function getStatusOverview(): array
    {
        $stmt = $this->pdo->query('
            SELECT status, COUNT(*) AS count
            FROM items
            GROUP BY status
            ORDER BY count DESC
        ');

        return $stmt->fetchAll();
    }
}
