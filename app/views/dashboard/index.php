<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
  <div class="card">
    <p class="text-sm text-slate-400">Ventas hoy</p>
    <p class="mt-1 text-2xl font-bold"><?= money($stats['sales_today_total']) ?></p>
    <p class="text-xs text-slate-500"><?= (int) $stats['sales_today_count'] ?> operaciones</p>
  </div>
  <div class="card">
    <p class="text-sm text-slate-400">Stock bajo</p>
    <p class="mt-1 text-2xl font-bold text-amber-400"><?= (int) $stats['low_stock_count'] ?></p>
    <a href="<?= url('/reports?type=low_stock') ?>" class="text-xs text-pulse-400">Ver reporte →</a>
  </div>
  <div class="card">
    <p class="text-sm text-slate-400">Compras pendientes</p>
    <p class="mt-1 text-2xl font-bold"><?= (int) $stats['pending_purchases'] ?></p>
  </div>
  <div class="card">
    <p class="text-sm text-slate-400">Caja</p>
    <?php if ($stats['open_cash']): ?>
    <p class="mt-1 text-lg font-semibold text-emerald-400">Abierta</p>
    <p class="text-xs text-slate-500">desde <?= e($stats['open_cash']['opened_at']) ?></p>
    <?php else: ?>
    <p class="mt-1 text-lg font-semibold text-rose-400">Cerrada</p>
    <a href="<?= url('/cash') ?>" class="text-xs text-pulse-400">Abrir caja →</a>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($stats['chart_labels'])): ?>
<div class="card mt-6">
  <h2 class="mb-4 font-semibold">Ventas últimos 7 días</h2>
  <canvas id="dashChart" height="90"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('dashChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($stats['chart_labels']) ?>,
    datasets: [{ label: 'Total $', data: <?= json_encode($stats['chart_values']) ?>, borderColor: '#818cf8', backgroundColor: 'rgba(99,102,241,0.15)', fill: true, tension: 0.3 }]
  },
  options: { plugins: { legend: { display: false } } }
});
</script>
<?php endif; ?>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
  <div class="card">
    <h2 class="mb-4 font-semibold">Últimas ventas</h2>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="text-left text-slate-500"><tr><th class="pb-2">Nº</th><th>Total</th><th>Pago</th><th>Fecha</th></tr></thead>
        <tbody>
        <?php foreach ($stats['recent_sales'] as $sale): ?>
        <tr class="border-t border-slate-800">
          <td class="py-2"><?= e($sale['sale_number']) ?></td>
          <td><?= money($sale['total']) ?></td>
          <td class="capitalize"><?= e(str_replace('_', ' ', $sale['payment_method'])) ?></td>
          <td class="text-slate-500"><?= e($sale['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$stats['recent_sales']): ?><tr><td colspan="4" class="py-4 text-slate-500">Sin ventas recientes</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card">
    <h2 class="mb-4 font-semibold">Más vendidos (30 días)</h2>
    <ul class="space-y-2 text-sm">
      <?php foreach ($stats['top_products'] as $p): ?>
      <li class="flex justify-between border-b border-slate-800/50 py-2">
        <span><?= e($p['product_name']) ?></span>
        <span class="text-slate-400"><?= e($p['qty']) ?> u.</span>
      </li>
      <?php endforeach; ?>
      <?php if (!$stats['top_products']): ?><li class="text-slate-500">Sin datos aún</li><?php endif; ?>
    </ul>
  </div>
</div>
