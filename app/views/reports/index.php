<?php
$types = [
    'sales_day' => 'Ventas por día',
    'sales_user' => 'Ventas por usuario',
    'top_products' => 'Más vendidos',
    'low_stock' => 'Stock bajo',
    'margin' => 'Margen de ganancia',
    'purchases_supplier' => 'Compras por proveedor',
    'cash_daily' => 'Caja diaria',
    'no_movement' => 'Sin movimiento',
];
?>
<form method="get" class="card mb-4 flex flex-wrap items-end gap-3">
  <div>
    <label class="label">Reporte</label>
    <select name="type" class="input-field">
      <?php foreach ($types as $k => $label): ?>
      <option value="<?= e($k) ?>" <?= $type === $k ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php if ($type !== 'low_stock' && $type !== 'no_movement'): ?>
  <div><label class="label">Desde</label><input type="date" name="from" value="<?= e($from) ?>" class="input-field"></div>
  <div><label class="label">Hasta</label><input type="date" name="to" value="<?= e($to) ?>" class="input-field"></div>
  <?php endif; ?>
  <?php if ($type === 'no_movement'): ?>
  <div><label class="label">Días sin venta</label><input type="number" name="days" value="<?= e($_GET['days'] ?? '90') ?>" class="input-field w-24"></div>
  <?php endif; ?>
  <button class="btn-primary">Generar</button>
</form>

<?php if ($type === 'sales_day' && $chartLabels): ?>
<div class="card mb-4"><canvas id="reportChart" height="80"></canvas></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('reportChart'), {
  type: 'bar',
  data: { labels: <?= json_encode($chartLabels) ?>, datasets: [{ label: 'Ventas $', data: <?= json_encode($chartValues) ?>, backgroundColor: 'rgba(99,102,241,0.6)' }] },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
<?php endif; ?>

<div class="card overflow-x-auto">
  <table class="w-full text-sm">
    <tbody>
    <?php if ($type === 'sales_day'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['day']) ?></td><td><?= (int)$r['sales_count'] ?> ventas</td><td><?= money($r['total']) ?></td></tr>
    <?php endforeach; elseif ($type === 'sales_user'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['user_name'] ?? 'Sin usuario') ?></td><td><?= (int)$r['sales_count'] ?></td><td><?= money($r['total']) ?></td></tr>
    <?php endforeach; elseif ($type === 'top_products'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['product_name']) ?></td><td><?= e($r['qty']) ?> u.</td><td><?= money($r['revenue']) ?></td></tr>
    <?php endforeach; elseif ($type === 'low_stock'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['name']) ?></td><td class="text-amber-400"><?= e($r['stock']) ?> / <?= e($r['min_stock']) ?></td><td><?= money($r['price']) ?></td></tr>
    <?php endforeach; elseif ($type === 'margin'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['product_name']) ?></td><td><?= money($r['margin']) ?></td><td><?= e($r['margin_pct']) ?>%</td></tr>
    <?php endforeach; elseif ($type === 'purchases_supplier'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['supplier_name']) ?></td><td><?= (int)$r['orders'] ?> órdenes</td><td><?= money($r['total']) ?></td></tr>
    <?php endforeach; elseif ($type === 'cash_daily'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['opened_at']) ?></td><td><?= e($r['status']) ?></td><td><?= $r['difference'] !== null ? money($r['difference']) : '—' ?></td></tr>
    <?php endforeach; elseif ($type === 'no_movement'): foreach ($data as $r): ?>
    <tr class="border-t border-slate-800"><td class="py-2"><?= e($r['name']) ?></td><td>Stock: <?= e($r['stock']) ?></td><td><?= money($r['price']) ?></td></tr>
    <?php endforeach; endif; ?>
    <?php if (!$data): ?><tr><td class="py-6 text-slate-500">Sin datos para el período seleccionado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
