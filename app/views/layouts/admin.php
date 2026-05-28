<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="min-h-full bg-slate-950 text-slate-100">
<aside class="fixed inset-y-0 left-0 z-40 w-56 border-r border-violet-900/40 bg-slate-950">
  <div class="border-b border-slate-800 px-4 py-3">
    <a href="<?= url('/admin/tenants') ?>" class="block hover:opacity-90" title="PulseOS Platform">
      <img src="<?= asset('images/logo.svg') ?>" alt="PulseOS" class="h-8 w-auto max-w-full object-contain object-left" width="212" height="52">
    </a>
    <p class="mt-2 text-xs text-violet-300">Platform</p>
  </div>
  <nav class="space-y-1 p-3 text-sm">
    <a href="<?= url('/admin/tenants') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/tenants') && !str_contains($_SERVER['REQUEST_URI'] ?? '', '/create') ? 'nav-active' : '' ?>">Comercios</a>
    <a href="<?= url('/admin/tenants/create') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/create') ? 'nav-active' : '' ?>">+ Nuevo comercio</a>
  </nav>
</aside>
<div class="pl-56">
  <header class="flex h-16 items-center justify-between border-b border-slate-800 px-6">
    <h1 class="text-lg font-semibold"><?= e($title ?? '') ?></h1>
    <div class="flex items-center gap-4 text-sm">
      <span class="text-slate-400"><?= e(\App\Core\Session::get('platform_admin_name', '')) ?></span>
      <form method="post" action="<?= url('/admin/logout') ?>"><?= csrf_field() ?>
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
