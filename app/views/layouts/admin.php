<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="min-h-full bg-slate-950 text-slate-100">
<aside class="fixed inset-y-0 left-0 z-40 w-56 border-r border-violet-900/40 bg-slate-950">
  <div class="flex h-16 items-center gap-2 border-b border-slate-800 px-4">
    <a href="<?= url('/admin/tenants') ?>" class="shrink-0 hover:opacity-90">
      <img src="<?= asset('images/logo-icon.svg') ?>" alt="PulseOS" class="h-9 w-9">
    </a>
    <div class="min-w-0">
      <p class="text-xs font-semibold text-white">Platform</p>
      <p class="text-xs text-violet-300">PulseOS</p>
    </div>
  </div>
  <nav class="p-3 text-sm">
    <a href="<?= url('/admin/tenants') ?>" class="nav-link nav-active">Tenants</a>
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
