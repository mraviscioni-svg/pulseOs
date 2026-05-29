<?php
$pageEyebrow = 'Análisis';
$pageTitle = 'Reportes';
$pageDescription = 'Consultá ventas, stock, márgenes y caja por período.';
require __DIR__ . '/../partials/crud_page_header.php';

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
<form method="get" class="form-card mb-6 flex flex-wrap items-end gap-3">
  <div>
    <label class="label">Reporte</label>
    <select name="type" class="input-field">
      <?php foreach ($types as $k => $typeLabel): ?>
      <option value="<?= e($k) ?>" <?= $type === $k ? 'selected' : '' ?>><?= e($typeLabel) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php if ($type !== 'low_stock' && $type !== 'no_movement'): ?>
  <div><label class="label">Desde</label><input type="date" name="from" value="<?= e($from) ?>" class="input-field"></div>
  <div><label class="label">Hasta</label><input type="date" name="to" value="<?= e($to) ?>" class="input-field"></div>
  <?php endif; ?>
  <?php if ($type === 'no_movement'): ?>
  <div><label class="label">Días sin venta</label><input type="number" name="days" value="<?= e($_GET['days'] ?? '90') ?>" class="input-field w-24" min="1"></div>
  <?php endif; ?>
  <button type="submit" class="btn-primary">Generar</button>
</form>

<?php if ($type === 'sales_day' && !empty($chartLabels)): ?>
<div class="form-card mb-6"><canvas id="reportChart" height="80"></canvas></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('reportChart'), {
  type: 'bar',
  data: { labels: <?= json_encode($chartLabels) ?>, datasets: [{ label: 'Ventas $', data: <?= json_encode($chartValues) ?>, backgroundColor: 'rgba(232,180,74,0.65)' }] },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
<?php endif; ?>

<div class="data-table-wrap">
  <div class="data-table-head"><h3>Resultados</h3></div>
  <div class="data-table-scroll">
    <table class="data-table">
      <tbody>
      <?php if ($type === 'sales_day'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e((string) ($r['day'] ?? '')) ?></td><td class="text-slate-600"><?= (int) ($r['sales_count'] ?? 0) ?> ventas</td><td><?= money($r['total'] ?? 0) ?></td></tr>
      <?php endforeach; elseif ($type === 'sales_user'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e($r['user_name'] ?? 'Sin usuario') ?></td><td class="text-slate-600"><?= (int) ($r['sales_count'] ?? 0) ?></td><td><?= money($r['total'] ?? 0) ?></td></tr>
      <?php endforeach; elseif ($type === 'top_products'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e($r['product_name'] ?? '') ?></td><td class="text-slate-600"><?= e($r['qty'] ?? 0) ?> u.</td><td><?= money($r['revenue'] ?? 0) ?></td></tr>
      <?php endforeach; elseif ($type === 'low_stock'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e($r['name'] ?? '') ?></td><td class="text-amber-700"><?= e($r['stock'] ?? 0) ?> / <?= e($r['min_stock'] ?? 0) ?></td><td><?= money($r['price'] ?? 0) ?></td></tr>
      <?php endforeach; elseif ($type === 'margin'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e($r['product_name'] ?? '') ?></td><td><?= money($r['margin'] ?? 0) ?></td><td class="text-slate-600"><?= e($r['margin_pct'] ?? 0) ?>%</td></tr>
      <?php endforeach; elseif ($type === 'purchases_supplier'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e($r['supplier_name'] ?? '') ?></td><td class="text-slate-600"><?= (int) ($r['orders'] ?? 0) ?> órdenes</td><td><?= money($r['total'] ?? 0) ?></td></tr>
      <?php endforeach; elseif ($type === 'cash_daily'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e($r['opened_at'] ?? '') ?></td><td class="text-slate-600"><?= e($r['status'] ?? '') ?></td><td><?= isset($r['difference']) && $r['difference'] !== null ? money($r['difference']) : '—' ?></td></tr>
      <?php endforeach; elseif ($type === 'no_movement'): foreach ($data as $r): ?>
      <tr><td class="font-medium text-navy-900"><?= e($r['name'] ?? '') ?></td><td class="text-slate-600">Stock: <?= e($r['stock'] ?? 0) ?></td><td><?= money($r['price'] ?? 0) ?></td></tr>
      <?php endforeach; endif; ?>
      <?php if ($data === []): ?>
      <tr><td colspan="3" class="py-6 text-center text-slate-500">Sin datos para el período seleccionado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
