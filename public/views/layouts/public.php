<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Layout: public
|--------------------------------------------------------------------------
*/
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'ToolTrack') ?></title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">

<?php require __DIR__ . '/../partials/nav.php'; ?>

<main class="flex-1 w-full max-w-7xl mx-auto px-6 py-10">
    <?= $content ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>