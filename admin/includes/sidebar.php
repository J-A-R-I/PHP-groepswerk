<?php
declare(strict_types=1);

/**
 * Sidebar — ToolTrack Admin
 *
 * Doel:
 * Toont de navigatiebalk aan de linkerkant van het admin-paneel.
 * Bevat het ToolTrack-logo, gebruikersprofiel, navigatielinks en uitlog-knop.
 *
 * Actieve link:
 * De huidige pagina wordt visueel gemarkeerd met een paarse/indigo achtergrond.
 */

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$currentPath = rtrim($currentPath, '/') ?: '/';

/**
 * isActiveAdmin()
 *
 * Doel:
 * Bepaalt of een navigatie-item actief is op basis van het huidige pad.
 */
if (!function_exists('isActiveAdmin')) {
    function isActiveAdmin(string $currentPath, string $targetPath): bool
    {
        if ($currentPath === $targetPath) {
            return true;
        }
        if ($targetPath !== ADMIN_BASE_PATH && str_starts_with($currentPath, $targetPath . '/')) {
            return true;
        }
        return false;
    }
}

/**
 * sidebarLinkClass()
 *
 * Doel:
 * Geeft de juiste TailwindCSS-klassen terug voor een sidebar-link.
 * Actieve link: paarse/indigo achtergrond met wit.
 * Inactieve link: grijze tekst met hover-effect.
 */
if (!function_exists('sidebarLinkClass')) {
    function sidebarLinkClass(bool $active): string
    {
        return $active
            ? 'flex items-center gap-3 rounded-lg px-3 py-2.5 bg-indigo-600 text-white font-semibold text-sm transition-colors'
            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 text-sm transition-colors';
    }
}

// Gebruikersnaam ophalen uit de sessie (ingesteld bij login)
$sidebarUserName = $_SESSION['user_name'] ?? 'Admin';
?>

<!-- Sidebar container -->
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col min-h-screen">

    <!-- Bovenste deel: Logo + User profiel -->
    <div class="p-5">

        <!-- ToolTrack logo + instellingen icoon -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <!-- Moersleutel icoon -->
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 01-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l.708.708a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3"/>
                </svg>
                <span class="text-lg font-bold text-gray-900">ToolTrack</span>
            </div>
            <!-- Instellingen tandwiel icoon -->
            <button class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.431.992a7.723 7.723 0 010 .255c-.007.378.138.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>

        <!-- Gebruikersprofiel -->
        <div class="flex items-center gap-3 mb-8">
            <!-- Avatar cirkel met initiaal -->
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">Hoofd</p>
                <p class="text-xs text-gray-500 truncate">Admin</p>
            </div>
            <!-- Dropdown chevron -->
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </div>
    </div>

    <!-- Navigatielinks -->
    <nav class="flex-1 px-4 space-y-1">

        <!-- Dashboard -->
        <a href="<?= ADMIN_BASE_PATH ?>"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH)) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
            </svg>
            Dashboard
        </a>

        <!-- Items -->
        <a href="<?= ADMIN_BASE_PATH ?>/items"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/items')) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
            </svg>
            Items
        </a>

        <!-- Categorieën -->
        <a href="<?= ADMIN_BASE_PATH ?>/categories"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/categories')) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
            </svg>
            Categorieën
        </a>

        <!-- Reserveringen -->
        <a href="<?= ADMIN_BASE_PATH ?>/reservations"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/reservations')) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            Reserveringen
        </a>

        <!-- Gebruikers -->
        <a href="<?= ADMIN_BASE_PATH ?>/users"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/users')) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
            Gebruikers
        </a>

    </nav>

    <!-- Uitloggen link onderaan de sidebar -->
    <div class="p-4 mt-auto">
        <form action="<?= ADMIN_BASE_PATH ?>/logout" method="POST">
            <button type="submit"
                    class="flex items-center gap-3 w-full rounded-lg px-3 py-2.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 text-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                </svg>
                Uitloggen
            </button>
        </form>
    </div>

</aside>