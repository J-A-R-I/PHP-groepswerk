
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Audit Logs & History</h1>
    </div>

    <?php 
    $success = \Admin\Core\Flash::get('success');
    $warning = \Admin\Core\Flash::get('warning');
    ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($warning): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ((array)$warning as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-semibold">
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Admin</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Entity</th>
                        <th class="px-6 py-4">Details</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No activity logs found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    <?= date('d-m-Y H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?= htmlspecialchars($log['admin_name'] ?? 'System') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $actionColors = [
                                        'create' => 'bg-green-100 text-green-800',
                                        'update' => 'bg-blue-100 text-blue-800',
                                        'delete' => 'bg-red-100 text-red-800',
                                    ];
                                    $colorClass = $actionColors[$log['action']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 rounded text-xs font-bold uppercase <?= $colorClass ?>">
                                        <?= htmlspecialchars($log['action']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-400 uppercase tracking-wider"><?= htmlspecialchars($log['entity_type']) ?></div>
                                    <div class="font-mono text-xs text-gray-600">ID: <?= $log['entity_id'] ?></div>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <?php if ($log['action'] === 'update'): ?>
                                        <div class="text-gray-500 italic mb-1">Field changes detected</div>
                                        <?php 
                                            $old = json_decode((string)$log['old_data'], true);
                                            $new = json_decode((string)$log['new_data'], true);
                                            foreach ((array)$new as $key => $val):
                                                if (isset($old[$key]) && $old[$key] != $val):
                                        ?>
                                            <div class="mb-1">
                                                <strong><?= htmlspecialchars($key) ?>:</strong> 
                                                <span class="text-red-500 line-through"><?= htmlspecialchars((string)$old[$key]) ?></span> &rarr; 
                                                <span class="text-green-600"><?= htmlspecialchars((string)$val) ?></span>
                                            </div>
                                        <?php 
                                                endif;
                                            endforeach;
                                        ?>
                                    <?php elseif ($log['action'] === 'create'): ?>
                                        <span class="text-gray-500">New record created.</span>
                                    <?php elseif ($log['action'] === 'delete'): ?>
                                        <span class="text-red-400">Record deleted. Data available for restore.</span>
                                    <?php endif; ?>

                                    <?php if (!empty($log['reverted_at'])): ?>
                                        <div class="mt-2 text-xs text-gray-400 border-t border-gray-100 pt-1">
                                            <span class="inline-flex items-center gap-1 text-orange-600 font-medium">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                Reverted
                                            </span>
                                            by <?= htmlspecialchars($log['reverted_by_name'] ?? 'Unknown') ?>
                                            on <?= date('d/m/Y H:i', strtotime($log['reverted_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if (empty($log['reverted_at'])): ?>
                                        <form action="<?= ADMIN_BASE_PATH ?>/activity-logs/<?= $log['id'] ?>/revert" method="POST" onsubmit="return confirm('Are you sure you want to revert this change?');">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium text-xs border border-blue-200 bg-blue-50 px-3 py-1 rounded hover:bg-blue-100 transition">
                                                Revert
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs italic">Already reverted</span>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

