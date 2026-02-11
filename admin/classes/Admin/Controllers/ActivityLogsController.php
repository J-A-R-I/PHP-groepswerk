<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\View;
use Admin\Core\Flash;
use Admin\Repositories\ActivityLogsRepository;

/**
 * ActivityLogsController
 * Manages the activity logs view and revert operations.
 */
class ActivityLogsController
{
    private ActivityLogsRepository $logsRepo;
    private string $title = 'Activity Logs';

    public function __construct(ActivityLogsRepository $logsRepo)
    {
        $this->logsRepo = $logsRepo;
    }

    /**
     * Show the logs overview.
     */
    public function index(): void
    {
        $logs = $this->logsRepo->getAll();

        View::render('activity-logs.php', [
            'title' => $this->title,
            'logs'  => $logs,
        ]);
    }

    /**
     * Revert a specific change.
     */
    public function revert(int $id): void
    {
        try {
            $this->logsRepo->revert($id);
            Flash::set('success', 'De wijziging is succesvol teruggedraaid.');
        } catch (\Throwable $e) {
            Flash::set('warning', ['Terugdraaien mislukt: ' . $e->getMessage()]);
        }

        header('Location: ' . ADMIN_BASE_PATH . '/activity-logs');
        exit;
    }
}