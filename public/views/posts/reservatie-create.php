<?php
declare(strict_types=1);

/**
 * View: Reservation Create
 * Verwacht: $item (array), $error (string|null), $defaultStart (string)
 */

ob_start();
?>

    <div class="max-w-4xl mx-auto">
        <a href="/catalogus" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-6 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Terug naar catalogus
        </a>

        <div class="grid md:grid-cols-3 gap-8">

            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="aspect-square bg-gray-50 flex items-center justify-center p-4">
                        <?php if(!empty($item['image_filename'])): ?>
                            <img src="/uploads/<?= htmlspecialchars($item['image_filename']) ?>"
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 class="w-full h-full object-contain">
                        <?php else: ?>
                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <div class="text-xs text-blue-600 font-bold uppercase tracking-wide mb-1">
                            <?= htmlspecialchars($item['category_name'] ?? 'Item') ?>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($item['name']) ?></h2>
                        <p class="text-sm text-gray-500 mb-2"><?= htmlspecialchars($item['brand'] ?? '') ?></p>

                        <?php if ((int)$item['available_stock'] > 0): ?>
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Nog beschikbaar: <?= (int)$item['available_stock'] ?>
                            </div>
                        <?php else: ?>
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Momenteel niet op voorraad
                            </div>
                        <?php endif; ?>

                        <hr class="my-4 border-gray-100">
                        <p class="text-sm text-gray-600 leading-relaxed">
                            <?= htmlspecialchars($item['description'] ?? 'Geen beschrijving beschikbaar.') ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Reservering plaatsen</h1>
                    <p class="text-gray-500 mb-6">Kies een periode en aantal.</p>

                    <?php if (isset($error)): ?>
                        <div class="mb-6 p-4 border border-red-200 bg-red-50 text-red-700 rounded-lg text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/reserve" class="space-y-6">
                        <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2" for="start_date">Van</label>
                                <input type="date"
                                       id="start_date"
                                       name="start_date"
                                       required
                                       min="<?= date('Y-m-d') ?>"
                                       value="<?= htmlspecialchars($_POST['start_date'] ?? $defaultStart ?? date('Y-m-d')) ?>"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2" for="end_date">Tot</label>
                                <input type="date"
                                       id="end_date"
                                       name="end_date"
                                       required
                                       min="<?= date('Y-m-d') ?>"
                                       value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="quantity">Aantal</label>
                            <select name="quantity" id="quantity" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition bg-white">
                                <?php 
                                $maxQty = max(1, (int)$item['available_stock']);
                                for($i=1; $i <= $maxQty; $i++): 
                                ?>
                                    <option value="<?= $i ?>" <?= (isset($_POST['quantity']) && $_POST['quantity'] == $i) ? 'selected' : '' ?>>
                                        <?= $i ?> stuks
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">
                                Maximaal <?= (int)$item['available_stock'] ?> stuks beschikbaar.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="remarks">Opmerkingen (optioneel)</label>
                            <textarea id="remarks"
                                      name="remarks"
                                      rows="3"
                                      placeholder="Bijv. Projectnaam..."
                                      class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition resize-none"><?= htmlspecialchars($_POST['remarks'] ?? '') ?></textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-blue-900 text-white font-bold py-4 rounded-xl hover:bg-blue-800 transition shadow-lg shadow-blue-900/10 flex items-center justify-center gap-2">
                                <span>Bevestig Reservering</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';