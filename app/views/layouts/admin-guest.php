<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="flex min-h-full items-center justify-center bg-slate-950 px-4 text-slate-100">
  <div class="w-full max-w-md">
    <div class="mb-8 w-full">
      <?php
      $logoHref = null;
      $logoSize = 'lg';
      $logoAlign = 'center';
      $logoTagline = 'Administración de tenants';
      require __DIR__ . '/../partials/logo.php';
      ?>
      <p class="mt-3 text-center text-sm font-medium text-violet-300">Platform</p>
    </div>
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
    <p class="mt-6 text-center text-sm text-slate-500">
      <a href="<?= url('/login') ?>" class="text-violet-400 hover:underline">← Acceso comercios</a>
    </p>
  </div>
</body>
</html>
