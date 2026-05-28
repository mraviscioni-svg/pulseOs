<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="min-h-full">
<aside class="sidebar !w-56">
  <div class="sidebar-brand">
    <a href="<?= url('/admin/tenants') ?>" class="flex items-center gap-3 hover:opacity-90" title="PulseOS Platform">
      <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-accent-50 text-accent-600">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3 6 6 .9-4.5 4.2 1.1 6.3L12 16.8 6.4 19.4l1.1-6.3L3 8.9 9 8z"/></svg>
      </span>
      <div>
        <p class="text-sm font-bold text-navy-900">PulseOS</p>
        <p class="text-xs text-slate-500">Platform</p>
      </div>
    </a>
  </div>
  <nav class="sidebar-nav">
    <?php $current = $_SERVER['REQUEST_URI'] ?? ''; ?>
    <a href="<?= url('/admin/tenants') ?>" class="nav-link <?= str_contains($current, '/admin/tenants') && !str_contains($current, '/create') ? 'nav-active' : '' ?>">Comercios</a>
    <a href="<?= url('/admin/tenants/create') ?>" class="nav-link <?= str_contains($current, '/create') ? 'nav-active' : '' ?>">+ Nuevo comercio</a>
  </nav>
  <div class="sidebar-footer space-y-3">
    <p class="truncate font-medium text-navy-900"><?= e(\App\Core\Session::get('platform_admin_name', '')) ?></p>
    <form method="post" action="<?= url('/admin/logout') ?>">
      <?= csrf_field() ?>
      <button type="submit" class="btn-secondary w-full !py-2 text-xs">Salir</button>
    </form>
  </div>
</aside>
<div class="min-h-screen pl-56">
  <main class="p-8">
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
  </main>
</div>
</body>
</html>
