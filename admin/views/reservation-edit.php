<?php
declare(strict_types=1);

/**
 * View: Reservation Edit
 * Verwacht: $reservation (array)
 */

$statusOptions = [
    'pending'   => 'In afwachting',
    'approved'  => 'Goedgekeurd',
    'rejected'  => 'Geweigerd',
    'returned'  => 'Geretourneerd',
    'cancelled' => 'Geannuleerd',
];
?>

<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <a href="<?= ADMIN_BASE_PATH ?>/reservations" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-6 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Terug naar overzicht
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="p-8 border-b border-gray-100 bg-gray-50/50">
            <h1 class="text-xl font-bold text-gray-900">Reservering #<?= (int)$reservation['id'] ?> Bewerken</h1>
            <p class="text-sm text-gray-500 mt-1">
                Item: <span class="font-medium text-gray-900"><?= htmlspecialchars($reservation['item_name'] ?? '') ?></span> | 
                User: <span class="font-medium text-gray-900"><?= htmlspecialchars($reservation['user_name'] ?? '') ?></span>
            </p>
        </div>

        <form action="<?= ADMIN_BASE_PATH ?>/reservations/<?= (int)$reservation['id'] ?>/update" method="POST" class="p-8 space-y-6">
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                    <div class="relative">
                        <select id="status" name="status" class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition appearance-none">
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $reservation['status'] === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1.5">Aantal</label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           value="<?= (int)$reservation['quantity'] ?>" 
                           min="1" 
                           required
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1.5">Start Datum</label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="<?= htmlspecialchars($reservation['start_date']) ?>" 
                           required 
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition">
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1.5">Eind Datum</label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="<?= htmlspecialchars($reservation['end_date']) ?>" 
                           required 
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition">
                </div>
            </div>

            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1.5">Opmerkingen</label>
                <textarea id="remarks" 
                          name="remarks" 
                          rows="3" 
                          class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition resize-none"><?= htmlspecialchars((string)$reservation['remarks']) ?></textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white font-semibold py-3 px-6 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Opslaan</span>
                </button>
            </div>

        </form>
    </div>
</div>
