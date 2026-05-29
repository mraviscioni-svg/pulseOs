<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="min-h-full">
<aside class="sidebar !w-56">
  <div class="sidebar-brand">
    <?php
    $logoSize = 'sm';
    $logoLayout = 'inline';
    $logoAlign = 'left';
    $logoHref = '/admin/tenants';
    require __DIR__ . '/../partials/logo.php';
    ?>
    <p class="mt-2.5 text-xs font-medium text-slate-500">Platform</p>
  </div>
  <nav class="sidebar-nav">
    <?php $current = $_SERVER['REQUEST_URI'] ?? ''; ?>
    <a href="<?= url('/admin/tenants') ?>" class="nav-link <?= str_contains($current, '/admin/tenants') && !str_contains($current, '/create') ? 'nav-active' : '' ?>">Comercios</a>
    <a href="<?= url('/admin/tenants/create') ?>" class="nav-link <?= str_contains($current, '/create') ? 'nav-active' : '' ?>">+ Nuevo comercio</a>
  </nav>
  <div class="sidebar-footer space-y-3">
    <p class="truncate font-medium text-navy-900"><?= e(\App\Core\Session::get('platform_admin_name', '')) ?></p>
    <?php if (has_tenant_session()): ?>
    <a href="<?= url('/dashboard') ?>" class="btn-secondary w-full !py-2 text-xs text-center" target="_blank" rel="noopener">Panel del comercio ↗</a>
    <?php endif; ?>
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
