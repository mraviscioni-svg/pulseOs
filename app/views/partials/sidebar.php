<aside class="fixed inset-y-0 left-0 z-40 w-64 border-r border-slate-800 bg-slate-950/95 backdrop-blur">
  <div class="flex h-16 items-center gap-2 border-b border-slate-800 px-4">
    <a href="<?= url('/dashboard') ?>" class="shrink-0 hover:opacity-90">
      <img src="<?= asset('images/logo-icon.svg') ?>" alt="PulseOS" class="h-9 w-9">
    </a>
    <div class="min-w-0 flex-1">
      <p class="truncate text-xs font-semibold text-white">PulseOS</p>
      <p class="truncate text-xs text-slate-400"><?= e(\App\Core\Session::get('tenant_name', '')) ?></p>
    </div>
  </div>
  <nav class="max-h-[calc(100vh-4rem)] space-y-1 overflow-y-auto p-3 text-sm">
    <?php
    $always = [
        ['/dashboard', 'Dashboard', 'dashboard.view', null],
    ];
    $navModules = config('nav_modules');
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';

    foreach ($always as [$href, $label, $perm, $mod]):
        if (!can($perm)) continue;
        $active = str_contains($current, $href);
    ?>
    <a href="<?= url($href) ?>" class="nav-link <?= $active ? 'nav-active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach;

    foreach ($navModules as $modKey => $meta):
        if ($modKey === 'variants') {
            if (!module_enabled('variants') && !module_enabled('ropa')) continue;
        } elseif (!module_enabled($modKey)) {
            continue;
        }
        if (!can($meta['perm'])) continue;
        $href = $meta['href'];
        if ($modKey === 'work_orders') $href = '/work-orders';
        if ($modKey === 'bar') $href = '/bar';
        if ($modKey === 'entries') $href = '/entries';
        if ($modKey === 'memberships') $href = '/memberships';
        $active = str_contains($current, $href);
    ?>
    <a href="<?= url($href) ?>" class="nav-link <?= $active ? 'nav-active' : '' ?>"><?= e($meta['label']) ?></a>
    <?php endforeach;

    if (can('settings.manage')):
        $active = str_contains($current, '/settings');
    ?>
    <a href="<?= url('/settings') ?>" class="nav-link <?= $active ? 'nav-active' : '' ?>">Configuración</a>
    <?php endif; ?>

    <?php if (can('users.manage')): ?>
    <a href="<?= url('/users') ?>" class="nav-link <?= str_contains($current, '/users') ? 'nav-active' : '' ?>">Usuarios</a>
    <?php endif; ?>
  </nav>
</aside>
