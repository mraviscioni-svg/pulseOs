<?php
$current = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$tenantName = \App\Core\Session::get('tenant_name', '');
$userName = \App\Core\Session::get('user_name', '');

$navIcons = [
    'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
    'products' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
    'categories' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>',
    'stock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M4 7h16M4 7l2-4h12l2 4"/>',
    'pos' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
    'suppliers' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
    'purchases' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
    'cash' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
    'reports' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
    'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
    'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'default' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
];

$renderNavIcon = static function (string $key) use ($navIcons): string {
    $path = $navIcons[$key] ?? $navIcons['default'];

    return '<svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">' . $path . '</svg>';
};
?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <a href="<?= url('/dashboard') ?>" class="flex items-center gap-3 hover:opacity-90" title="PulseOS">
      <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-accent-50 text-accent-600">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3 6 6 .9-4.5 4.2 1.1 6.3L12 16.8 6.4 19.4l1.1-6.3L3 8.9 9 8z"/></svg>
      </span>
      <div class="min-w-0">
        <p class="truncate text-sm font-bold text-navy-900">PulseOS</p>
        <p class="truncate text-xs text-slate-500"><?= e($tenantName) ?></p>
      </div>
    </a>
  </div>
  <nav class="sidebar-nav">
    <?php
    if (can('dashboard.view')):
        $active = str_contains($current, '/dashboard');
    ?>
    <a href="<?= url('/dashboard') ?>" class="nav-link <?= $active ? 'nav-active' : '' ?>">
      <?= $renderNavIcon('dashboard') ?> Dashboard
    </a>
    <?php endif;

    $navModules = config('nav_modules');
    foreach ($navModules as $modKey => $meta):
        if ($modKey === 'categories') {
            if (!module_enabled('categories') && !module_enabled('products')) {
                continue;
            }
        } elseif (!module_enabled($modKey)) {
            continue;
        }
        if (!can($meta['perm'])) {
            continue;
        }
        $href = $meta['href'];
        if ($modKey === 'work_orders') {
            $href = '/work-orders';
        }
        if ($modKey === 'bar') {
            $href = '/bar';
        }
        if ($modKey === 'entries') {
            $href = '/entries';
        }
        if ($modKey === 'memberships') {
            $href = '/memberships';
        }
        $iconKey = in_array($modKey, ['categories', 'variants'], true) ? 'categories' : $modKey;
        $active = str_contains($current, $href);
    ?>
    <a href="<?= url($href) ?>" class="nav-link <?= $active ? 'nav-active' : '' ?>">
      <?= $renderNavIcon($iconKey) ?> <?= e($meta['label']) ?>
    </a>
    <?php endforeach;

    if (can('settings.manage')):
        $active = str_contains($current, '/settings');
    ?>
    <a href="<?= url('/settings') ?>" class="nav-link <?= $active ? 'nav-active' : '' ?>">
      <?= $renderNavIcon('settings') ?> Configuración
    </a>
    <?php endif;

    if (can('users.manage')):
        $active = str_contains($current, '/users');
    ?>
    <a href="<?= url('/users') ?>" class="nav-link <?= $active ? 'nav-active' : '' ?>">
      <?= $renderNavIcon('users') ?> Usuarios
    </a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-footer space-y-3">
    <p class="truncate font-medium text-navy-900"><?= e($userName) ?></p>
    <p class="text-[11px] leading-relaxed">Podés editar datos del comercio y el equipo desde Configuración.</p>
    <form method="post" action="<?= url('/logout') ?>">
      <?= csrf_field() ?>
      <button type="submit" class="btn-secondary w-full !py-2 text-xs">Cerrar sesión</button>
    </form>
  </div>
</aside>
