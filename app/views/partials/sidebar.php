<aside class="fixed inset-y-0 left-0 z-40 w-64 border-r border-slate-800 bg-slate-950/95 backdrop-blur">
  <div class="flex h-16 items-center gap-2 border-b border-slate-800 px-5">
    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-pulse-600 font-bold text-white">P</span>
    <div>
      <p class="font-semibold text-white">pulseOS</p>
      <p class="truncate text-xs text-slate-400"><?= e(\App\Core\Session::get('tenant_name', '')) ?></p>
    </div>
  </div>
  <nav class="space-y-1 p-3 text-sm">
    <?php
    $links = [
        ['/dashboard', 'Dashboard', 'dashboard.view'],
        ['/pos', 'Punto de venta', 'pos.sell'],
        ['/products', 'Productos', 'products.manage'],
        ['/suppliers', 'Proveedores', 'suppliers.manage'],
        ['/purchases', 'Compras', 'purchases.manage'],
        ['/cash', 'Caja', 'cash.manage'],
        ['/users', 'Usuarios', 'users.manage'],
    ];
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    foreach ($links as [$href, $label, $perm]):
        if (!can($perm)) continue;
        $active = str_contains($current, $href);
    ?>
    <a href="<?= url($href) ?>" class="flex items-center rounded-lg px-3 py-2.5 <?= $active ? 'bg-pulse-600/20 text-pulse-400' : 'text-slate-400 hover:bg-slate-900 hover:text-white' ?>">
      <?= e($label) ?>
    </a>
    <?php endforeach; ?>
  </nav>
</aside>
