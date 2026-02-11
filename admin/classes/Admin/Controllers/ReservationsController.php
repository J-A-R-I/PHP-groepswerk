<?php
declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Core\Flash;
use Admin\Core\View;
use Admin\Repositories\ReservationsRepository;

class ReservationsController
{
    private ReservationsRepository $reservationsRepository;

    public function __construct(ReservationsRepository $reservationsRepository)
    {
        $this->reservationsRepository = $reservationsRepository;
    }

    public function index(): void
    {
        $reservations = $this->reservationsRepository->getAllWithDetails();

        View::render('reservations.php', [
            'reservations' => $reservations,
            'title'        => 'Reserveringen Beheer',
        ]);
    }

    public function edit(int $id): void
    {
        $reservation = $this->reservationsRepository->find($id);

        if (!$reservation) {
            Flash::set('error', 'Reservering niet gevonden.');
            header('Location: ' . ADMIN_BASE_PATH . '/reservations');
            exit;
        }

        View::render('reservation-edit.php', [
            'reservation' => $reservation,
            'title'       => 'Reservering Bewerken',
        ]);
    }

    public function update(int $id): void
    {
        $status    = trim((string)($_POST['status'] ?? ''));
        $quantity  = (int)($_POST['quantity'] ?? 1);
        $startDate = (string)($_POST['start_date'] ?? '');
        $endDate   = (string)($_POST['end_date'] ?? '');
        $remarks   = trim((string)($_POST['remarks'] ?? ''));

        // Validatie
        $errors = [];
        $validStatuses = ['pending', 'approved', 'rejected', 'returned', 'cancelled'];
        
        if (!in_array($status, $validStatuses, true)) {
            $errors[] = 'Ongeldige status.';
        }
        if ($quantity < 1) {
            $errors[] = 'Aantal moet minimaal 1 zijn.';
        }
        if (strtotime($startDate) === false || strtotime($endDate) === false) {
            $errors[] = 'Ongeldige datums.';
        }
        if (strtotime($endDate) < strtotime($startDate)) {
            $errors[] = 'Einddatum moet na startdatum liggen.';
        }

        if (!empty($errors)) {
            Flash::set('error', implode(' ', $errors));
            header('Location: ' . ADMIN_BASE_PATH . '/reservations/' . $id . '/edit');
            exit;
        }

        $this->reservationsRepository->update($id, $status, $quantity, $startDate, $endDate, $remarks);

        Flash::set('success', 'Reservering bijgewerkt.');
        header('Location: ' . ADMIN_BASE_PATH . '/reservations');
        exit;
    }

    public function delete(int $id): void
    {
        $this->reservationsRepository->delete($id);
        Flash::set('success', 'Reservering verwijderd.');
        header('Location: ' . ADMIN_BASE_PATH . '/reservations');
        exit;
    }
}
