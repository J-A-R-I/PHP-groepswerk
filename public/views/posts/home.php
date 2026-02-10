<?php
declare(strict_types=1);

/**
 * Expected variables:
 * $featured (array|null)
 * $items (array)
 * $categories (array) -> key=Name, value=Count
 */

ob_start();

// Status kleuren configuratie
$statusConfig = [
        'available'   => ['class' => 'bg-green-100 text-green-800 border-green-200', 'label' => 'Beschikbaar'],
        'maintenance' => ['class' => 'bg-orange-100 text-orange-800 border-orange-200', 'label' => 'In onderhoud'],
        'unavailable' => ['class' => 'bg-gray-100 text-gray-600 border-gray-200', 'label' => 'Niet beschikbaar'],
        'lost'        => ['class' => 'bg-red-100 text-red-800 border-red-200', 'label' => 'Verloren'],
];

// Helper functie (veilig gedefinieerd)
if (!function_exists('getStatusBadge')) {
    function getStatusBadge(string $status, array $config): string {
        $data = $config[$status] ?? $config['unavailable'];
        return sprintf('<span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border %s">%s</span>',
                $data['class'],
                $data['label']
        );
    }
}
?>

<?php if ($featured): // Alleen tonen op homepage, niet op catalogus als featured null is ?>
    <div class="grid lg:grid-cols-3 gap-6 mb-10">

        <div class="lg:col-span-2 bg-gray-900 rounded-2xl p-8 md:p-12 flex flex-col justify-center relative overflow-hidden group shadow-lg">
            <div class="relative z-10 max-w-lg">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight">Leen professioneel materiaal.</h1>
                <p class="text-lg text-gray-300 mb-8">Reserveer camera's, laptops en meer voor jouw schoolprojecten.</p>
                <a href="/catalogus" class="inline-block bg-white text-gray-900 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition">
                    Bekijk catalogus
                </a>
            </div>
            <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-gray-800 to-transparent opacity-50 pointer-events-none"></div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                Categorieën
            </h3>
            <div class="space-y-3 flex-1 overflow-y-auto pr-2 custom-scrollbar">
                <?php foreach ($categories as $catName => $count): ?>
                    <div class="flex items-center gap-4 group cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition -mx-2 border border-transparent hover:border-gray-100">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                            <?php if (str_contains(strtolower($catName), 'camera')): ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <?php elseif (str_contains(strtolower($catName), 'laptop')): ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <?php else: ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($catName) ?></h4>
                            <p class="text-xs text-gray-500"><?= (int)$count ?> items</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

    <div class="grid lg:grid-cols-4 gap-6 mb-8">

        <?php if ($featured): ?>
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden flex flex-col md:flex-row items-center gap-6 group hover:shadow-md transition">
                <div class="w-full md:w-1/2 relative">
                    <div class="aspect-w-4 aspect-h-3 bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center">
                        <?php if(!empty($featured['image_filename'])): ?>
                            <img src="uploads/<?= htmlspecialchars($featured['image_filename']) ?>"
                                 alt="<?= htmlspecialchars($featured['name']) ?>"
                                 class="object-contain w-full h-full p-4 transition-transform duration-500 group-hover:scale-110">
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">Geen afbeelding</span>
                        <?php endif; ?>
                    </div>
                    <div class="absolute top-2 left-2">
                        <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded shadow-sm">NIEUW</span>
                    </div>
                </div>
                <div class="w-full md:w-1/2 flex flex-col h-full justify-center">
                    <div class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-1">Uitgelicht</div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2 leading-tight"><?= htmlspecialchars($featured['name']) ?></h2>

                    <div class="mb-3">
                        <?= getStatusBadge($featured['status'], $statusConfig) ?>
                    </div>

                    <p class="text-sm text-gray-500 mb-6 line-clamp-2"><?= htmlspecialchars($featured['brand'] ?? '') ?> - <?= htmlspecialchars($featured['description'] ?? '') ?></p>

                    <button class="bg-gray-900 hover:bg-gray-800 text-white font-medium px-5 py-2.5 rounded-lg transition w-full shadow-lg shadow-gray-200">
                        Nu Reserveren
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $counter = 0;
        // Bepaal max aantal items (4 op home, alles op catalogus)
        $maxItems = ($featured) ? 2 : 999; // Op home 2 items naast de featured (totaal 3 colums in row 1? Nee grid is 4)
        // Correctie: Grid is cols-4. Featured neemt 2 in. Dus nog 2 plekken op rij 1.
        // Laten we gewoon een mooie grid maken.

        foreach ($items as $item):
            // Skip featured
            if ($featured && $item['id'] === $featured['id']) continue;

            // Op home pagina max X items tonen, op catalogus alles
            if ($featured && $counter >= 6) break;
            $counter++;
            ?>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col h-full group hover:shadow-lg transition-all duration-300 relative">

                <div class="mb-4 aspect-square bg-white rounded-xl overflow-hidden relative border border-gray-50 flex items-center justify-center">
                    <?php if(!empty($item['image_filename'])): ?>
                        <img src="uploads/<?= htmlspecialchars($item['image_filename']) ?>"
                             class="w-full h-full object-contain p-2 transition-transform duration-500 group-hover:scale-110"
                             alt="<?= htmlspecialchars($item['name']) ?>">
                    <?php else: ?>
                        <div class="text-gray-300 flex flex-col items-center">
                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    <?php endif; ?>

                    <div class="absolute top-2 right-2">
                 <span class="flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 <?= $item['status'] === 'available' ? 'bg-green-400' : 'hidden' ?>"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 <?= $item['status'] === 'available' ? 'bg-green-500' : ($item['status'] === 'maintenance' ? 'bg-orange-400' : 'bg-gray-400') ?>"></span>
                  </span>
                    </div>
                </div>

                <div class="flex-1 flex flex-col">
                    <div class="mb-1 text-xs text-gray-400 font-semibold uppercase tracking-wider">
                        <?= htmlspecialchars($item['category_name'] ?? 'Overig') ?>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1 leading-snug group-hover:text-blue-600 transition">
                        <?= htmlspecialchars($item['name']) ?>
                    </h3>
                    <p class="text-sm text-gray-500 mb-3 truncate"><?= htmlspecialchars($item['brand'] ?? '') ?></p>

                    <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between gap-2">
                        <?= getStatusBadge($item['status'], $statusConfig) ?>

                        <?php if ($item['status'] === 'available'): ?>
                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                Reserveer &rarr;
                            </button>
                        <?php else: ?>
                            <span class="text-gray-300 text-sm">Unavailable</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';