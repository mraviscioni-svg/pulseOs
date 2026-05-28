<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="flex min-h-full items-center justify-center bg-slate-950 px-4 text-slate-100">
  <div class="w-full max-w-md">
    <div class="mb-8 text-center">
      <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-violet-600 text-xl font-bold">⚙</div>
      <h1 class="text-2xl font-bold">PulseOS Platform</h1>
      <p class="text-sm text-slate-400">Administración de tenants</p>
    </div>
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
    <p class="mt-6 text-center text-sm text-slate-500">
      <a href="<?= url('/login') ?>" class="text-violet-400 hover:underline">← Acceso comercios</a>
    </p>
  </div>
</body>
</html>
