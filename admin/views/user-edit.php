<?php
declare(strict_types=1);

/**
 * Gebruiker Bewerken View — ToolTrack Admin
 *
 * Doel:
 * Toont het formulier om een gebruiker te bewerken.
 * Bevat velden voor naam, email, rol en optionele wachtwoord-reset.
 */

// Variabelen initialiseren
$user   = $user ?? [];
$roles  = $roles ?? [];
$errors = $errors ?? [];
$old    = $old ?? [];
?>

<section class="p-6 lg:p-8">

    <!-- Paginakop -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gebruiker Bewerken</h1>
        <p class="text-sm text-gray-500 mt-1">
            Pas de gegevens aan van <span class="font-medium text-gray-700"><?= htmlspecialchars($user['name'] ?? '') ?></span>
        </p>
    </div>

    <!-- Foutmeldingen tonen -->
    <?php if (!empty($errors)): ?>
        <div class="mb-6 rounded-xl bg-red-50 p-4 border border-red-100">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Er zijn fouten opgetreden:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc pl-5 space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Formulier -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="/admin/users/<?= (int)$user['id'] ?>/update" method="POST" class="p-6 lg:p-8 space-y-6">

            <!-- Naam -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Naam <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?= htmlspecialchars((string)($old['name'] ?? '')) ?>" 
                       required
                       class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= htmlspecialchars((string)($old['email'] ?? '')) ?>" 
                       required
                       class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200">
            </div>

            <!-- Rol -->
            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Rol <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select id="role_id" 
                            name="role_id" 
                            required
                            class="block w-full appearance-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= (int)$role['id'] ?>" 
                                <?= ((string)($old['role_id'] ?? '') === (string)$role['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 my-6">

            <!-- Wachtwoord (Optioneel) -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nieuw Wachtwoord
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="••••••••"
                       class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all duration-200">
                <p class="mt-2 text-xs text-gray-500">
                    <span class="text-amber-600 font-medium">💡 Tip:</span> 
                    Laat dit veld leeg om het huidige wachtwoord te behouden.
                </p>
            </div>

            <!-- Actieknoppen -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="/admin/users" 
                   class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors duration-200">
                    Annuleren
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Opslaan
                </button>
            </div>

        </form>
    </div>

</section>
