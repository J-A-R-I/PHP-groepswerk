<?php
declare(strict_types=1);

/**
 * Dashboard View — ToolTrack Admin
 *
 * Doel:
 * Toont het hoofddashboard met 5 widgets:
 * 1. Totaal Items (aantal items in de database)
 * 2. Openstaande Reserveringen (status = pending)
 * 3. Populaire Categorieën (top 3 met item-aantallen)
 * 4. Recente Activiteit (laatste reserveringen)
 * 5. Status Overzicht (donut-chart met item-statussen)
 *
 * Variabelen beschikbaar via View::render():
 * - $stats['totalItems']          → int
 * - $stats['pendingReservations'] → int
 * - $stats['popularCategories']   → array met 'name' en 'item_count'
 * - $stats['recentActivity']      → array met 'user_name', 'item_name', 'status', 'created_at'
 * - $stats['statusOverview']      → array met 'status' en 'count'
 */

// Data uit de $stats array halen voor overzichtelijkheid in de template
$totalItems          = (int) ($stats['totalItems'] ?? 0);
$pendingReservations = (int) ($stats['pendingReservations'] ?? 0);
$popularCategories   = $stats['popularCategories'] ?? [];
$recentActivity      = $stats['recentActivity'] ?? [];
$statusOverview      = $stats['statusOverview'] ?? [];

/**
 * Status-labels vertalen naar Nederlands.
 * Wordt gebruikt in de activiteitsfeed en de chart-legenda.
 */
$statusLabels = [
    'available'   => 'Available',
    'maintenance' => 'Maintenance',
    'lost'        => 'Verloren',
    'retired'     => 'Buiten dienst',
];

/**
 * Kleuren per status voor de donut-chart.
 * Groen = beschikbaar, oranje = onderhoud, rood = verloren, grijs = buiten dienst.
 */
$statusColors = [
    'available'   => '#4ade80',
    'maintenance' => '#fb923c',
    'lost'        => '#f87171',
    'retired'     => '#94a3b8',
];

// Chart-data voorbereiden: labels, waarden en kleuren
$chartLabels = [];
$chartValues = [];
$chartColors = [];

foreach ($statusOverview as $row) {
    $key = $row['status'];
    $chartLabels[] = $statusLabels[$key] ?? ucfirst($key);
    $chartValues[] = (int) $row['count'];
    $chartColors[] = $statusColors[$key] ?? '#94a3b8';
}
?>

<!-- Dashboard grid layout -->
<section class="p-6 lg:p-8">

    <!-- === RIJ 1: Twee statistiek-kaarten naast elkaar === -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <!-- Kaart: Totaal Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Totaal Items</h3>
                <!-- Camera icoon -->
                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-4xl font-extrabold text-gray-900"><?= $totalItems ?></p>
        </div>

        <!-- Kaart: Openstaande Reserveringen (licht oranje accent) -->
        <div class="bg-white rounded-2xl shadow-sm border-2 border-orange-200 p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Openstaande Reserveringen</h3>
                <!-- Klok icoon in oranje -->
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-4xl font-extrabold text-gray-900"><?= $pendingReservations ?></p>
        </div>

    </div>

    <!-- === RIJ 2: Drie widget-kaarten naast elkaar === -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Kaart: Populaire Categorieën -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-5">Populaire Categorieën</h3>

            <?php if (empty($popularCategories)): ?>
                <p class="text-sm text-gray-400">Geen categorieën gevonden.</p>
            <?php else: ?>
                <ul class="space-y-3">
                    <?php foreach ($popularCategories as $cat): ?>
                        <li class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <!-- Map-icoon -->
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
                                </svg>
                                <span class="text-sm text-gray-700"><?= htmlspecialchars($cat['name']) ?></span>
                            </div>
                            <span class="text-sm text-gray-400">(<?= (int) $cat['item_count'] ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Kaart: Recente Activiteit -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-5">Recente Activiteit</h3>

            <?php if (empty($recentActivity)): ?>
                <p class="text-sm text-gray-400">Geen recente activiteit.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentActivity as $activity): ?>
                        <div>
                            <p class="text-sm text-gray-800">
                                <!-- Formaat: "[Gebruiker] reserveerde [Item]" -->
                                <span class="font-semibold"><?= htmlspecialchars($activity['user_name']) ?></span>
                                reserveerde
                                <span class="font-semibold"><?= htmlspecialchars($activity['item_name']) ?></span>
                            </p>
                            <!-- Datum/tijd formatteren: "26 april 2023, 14:30" -->
                            <p class="text-xs text-orange-400 mt-0.5">
                                <?php
                                    $date = new DateTime($activity['created_at']);
                                    // Nederlandse maandnamen
                                    $dutchMonths = [
                                        1 => 'januari', 2 => 'februari', 3 => 'maart',
                                        4 => 'april',   5 => 'mei',      6 => 'juni',
                                        7 => 'juli',    8 => 'augustus',  9 => 'september',
                                        10 => 'oktober', 11 => 'november', 12 => 'december',
                                    ];
                                    $month = $dutchMonths[(int) $date->format('n')];
                                    echo $date->format('d') . ' ' . $month . ' ' . $date->format('Y') . ', ' . $date->format('H:i');
                                ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php
            /**
             * Extra: status-wijzigingen tonen.
             * Als er items in 'maintenance' staan, tonen we dat als activiteit.
             */
            if (!empty($statusOverview)):
                foreach ($statusOverview as $row):
                    if ($row['status'] !== 'available'):
            ?>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-600">
                                <?php
                                // Zoek een item dat in maintenance staat voor de activiteitsfeed
                                // Dit is een visuele weergave zoals op het screenshot
                                ?>
                            </p>
                        </div>
            <?php
                        break; // Toon maar 1 extra statusmelding
                    endif;
                endforeach;
            endif;
            ?>
        </div>

        <!-- Kaart: Status Overzicht (Donut chart) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-5">Status Overzicht</h3>

            <!-- Container voor de ApexCharts donut -->
            <div id="statusChart" class="flex justify-center"></div>

            <!-- Legenda onder de chart -->
            <div class="flex flex-wrap justify-center gap-x-5 gap-y-2 mt-4">
                <?php foreach ($statusOverview as $row): ?>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full inline-block"
                              style="background-color: <?= $statusColors[$row['status']] ?? '#94a3b8' ?>"></span>
                        <span class="text-sm text-gray-600">
                            <?= $statusLabels[$row['status']] ?? ucfirst($row['status']) ?>
                            <span class="text-gray-400">(<?= (int) $row['count'] ?>)</span>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</section>

<!-- ApexCharts donut-chart initialisatie -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        /**
         * ApexCharts configuratie voor de donut-chart.
         * De data komt uit PHP via json_encode.
         */
        var options = {
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'Inter, sans-serif',
            },
            series: <?= json_encode($chartValues) ?>,
            labels: <?= json_encode($chartLabels) ?>,
            colors: <?= json_encode($chartColors) ?>,
            legend: {
                show: false // We gebruiken onze eigen legenda
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '55%'
                    }
                }
            },
            stroke: {
                width: 2,
                colors: ['#ffffff']
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' items';
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#statusChart"), options);
        chart.render();
    });
</script>
