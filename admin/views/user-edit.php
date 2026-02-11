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

<section class="p-6">
    <div class="bg-white p-6 rounded shadow max-w-2xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Gebruiker bewerken</h2>
            <a class="underline" href="<?= ADMIN_BASE_PATH ?>/users">Terug naar overzicht</a>
        </div>

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

        <form method="post" action="<?= ADMIN_BASE_PATH ?>/users/<?php echo (int)$user['id']; ?>/update" class="space-y-4">
            <div>
                <label class="block text-sm font-bold mb-1" for="email">Email (readonly)</label>
                <input class="w-full border rounded p-2 bg-gray-100" type="email" id="email"
                       value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" disabled>
            </div>

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

            <div class="flex gap-4">
                <button class="border rounded px-4 py-2" type="submit">Opslaan</button>
                <a class="underline" href="<?= ADMIN_BASE_PATH ?>/users">Annuleren</a>
            </div>

            <hr class="border-gray-100 my-6">

        <?php if (!empty($pw_errors)): ?>
            <div class="mb-4 p-4 border border-red-200 bg-red-50 rounded">
                <p class="font-bold mb-2">Controleer je invoer:</p>
                <ul class="list-disc pl-6">
                    <?php foreach ($pw_errors as $error): ?>
                        <li><?php echo htmlspecialchars((string)$error, ENT_QUOTES); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= ADMIN_BASE_PATH ?>/users/<?php echo (int)$user['id']; ?>/reset-password" class="space-y-4">
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

        <hr class="my-8">

        <h3 class="text-lg font-bold mb-3">Status beheren</h3>

        <?php if ($isActive): ?>
            <form method="post" action="<?= ADMIN_BASE_PATH ?>/users/<?php echo (int)$user['id']; ?>/disable">
                <button class="underline text-red-600" type="submit">Blokkeer gebruiker</button>
            </form>
        <?php else: ?>
            <form method="post" action="<?= ADMIN_BASE_PATH ?>/users/<?php echo (int)$user['id']; ?>/enable">
                <button class="underline text-green-700" type="submit">Deblokkeer gebruiker</button>
            </form>
        <?php endif; ?>
    </div>

</section>
