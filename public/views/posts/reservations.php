<?php
declare(strict_types=1);

use Admin\Repositories\ReservationsRepository;
use Admin\Core\Database;

$reservationsRepo = new ReservationsRepository(Database::getConnection());
$reservations = $reservationsRepo->getAllWithDetails();
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reservatie Logboek</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                    <th class="px-6 py-4">Gebruiker</th>
                    <th class="px-6 py-4">Item</th>
                    <th class="px-6 py-4 text-center">Aantal</th>
                    <th class="px-6 py-4">Periode</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actie</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                <?php if(empty($reservations)): ?>
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Geen reservaties gevonden.</td></tr>
                <?php else: ?>
                    <?php foreach($reservations as $res): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?= htmlspecialchars($res['user_name']) ?><br>
                                <span class="text-xs text-gray-400 font-normal"><?= htmlspecialchars($res['user_email']) ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?= htmlspecialchars($res['item_name']) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= (int)$res['quantity'] ?> / <?= (int)$res['total_stock'] ?>
                                    </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= date('d-m-Y', strtotime($res['start_date'])) ?> <span class="text-gray-300 mx-1">tot</span> <?= date('d-m-Y', strtotime($res['end_date'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $statusClasses = [
                                    'pending'  => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'returned' => 'bg-gray-100 text-gray-800',
                                ];
                                $cls = $statusClasses[$res['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-md <?= $cls ?>">
                                        <?= htmlspecialchars($res['status']) ?>
                                    </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-400 hover:text-blue-600">Details</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>