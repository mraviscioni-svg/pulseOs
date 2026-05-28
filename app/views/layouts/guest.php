<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="flex min-h-full items-center justify-center bg-slate-950 px-4 text-slate-100">
  <div class="w-full max-w-md">
    <div class="mb-8 text-center">
      <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-pulse-600 text-xl font-bold">P</div>
      <h1 class="text-2xl font-bold">pulseOS</h1>
      <p class="text-sm text-slate-400">Gestión comercial multitenant</p>
    </div>
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
  </div>
</body>
</html>
