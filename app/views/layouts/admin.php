<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="min-h-full">
<aside class="sidebar !w-56">
  <div class="sidebar-brand">
    <a href="<?= url('/admin/tenants') ?>" class="block hover:opacity-90" title="PulseOS Platform">
      <img src="<?= asset('images/logo.svg') ?>" alt="PulseOS" class="h-9 w-auto max-w-full object-contain object-left" width="212" height="52">
    </a>
    <p class="mt-2 text-xs text-slate-500">Platform</p>
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
