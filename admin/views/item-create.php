<?php
declare(strict_types=1);

/**
 * Item Aanmaken View — ToolTrack Admin
 *
 * Doel:
 * Toont het formulier om een nieuw item aan te maken.
 * Twee kolommen: links algemene info, rechts status + afbeelding.
 *
 * Variabelen beschikbaar via View::render():
 * - $categories → array met per categorie: 'id' en 'name'
 * - $old        → array met vorige formulierwaarden (na gefaalde validatie)
 */

$categories = $categories ?? [];
$old = $old ?? [
    'name'        => '',
    'brand'       => '',
    'description' => '',
    'category_id' => '',
    'quantity'    => 1,
    'status'      => 'available',
];
?>

<!-- Item aanmaken pagina -->
<section class="p-6 lg:p-8">

    <!-- Paginakop -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nieuw Item</h1>
        <p class="text-sm text-gray-500 mt-1">Vul de gegevens in om een nieuw item aan te maken</p>
    </div>

    <!-- Formulier -->
    <form action="<?= ADMIN_BASE_PATH ?>/items/store" method="POST" enctype="multipart/form-data">

        <!-- Twee kolommen grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- === LINKER KOLOM (2/3): Algemene informatie === -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-5">Algemene Informatie</h2>

                    <div class="space-y-5">

                        <!-- Naam (verplicht) -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Naam <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="<?= htmlspecialchars((string)$old['name']) ?>"
                                   placeholder="Bijv. Canon EOS 5D Mark IV"
                                   required
                                   class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200">
                        </div>

                        <!-- Merk -->
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Merk
                            </label>
                            <input type="text"
                                   id="brand"
                                   name="brand"
                                   value="<?= htmlspecialchars((string)$old['brand']) ?>"
                                   placeholder="Bijv. Canon, Apple, GoPro"
                                   class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200">
                        </div>

                        <!-- Categorie (verplicht) -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Categorie <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id"
                                    name="category_id"
                                    required
                                    class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200 appearance-none"
                                    style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%239ca3af%22 stroke-width=%222%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19.5 8.25l-7.5 7.5-7.5-7.5%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25rem;">
                                <option value="">Selecteer een categorie...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>"
                                        <?= ((string)$old['category_id'] === (string)$cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Beschrijving -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Beschrijving
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Beschrijf het item..."
                                      class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200 resize-none"><?= htmlspecialchars((string)$old['description']) ?></textarea>
                        </div>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Voorraad <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="quantity"
                                   name="quantity"
                                   value="<?= (int)$old['quantity'] ?>"
                                   min="1"
                                   required
                                   class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200">
                        </div>

                    </div>
                </div>
            </div>

            <!-- === RECHTER KOLOM (1/3): Status + Afbeelding === -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Kaart: Status -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-5">Status</h2>

                    <select id="status"
                            name="status"
                            class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200 appearance-none"
                            style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%239ca3af%22 stroke-width=%222%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19.5 8.25l-7.5 7.5-7.5-7.5%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25rem;">
                        <option value="available"   <?= ($old['status'] === 'available')   ? 'selected' : '' ?>>Beschikbaar</option>
                        <option value="maintenance" <?= ($old['status'] === 'maintenance') ? 'selected' : '' ?>>Onderhoud</option>
                        <option value="lost"        <?= ($old['status'] === 'lost')        ? 'selected' : '' ?>>Verloren</option>
                        <option value="retired"     <?= ($old['status'] === 'retired')     ? 'selected' : '' ?>>Buiten dienst</option>
                    </select>
                </div>

                <!-- Kaart: Afbeelding upload -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-5">Afbeelding</h2>

                    <!-- Drop-zone stijl upload area -->
                    <label for="image"
                           id="dropZone"
                           class="relative flex flex-col items-center justify-center w-full h-44 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-all duration-200">

                        <!-- Upload icoon en tekst (zichtbaar als er geen preview is) -->
                        <div id="uploadPrompt" class="flex flex-col items-center">
                            <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21zm14.25-12.75a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium">Klik om een afbeelding te kiezen</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG of WEBP (max 5MB)</p>
                        </div>

                        <!-- Preview afbeelding (verborgen tot bestand gekozen is) -->
                        <img id="imagePreview"
                             class="absolute inset-0 w-full h-full object-contain rounded-xl hidden p-2"
                             alt="Preview">

                        <!-- Verborgen file input -->
                        <input type="file"
                               id="image"
                               name="image"
                               accept="image/jpeg,image/png,image/webp"
                               class="sr-only">
                    </label>

                    <!-- Bestandsnaam weergave -->
                    <p id="fileName" class="text-xs text-gray-500 mt-2 truncate"></p>
                </div>

            </div>

        </div>

        <!-- === Actieknoppen === -->
        <div class="flex items-center justify-end gap-3 mt-6">
            <!-- Annuleren: grijze knop, terug naar items overzicht -->
            <a href="<?= ADMIN_BASE_PATH ?>/items"
               class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors duration-200">
                Annuleren
            </a>
            <!-- Opslaan: primaire paarse knop -->
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                Opslaan
            </button>
        </div>

    </form>

</section>

<!-- JavaScript voor afbeelding preview -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        /**
         * Afbeelding preview functionaliteit.
         *
         * Doel:
         * Toont een preview van de geselecteerde afbeelding in de drop-zone.
         * Verbergt de upload-tekst en toont de naam van het bestand.
         */
        const fileInput     = document.getElementById('image');
        const preview       = document.getElementById('imagePreview');
        const uploadPrompt  = document.getElementById('uploadPrompt');
        const fileNameEl    = document.getElementById('fileName');
        const dropZone      = document.getElementById('dropZone');

        fileInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                // Preview tonen via FileReader API
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    uploadPrompt.classList.add('hidden');
                };
                reader.readAsDataURL(file);

                // Bestandsnaam weergeven
                fileNameEl.textContent = file.name;
            } else {
                // Geen bestand: reset naar originele staat
                preview.classList.add('hidden');
                uploadPrompt.classList.remove('hidden');
                fileNameEl.textContent = '';
            }
        });

        /**
         * Drag & drop ondersteuning.
         *
         * Doel:
         * Staat toe dat gebruikers een afbeelding naar de drop-zone slepen.
         * Visuele feedback via border-kleur bij dragover.
         */
        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('border-indigo-400', 'bg-indigo-50/30');
        });

        dropZone.addEventListener('dragleave', function () {
            this.classList.remove('border-indigo-400', 'bg-indigo-50/30');
        });

        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('border-indigo-400', 'bg-indigo-50/30');

            // Bestanden van de drop-event naar het file-input verplaatsen
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                // Change event handmatig triggeren voor de preview
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    });
</script>
