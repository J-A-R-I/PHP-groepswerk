<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;

final class ReservationsRepository
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

    /**
     * Haalt alle reservaties op met details voor het admin logboek.
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT 
                    r.*,
                    u.name as user_name,
                    u.email as user_email,
                    i.name as item_name,
                    i.quantity as total_stock
                FROM reservations r
                JOIN users u ON r.user_id = u.id
                JOIN items i ON r.item_id = i.id
                ORDER BY r.created_at DESC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Berekent hoeveel items er nog beschikbaar zijn in een specifieke periode.
     * Formule: Totale Voorraad - (Gereserveerd in overlappende periode)
     */
    public function getAvailableQuantity(int $itemId, string $startDate, string $endDate): int
    {
        // 1. Haal totale voorraad op
        $stmtItem = $this->pdo->prepare("SELECT quantity FROM items WHERE id = :id");
        $stmtItem->execute(['id' => $itemId]);
        $totalStock = (int)$stmtItem->fetchColumn();

        // 2. Tel hoeveel er al verhuurd zijn (overlap check)
        // Overlap: (NieuweStart <= BestaandeEind) EN (NieuweEind >= BestaandeStart)
        $sql = "SELECT COALESCE(SUM(quantity), 0) 
                FROM reservations 
                WHERE item_id = :item_id 
                AND status NOT IN ('rejected', 'returned')
                AND (start_date <= :end_date AND end_date >= :start_date)";

        $stmtRes = $this->pdo->prepare($sql);
        $stmtRes->execute([
            'item_id'    => $itemId,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ]);

        $reservedQuantity = (int)$stmtRes->fetchColumn();

        return max(0, $totalStock - $reservedQuantity);
    }

    public function create(int $userId, int $itemId, int $quantity, string $startDate, string $endDate, ?string $remarks = null): int
    {
        $sql = "INSERT INTO reservations (user_id, item_id, quantity, start_date, end_date, status, remarks, created_at)
                VALUES (:user_id, :item_id, :quantity, :start_date, :end_date, 'approved', :remarks, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id'    => $userId,
            'item_id'    => $itemId,
            'quantity'   => $quantity,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'remarks'    => $remarks
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT 
                    r.*,
                    u.name as user_name,
                    u.email as user_email,
                    i.name as item_name
                FROM reservations r
                JOIN users u ON r.user_id = u.id
                JOIN items i ON r.item_id = i.id
                WHERE r.id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }

    public function update(int $id, string $status, int $quantity, string $startDate, string $endDate, ?string $remarks): void
    {
        $sql = "UPDATE reservations
                SET status = :status,
                    quantity = :quantity,
                    start_date = :start_date,
                    end_date = :end_date,
                    remarks = :remarks
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id'         => $id,
            'status'     => $status,
            'quantity'   => $quantity,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'remarks'    => $remarks
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM reservations WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}