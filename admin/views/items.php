<?php
declare(strict_types=1);

/**
 * Items Overzicht View — ToolTrack Admin
 *
 * Doel:
 * Toont alle items in een overzichtelijke tabel met filtermogelijkheden.
 * Inclusief categorie-naam, status-badge en thumbnail (of placeholder).
 *
 * Variabelen beschikbaar via View::render():
 * - $items            → array met items (gefilterd of alle)
 * - $categories       → array met categorieën voor de filter-dropdown
 * - $filterCategoryId → actieve categorie-filter (int of null)
 * - $filterStatus     → actieve status-filter (string of null)
 */

// Filter-variabelen veilig initialiseren
$categories       = $categories ?? [];
$filterCategoryId = $filterCategoryId ?? null;
$filterStatus     = $filterStatus ?? null;
$hasActiveFilters = ($filterCategoryId !== null || $filterStatus !== null);

/**
 * statusBadgeClass()
 *
 * Doel:
 * Geeft de juiste TailwindCSS classes terug voor een status-badge.
 * Elke status heeft een eigen kleur: groen, oranje, rood, of grijs.
 */
function statusBadgeClass(string $status): string
{
    return match ($status) {
        'available'   => 'bg-green-100 text-green-700',
        'maintenance' => 'bg-orange-100 text-orange-700',
        'lost'        => 'bg-red-100 text-red-700',
        'retired'     => 'bg-gray-100 text-gray-600',
        default       => 'bg-gray-100 text-gray-600',
    };
}

/**
 * statusLabel()
 *
 * Doel:
 * Vertaalt de Engelse status-waarde naar een leesbaar label.
 */
function statusLabel(string $status): string
{
    return match ($status) {
        'available'   => 'Beschikbaar',
        'maintenance' => 'Onderhoud',
        'lost'        => 'Verloren',
        'retired'     => 'Buiten dienst',
        default       => ucfirst($status),
    };
}
?>

<!-- Items pagina layout -->
<section class="p-6 lg:p-8">

    <!-- Paginakop: Titel + Actieknop -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Items Beheer</h1>
            <p class="text-sm text-gray-500 mt-1">Overzicht van alle items in het systeem</p>
        </div>
        <!-- "Nieuw Item" knop -->
        <a href="<?= ADMIN_BASE_PATH ?>/items/create"
           class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nieuw Item
        </a>
    </div>

    <!-- Filter Toolbar -->
    <form method="GET" action="<?= ADMIN_BASE_PATH ?>/items" class="mb-4">
        <div class="flex flex-wrap items-center gap-3">

            <!-- Filter icoon -->
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                </svg>
                Filters
            </div>

            <!-- Categorie filter dropdown -->
            <select name="category_id"
                    onchange="this.form.submit()"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200 appearance-none pr-8"
                    style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%239ca3af%22 stroke-width=%222%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19.5 8.25l-7.5 7.5-7.5-7.5%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1rem;">
                <option value="">Alle Categorieën</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>"
                        <?= ($filterCategoryId === (int)$cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Status filter dropdown -->
            <select name="status"
                    onchange="this.form.submit()"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200 appearance-none pr-8"
                    style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%239ca3af%22 stroke-width=%222%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19.5 8.25l-7.5 7.5-7.5-7.5%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1rem;">
                <option value="">Alle Statussen</option>
                <option value="available"   <?= ($filterStatus === 'available')   ? 'selected' : '' ?>>Beschikbaar</option>
                <option value="maintenance" <?= ($filterStatus === 'maintenance') ? 'selected' : '' ?>>Onderhoud</option>
                <option value="lost"        <?= ($filterStatus === 'lost')        ? 'selected' : '' ?>>Verloren</option>
                <option value="retired"     <?= ($filterStatus === 'retired')     ? 'selected' : '' ?>>Buiten dienst</option>
            </select>

            <?php if ($hasActiveFilters): ?>
                <!-- Filters wissen knop, alleen zichtbaar als er actieve filters zijn -->
                <a href="<?= ADMIN_BASE_PATH ?>/items"
                   class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Filters wissen
                </a>
            <?php endif; ?>

        </div>
    </form>

    <!-- Tabel in een witte kaart -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <?php if (empty($items)): ?>
            <!-- Lege staat: geen items gevonden -->
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H2.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
                <p class="text-gray-500 font-medium">Geen items gevonden</p>
                <p class="text-sm text-gray-400 mt-1">Voeg een nieuw item toe om te beginnen.</p>
            </div>
        <?php else: ?>
            <!-- Tabel met item-gegevens -->
            <table class="w-full">
                <!-- Tabelkop -->
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Afbeelding</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Product</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Categorie</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Voorraad</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Aangemaakt</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Acties</th>
                    </tr>
                </thead>

                <!-- Tabelinhoud: één rij per item -->
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors duration-150">

                            <td class="px-6 py-4">
                                <?php if (!empty($item['image_filename'])): ?>
                                    <img src="/<?= htmlspecialchars($item['image_path']) ?>/<?= htmlspecialchars($item['image_filename']) ?>"
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         class="w-10 h-10 rounded-lg object-cover border border-gray-100">
                                <?php else: ?>
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Kolom: Product (naam + merk) -->
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($item['name']) ?></p>
                                <?php if (!empty($item['brand'])): ?>
                                    <p class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($item['brand']) ?></p>
                                <?php endif; ?>
                            </td>

                            <!-- Kolom: Categorie -->
                            <td class="px-6 py-4">
                                <?php if (!empty($item['category_name'])): ?>
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($item['category_name']) ?></span>
                                <?php else: ?>
                                    <span class="text-sm text-gray-400 italic">Geen categorie</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900"><?= (int)($item['quantity'] ?? 0) ?></span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium <?= statusBadgeClass($item['status']) ?>">
                                    <?= statusLabel($item['status']) ?>
                                </span>
                            </td>

                            <!-- Kolom: Aanmaakdatum -->
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                                </span>
                            </td>

                            <!-- Kolom: Acties (Bewerk + Verwijder) -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Bewerk knop -->
                                    <a href="<?= ADMIN_BASE_PATH ?>/items/<?= (int) $item['id'] ?>/edit"
                                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors duration-200"
                                       title="Item bewerken">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                        Bewerk
                                    </a>

                                    <!-- Verwijder knop -->
                                    <form method="POST" action="<?= ADMIN_BASE_PATH ?>/items/<?= (int) $item['id'] ?>/delete"
                                          onsubmit="return confirm('Weet je zeker dat je dit item wilt verwijderen?');">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 transition-colors duration-200"
                                                title="Item verwijderen">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                            Verwijder
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Tabel footer: totaal aantal items -->
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    <?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?> gevonden
                </p>
            </div>
        <?php endif; ?>

    </div>

</section>
