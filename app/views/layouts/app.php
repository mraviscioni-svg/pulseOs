<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="min-h-full bg-slate-950 text-slate-100">
<?php require __DIR__ . '/../partials/sidebar.php'; ?>
<div class="pl-64">
  <?php if (is_platform_impersonating()): ?>
  <div class="impersonation-banner">
    <span>Estás viendo el panel como administrador de plataforma.</span>
    <form method="post" action="<?= url('/admin/stop-impersonate') ?>" class="inline">
      <?= csrf_field() ?>
      <button type="submit" class="btn-secondary !py-1.5 !text-xs">← Volver al admin</button>
    </form>
  </div>
  <?php endif; ?>
  <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-slate-800/80 bg-slate-950/90 px-6 backdrop-blur-md">
    <h1 class="truncate text-base font-semibold text-slate-200"><?= e($title ?? '') ?></h1>
    <div class="flex items-center gap-4 text-sm">
      <span class="text-slate-400"><?= e(\App\Core\Session::get('user_name', '')) ?></span>
      <form method="post" action="<?= url('/logout') ?>">
        <?= csrf_field() ?>
        <button class="rounded-lg border border-slate-700 px-3 py-1.5 hover:bg-slate-900">Salir</button>
      </form>
    </div>
  </header>
  <main class="p-6">
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
  </main>
</div>
</body>
</html>
