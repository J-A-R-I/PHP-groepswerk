<?php
declare(strict_types=1);

$currentUserId = (int)($_SESSION['user_id'] ?? 0);

/**
 * Gebruikers Overzicht View — ToolTrack Admin
 *
 * Doel:
 * Toont een lijst van alle geregistreerde gebruikers.
 * Inclusief avatar (initialen), rol en status management.
 */

// Helper functie voor initialen
function getUserInitials(string $name): string {
    $parts = explode(' ', trim($name));
    $initials = '';
    foreach ($parts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper($part[0]);
        }
    }
    return substr($initials, 0, 2);
}

// Bepaal de badge kleur op basis van de rol
function getRoleBadgeClass(string $role): string {
    return match(strtolower($role)) {
        'admin'   => 'bg-purple-100 text-purple-700',
        'user'    => 'bg-gray-100 text-gray-700',
        default   => 'bg-blue-50 text-blue-700',
    };
}
?>

<section class="p-6 lg:p-8">

    <!-- Paginakop -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gebruikers Beheer</h1>
        <p class="text-sm text-gray-500 mt-1">Overzicht van geregistreerde accounts en rollen.</p>
    </div>

    <!-- Tabel Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <?php if (empty($users)): ?>
            <div class="p-12 text-center">
                <p class="text-gray-500 font-medium">Geen gebruikers gevonden.</p>
            </div>
        <?php else: ?>

            <table class="w-full">
                <!-- Tabelkop -->
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Gebruiker</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Rol</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Status</th>
                        <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Acties</th>
                    </tr>
                </thead>

                <!-- Tabelinhoud -->
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors duration-150">

                            <!-- Kolom 1: Gebruiker (Avatar + Naam + Email) -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <!-- Avatar met initialen -->
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center border border-indigo-200">
                                        <span class="text-sm font-bold text-indigo-700 tracking-tight">
                                            <?= htmlspecialchars(getUserInitials($user['name'])) ?>
                                        </span>
                                    </div>
                                    <!-- Naam en Email -->
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 leading-tight">
                                            <?= htmlspecialchars($user['name']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom 2: Rol -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-medium <?= getRoleBadgeClass($user['role_name']) ?>">
                                    <?= htmlspecialchars(ucfirst($user['role_name'])) ?>
                                </span>
                            </td>

                            <!-- Kolom 3: Status -->
                            <td class="px-6 py-4">
                                <?php if ((int)$user['is_active'] === 1): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        Actief
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                        Geblokkeerd
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Kolom 4: Acties -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    
                                    <!-- Bewerk knop -->
                                    <a href="/admin/users/<?= (int)$user['id'] ?>/edit" 
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm"
                                       title="Gebruiker bewerken">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                        Bewerk
                                    </a>

                                    <!-- Blokkeer / Deblokkeer knop -->
                                    <?php if ((int)$user['id'] === $currentUserId): ?>
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-300 cursor-not-allowed select-none"
                                              title="Je kunt jezelf niet blokkeren">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Blokkeer
                                        </span>
                                    <?php elseif ((int)$user['is_active'] === 1): ?>
                                        <form method="POST" action="/admin/users/<?= (int)$user['id'] ?>/disable" 
                                              onsubmit="return confirm('Weet je zeker dat je <?= htmlspecialchars($user['name']) ?> wilt blokkeren?');">
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 hover:border-red-300 transition-colors shadow-sm"
                                                    title="Account blokkeren">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Blokkeer
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/admin/users/<?= (int)$user['id'] ?>/enable"
                                              onsubmit="return confirm('Weet je zeker dat je <?= htmlspecialchars($user['name']) ?> wilt deblokkeren?');">
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 hover:border-green-300 transition-colors shadow-sm"
                                                    title="Account opnieuw activeren">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Deblokkeer
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Footer teller -->
            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    <?= count($users) ?> geregistreerde gebruiker<?= count($users) !== 1 ? 's' : '' ?>
                </p>
            </div>

        <?php endif; ?>

    </div>
</section>
