<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="flex min-h-full items-center justify-center bg-slate-950 px-4 text-slate-100">
  <div class="w-full max-w-md">
    <div class="mb-8 flex flex-col items-center text-center">
      <?php
      $logoHref = null;
      $logoSize = 'lg';
      $logoTagline = 'Gestión comercial multitenant';
      require __DIR__ . '/../partials/logo.php';
      ?>
    </div>
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
  </div>
</body>
</html>
