<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="flex min-h-full items-center justify-center bg-surface px-4">
  <div class="w-full max-w-md">
    <div class="mb-8">
      <?php
      $logoSize = 'lg';
      $logoLayout = 'stacked';
      $logoAlign = 'center';
      $logoTagline = 'Gestión comercial multitenant';
      require __DIR__ . '/../partials/logo.php';
      ?>
    </div>
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
  </div>
</body>
</html>
