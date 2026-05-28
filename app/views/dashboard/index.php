<div class="page-header">
  <div class="page-header-main">
    <p class="page-eyebrow">Resumen</p>
    <h2>Dashboard</h2>
    <p>Vista rápida de tu comercio — datos filtrados por sesión en el servidor.</p>
  </div>
  <?php if (module_enabled('pos') && can('pos.sell')): ?>
  <a href="<?= url('/pos') ?>" class="btn-primary shrink-0">Nueva venta</a>
  <?php endif; ?>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <div class="stat-card">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="stat-card-label">Ventas del día</p>
        <p class="stat-card-value"><?= money($stats['sales_today_total']) ?></p>
        <p class="stat-card-sub"><?= (int) $stats['sales_today_count'] ?> tickets</p>
      </div>
      <div class="stat-card-icon">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="stat-card-label">Stock bajo</p>
        <p class="stat-card-value"><?= (int) $stats['low_stock_count'] ?></p>
        <p class="stat-card-sub"><a href="<?= url('/reports?type=low_stock') ?>" class="link-accent">Ver reporte</a></p>
      </div>
      <div class="stat-card-icon">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="stat-card-label">Compras pendientes</p>
        <p class="stat-card-value"><?= (int) $stats['pending_purchases'] ?></p>
        <p class="stat-card-sub">Órdenes por recibir</p>
      </div>
      <div class="stat-card-icon">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="stat-card-label">Caja</p>
        <?php if ($stats['open_cash']): ?>
        <p class="stat-card-value text-emerald-700">Operativa</p>
        <p class="stat-card-sub">Desde <?= e($stats['open_cash']['opened_at']) ?></p>
        <?php else: ?>
        <p class="stat-card-value text-rose-600">Cerrada</p>
        <p class="stat-card-sub"><a href="<?= url('/cash') ?>" class="link-accent">Abrir caja</a></p>
        <?php endif; ?>
      </div>
      <div class="stat-card-icon">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($stats['chart_labels'])): ?>
<div class="card mt-6">
  <h3 class="mb-4 text-sm font-semibold text-navy-900">Ventas últimos 7 días</h3>
  <canvas id="dashChart" height="90"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('dashChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($stats['chart_labels']) ?>,
    datasets: [{ label: 'Total $', data: <?= json_encode($stats['chart_values']) ?>, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.35 }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { grid: { color: '#eef1f6' } }, x: { grid: { display: false } } } }
});
</script>
<?php endif; ?>

<div class="mt-6 grid gap-6 lg:grid-cols-3">
  <div class="card lg:col-span-2">
    <div class="mb-4 flex items-center justify-between">
      <h3 class="text-sm font-semibold text-navy-900">Últimas ventas</h3>
      <?php if (module_enabled('reports') && can('reports.view')): ?>
      <a href="<?= url('/reports') ?>" class="link-accent text-sm">Ir a ventas</a>
      <?php endif; ?>
    </div>
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead><tr><th>Nº</th><th>Total</th><th>Pago</th><th>Fecha</th></tr></thead>
        <tbody>
        <?php foreach ($stats['recent_sales'] as $sale): ?>
        <tr>
          <td class="font-medium"><?= e($sale['sale_number']) ?></td>
          <td><?= money($sale['total']) ?></td>
          <td class="capitalize"><?= e(str_replace('_', ' ', $sale['payment_method'])) ?></td>
          <td class="text-slate-500"><?= e($sale['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$stats['recent_sales']): ?><tr><td colspan="4" class="py-8 text-center text-slate-500">Sin ventas recientes</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="space-y-6">
    <div class="card">
      <h3 class="mb-4 text-sm font-semibold text-navy-900">Más vendidos (30 días)</h3>
      <ul class="space-y-2 text-sm">
        <?php foreach ($stats['top_products'] as $p): ?>
        <li class="flex justify-between border-b border-slate-100 py-2 last:border-0">
          <span class="text-navy-900"><?= e($p['product_name']) ?></span>
          <span class="text-slate-500"><?= e($p['qty']) ?> u.</span>
        </li>
        <?php endforeach; ?>
        <?php if (!$stats['top_products']): ?><li class="text-slate-500">Sin datos aún</li><?php endif; ?>
      </ul>
    </div>

    <div class="promo-card">
      <p class="text-xs font-semibold uppercase tracking-wider text-white/70">PulseOS</p>
      <p class="mt-2 text-sm leading-relaxed text-white/90">Vista filtrada por tu comercio y rol. Los totales se calculan en el servidor con los permisos de tu sesión.</p>
      <div class="mt-4 flex flex-wrap gap-2">
        <span class="promo-pill">Rol: <?= e(strtoupper(\App\Core\Session::get('role_slug', 'user'))) ?></span>
        <span class="promo-pill">PulseOS</span>
      </div>
    </div>
  </div>
</div>
