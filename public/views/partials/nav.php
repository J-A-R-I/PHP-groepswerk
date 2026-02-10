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
                    <span class="hidden md:inline text-sm text-gray-500">
                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Gebruiker') ?>
                    </span>

                    <?php if (Auth::isAdmin()): ?>
                        <a href="/admin" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                            Admin Panel
                        </a>
                    <?php endif; ?>

                    <a href="/logout" class="bg-gray-100 hover:bg-gray-200 text-gray-900 px-5 py-2.5 rounded-lg text-sm font-semibold transition border border-gray-200">
                        Uitloggen
                    </a>
                </div>
            <?php else: ?>
                <a href="/login" class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                    Inloggen
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>