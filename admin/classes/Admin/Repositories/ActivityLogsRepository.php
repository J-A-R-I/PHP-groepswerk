<?php
declare(strict_types=1);

namespace Admin\Repositories;

use Admin\Core\Database;
use PDO;
use RuntimeException;

/**
 * ActivityLogsRepository
 * Handles tracking of CRUD operations and rollback functionality.
 */
final class ActivityLogsRepository
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
     * Store a new activity log entry.
     */
    public function log(int $userId, string $action, string $entityType, int $entityId, ?array $oldData, ?array $newData): void
    {
        $sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, old_data, new_data, ip_address, created_at)
                VALUES (:user_id, :action, :entity_type, :entity_id, :old_data, :new_data, :ip_address, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_data'    => $oldData ? json_encode($oldData) : null,
            'new_data'    => $newData ? json_encode($newData) : null,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ]);
    }

    /**
     * Get all logs with user details.
     */
    public function getAll(): array
    {
        $sql = "SELECT 
                    l.*, 
                    u.name as admin_name,
                    r.name as reverted_by_name
                FROM activity_logs l
                LEFT JOIN users u ON l.user_id = u.id
                LEFT JOIN users r ON l.reverted_by = r.id
                ORDER BY l.created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Find a single log entry.
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM activity_logs WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Revert an action (rollback).
     */
    public function revert(int $logId): void
    {
        $log = $this->find($logId);
        if (!$log) {
            throw new RuntimeException("Log ID {$logId} niet gevonden.");
        }

        $entityId = (int)$log['entity_id'];
        $entityType = $log['entity_type'];
        $action = $log['action'];
        $oldData = $log['old_data'] ? json_decode($log['old_data'], true) : null;
        $table = $this->mapEntityTypeToTable($entityType);

        $this->pdo->beginTransaction();

        try {
            switch ($action) {
                case 'create':
                    // Reverting a 'create' means deleting the entity
                    $this->deleteEntity($table, $entityId);
                    break;

                case 'update':
                    // Reverting an 'update' means restoring old_data
                    if (!$oldData) {
                        throw new RuntimeException("Geen 'old_data' beschikbaar voor rollback.");
                    }
                    $this->updateEntity($table, $entityId, $oldData);
                    break;

                case 'delete':
                    // Reverting a 'delete' means re-inserting the old_data
                    if (!$oldData) {
                        throw new RuntimeException("Geen 'old_data' beschikbaar voor herstel.");
                    }
                    $this->insertEntity($table, $oldData);
                    break;
            }

            // Mark log as reverted
            $updateStmt = $this->pdo->prepare("
                UPDATE activity_logs 
                SET reverted_at = NOW(), reverted_by = :user_id 
                WHERE id = :id
            ");
            $updateStmt->execute([
                'user_id' => $_SESSION['user_id'] ?? null,
                'id'      => $logId
            ]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function mapEntityTypeToTable(string $entityType): string
    {
        $mapping = [
            'item'        => 'items',
            'category'    => 'categories',
            'user'        => 'users',
            'post'        => 'posts',
            'reservation' => 'reservations'
        ];

        if (!isset($mapping[$entityType])) {
            throw new RuntimeException("Onbekend entiteitstype: {$entityType}");
        }

        return $mapping[$entityType];
    }

    private function deleteEntity(string $table, int $id): void
    {
        $sql = "DELETE FROM `{$table}` WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    private function updateEntity(string $table, int $id, array $data): void
    {
        $set = [];
        $params = ['id' => $id];

        foreach ($data as $column => $value) {
            if ($column === 'id' || $column === 'created_at' || $column === 'updated_at') {
                continue;
            }
            $set[] = "`{$column}` = :{$column}";
            $params[$column] = $value;
        }

        if (empty($set)) {
            return;
        }

        $sql = "UPDATE `{$table}` SET " . implode(', ', $set) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    private function insertEntity(string $table, array $data): void
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $sql = "INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }
}