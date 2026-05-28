<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="flex min-h-full items-center justify-center bg-surface px-4">
  <div class="w-full max-w-md">
    <div class="mb-8 text-center">
      <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-50 text-accent-600">
        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3 6 6 .9-4.5 4.2 1.1 6.3L12 16.8 6.4 19.4l1.1-6.3L3 8.9 9 8z"/></svg>
      </span>
      <h1 class="text-xl font-bold text-navy-900">PulseOS Platform</h1>
      <p class="mt-1 text-sm text-slate-500">Administración de tenants</p>
    </div>
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
    <p class="mt-6 text-center text-sm text-slate-500">
      <a href="<?= url('/login') ?>" class="link-accent">← Acceso comercios</a>
    </p>
  </div>
</body>
</html>
