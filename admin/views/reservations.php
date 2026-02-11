<?php
declare(strict_types=1);

/**
 * View: Reservations List
 */

// Helper voor status badges
function statusBadgeClass(string $status): string {
    return match ($status) {
        'approved' => 'bg-green-100 text-green-800',
        'pending'  => 'bg-yellow-100 text-yellow-800',
        'rejected' => 'bg-red-100 text-red-800',
        'returned' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-gray-100 text-gray-800',
        default    => 'bg-gray-100 text-gray-800',
    };
}

function statusLabel(string $status): string {
    return match ($status) {
        'approved' => 'Goedgekeurd',
        'pending'  => 'In afwachting',
        'rejected' => 'Geweigerd',
        'returned' => 'Geretourneerd',
        'cancelled' => 'Geannuleerd',
        default    => ucfirst($status),
    };
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Reserveringen</h1>
            <p class="text-sm text-gray-500 mt-1">Beheer alle uitleningen en aanvragen.</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">ID</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Gebruiker</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Item</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Periode</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Aantal</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Status</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Acties</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p>Nog geen reserveringen gevonden.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $res): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">#<?= (int)$res['id'] ?></span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                            <?= strtoupper(substr($res['user_name'] ?? 'U', 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($res['user_name'] ?? 'Onbekend') ?></div>
                                            <div class="text-xs text-gray-500"><?= htmlspecialchars($res['user_email'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($res['item_name'] ?? 'Onbekend Item') ?></span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d-m-Y', strtotime($res['start_date'])) ?>
                                        <span class="text-gray-400 mx-1">&rarr;</span>
                                        <?= date('d-m-Y', strtotime($res['end_date'])) ?>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        <?php
                                        $days = (strtotime($res['end_date']) - strtotime($res['start_date'])) / (60 * 60 * 24) + 1;
                                        echo (int)$days . ' dagen';
                                        ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900"><?= (int)$res['quantity'] ?></span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= statusBadgeClass($res['status']) ?>">
                                        <?= statusLabel($res['status']) ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= ADMIN_BASE_PATH ?>/reservations/<?= (int)$res['id'] ?>/edit" 
                                           class="p-2 bg-white border border-gray-200 rounded-lg text-gray-600 hover:text-blue-600 hover:border-blue-200 transition shadow-sm"
                                           title="Bewerken">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <form action="<?= ADMIN_BASE_PATH ?>/reservations/<?= (int)$res['id'] ?>/delete" method="POST" onsubmit="return confirm('Weet je zeker dat je deze reservering wilt verwijderen?');" class="inline">
                                            <button type="submit" 
                                                    class="p-2 bg-white border border-gray-200 rounded-lg text-gray-600 hover:text-red-600 hover:border-red-200 transition shadow-sm"
                                                    title="Verwijderen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
