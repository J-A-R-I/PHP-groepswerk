<?php
declare(strict_types=1);

$title = 'Registreren - ToolTrack';
ob_start();
?>

    <div class="flex items-center justify-center min-h-[70vh]">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-900"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    <span class="text-xl font-bold text-gray-900">ToolTrack</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Account aanmaken</h1>
                <p class="text-gray-500 mt-2">Registreer om materiaal te kunnen lenen.</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="mb-4 p-4 border border-red-200 bg-red-50 text-red-700 rounded-lg text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/register" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="name">Volledige naam</label>
                    <input
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                        type="text"
                        id="name"
                        name="name"
                        required
                        placeholder="Jan Janssens"
                        autocomplete="name">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="email">E-mailadres</label>
                    <input
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                        type="email"
                        id="email"
                        name="email"
                        required
                        placeholder="naam@voorbeeld.be"
                        autocomplete="email">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="password">Wachtwoord</label>
                    <input
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        autocomplete="new-password">
                </div>

                <button class="w-full bg-blue-900 text-white font-semibold py-3 rounded-lg hover:bg-blue-800 transition shadow-sm" type="submit">
                    Registreren
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                Heb je al een account? <a href="/login" class="text-blue-600 font-semibold hover:underline">Log hier in</a>.
            </div>
        </div>
    </div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/public.php';