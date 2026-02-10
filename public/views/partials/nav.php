<?php
declare(strict_types=1);

use Admin\Core\Auth;

?>
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        <a href="/" class="flex items-center gap-2 group">
            <div class="w-8 h-8 bg-white border-2 border-gray-900 rounded flex items-center justify-center text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
            <span class="font-bold text-xl tracking-tight text-gray-900">ToolTrack</span>
        </a>

        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="/catalogus" class="hover:text-gray-900 transition">Catalogus</a>
            <a href="#" class="hover:text-gray-900 transition">Hoe werkt het?</a>
            <a href="#" class="hover:text-gray-900 transition">Contact</a>
        </div>

        <div>
            <?php if (Auth::check()): ?>
                <div class="flex items-center gap-4">

                    <div class="hidden md:flex flex-col items-end leading-tight mr-2">
                        <span class="text-sm font-semibold text-gray-900">
                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Gebruiker') ?>
                        </span>
                        <span class="text-[10px] uppercase tracking-wider font-bold
                            <?= ($_SESSION['user_role'] ?? '') === 'admin' ? 'text-blue-600' : 'text-gray-400' ?>">
                            <?= htmlspecialchars($_SESSION['user_role'] ?? 'Gast') ?>
                        </span>
                    </div>

                    <?php if (Auth::isAdmin()): ?>
                        <a href="/admin" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                    <?php endif; ?>

                    <a href="/logout" class="text-gray-500 hover:text-red-600 transition p-2" title="Uitloggen">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </a>
                </div>
            <?php else: ?>
                <div class="flex gap-3">
                    <a href="/login" class="text-gray-600 hover:text-gray-900 px-4 py-2.5 rounded-lg text-sm font-semibold transition">
                        Inloggen
                    </a>
                    <a href="/register" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        Registreren
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>