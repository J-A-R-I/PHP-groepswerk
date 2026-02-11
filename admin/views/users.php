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

<section class="p-6">
    <div class="bg-white p-6 rounded shadow">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold"><?= $title; ?></h2>

            <?php if (Auth::isAdmin()): ?>
                <a class="underline" href="<?= ADMIN_BASE_PATH ?>/users/create">
                    + Nieuwe gebruiker
                </a>
            <?php endif; ?>
        </div>

        <table class="w-full text-sm">
            <thead>
            <tr class="text-left border-b">
                <th class="py-2">Email</th>
                <th>Naam</th>
                <th>Rol</th>
                <th>Status</th>
                <th class="text-right">Acties</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="border-b">
                    <td class="py-2"><?php echo htmlspecialchars((string)$user['email'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['name'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['role_name'], ENT_QUOTES); ?></td>
                    <td><?php echo ((int)$user['is_active'] === 1) ? 'Actief' : 'Geblokkeerd'; ?></td>

                    <td class="text-right">
                        <a class="underline mr-4" href="<?= ADMIN_BASE_PATH ?>/users/<?php echo (int)$user['id']; ?>/edit">Bewerk</a>

                        <?php if ((int)$user['is_active'] === 1): ?>
                            <form class="inline" method="post" action="<?= ADMIN_BASE_PATH ?>/users/<?php echo (int)$user['id']; ?>/disable">
                                <button class="underline text-red-600" type="submit">Blokkeer</button>
                            </form>
                        <?php else: ?>
                            <form class="inline" method="post" action="<?= ADMIN_BASE_PATH ?>/users/<?php echo (int)$user['id']; ?>/enable">
                                <button class="underline text-green-700" type="submit">Deblokkeer</button>
                            </form>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
