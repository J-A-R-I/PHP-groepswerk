<?php
declare(strict_types=1);

/**
 * Sidebar navigatie voor het ToolTrack admin dashboard.
 *
 * Doel:
 * - Toont het ToolTrack logo en instellingen-icoon bovenaan.
 * - Toont de ingelogde gebruiker (of "Hoofd Admin" als fallback).
 * - Navigatielinks met SVG-iconen en actieve-status styling.
 * - Licht thema met zachte rand, passend bij het screenshot.
 */

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$currentPath = rtrim($currentPath, '/') ?: '/';

/**
 * isActiveAdmin()
 *
 * Doel:
 * Controleert of de huidige URL overeenkomt met een navigatielink.
 * Wordt gebruikt om de actieve stijl toe te passen.
 */
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

/**
 * sidebarLinkClass()
 *
 * Doel:
 * Geeft de juiste Tailwind-classes terug voor een actieve of inactieve link.
 * Actief: blauw/indigo achtergrond met wit icoon/tekst.
 * Inactief: grijze tekst met hover-effect.
 */
function sidebarLinkClass(bool $active): string
{
    return $active
        ? 'flex items-center gap-3 rounded-lg px-3 py-2.5 bg-indigo-600 text-white font-semibold text-sm transition-all duration-200'
        : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 text-sm transition-all duration-200';
}

// Naam van de ingelogde gebruiker ophalen, of fallback naar "Admin"
$userName = $_SESSION['user_name'] ?? 'Admin';
?>

<!-- Sidebar container: wit, vaste breedte, zachte schaduw -->
<aside class="w-60 bg-white border-r border-gray-200 flex flex-col min-h-screen">

    <!-- Logo + instellingen bovenaan -->
    <div class="px-5 pt-5 pb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <!-- Gekruiste moersleutels icoon (ToolTrack logo) -->
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548
                         2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486l-3.276
                         3.276a3.004 3.004 0 002.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.867 19.125h.008v.008h-.008v-.008z"/>
            </svg>
            <span class="text-lg font-bold text-gray-900">ToolTrack</span>
        </div>
        <!-- Instellingen tandwiel -->
        <button class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
    </div>

    <!-- Gebruikersprofiel sectie -->
    <div class="px-5 py-3 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <!-- Avatar icoon -->
            <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">Hoofd</p>
                <p class="text-xs text-gray-500">Admin</p>
            </div>
            <!-- Chevron dropdown indicator -->
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </div>
    </div>

    <!-- Navigatielinks -->
    <nav class="flex-1 px-3 py-4 space-y-1">

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
                      d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H2.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
            Items
        </a>

        <!-- Categorieën -->
        <a href="<?= ADMIN_BASE_PATH ?>/categories"
           class="<?= sidebarLinkClass(isActiveAdmin($currentPath, ADMIN_BASE_PATH . '/categories')) ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
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

    <!-- Logout knop onderaan -->
    <div class="px-3 pb-4">
        <form method="POST" action="<?= ADMIN_BASE_PATH ?>/logout">
            <button type="submit"
                    class="flex items-center gap-3 w-full rounded-lg px-3 py-2.5 text-gray-500 hover:bg-red-50 hover:text-red-600 text-sm transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                </svg>
                Uitloggen
            </button>
        </form>
    </div>

</aside>