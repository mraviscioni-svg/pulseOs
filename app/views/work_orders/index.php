<?php
$pageEyebrow = 'Taller';
$pageTitle = 'Órdenes de trabajo';
$pageDescription = 'Presupuestos, seguimiento en taller y cierre con cobro en POS.';
$createUrl = url('/work-orders/create');
$createLabel = '+ Nueva orden';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<?php
$basePath = '/work-orders';
$searchPlaceholder = 'Buscar por cliente, patente, Nº OT…';
$showStatusFilter = true;
$status = $status ?? 'all';
$statusOptions = $statusOptions ?? ['all' => 'Todos'];
$queryExtra = !empty($assigned) ? ['assigned' => (string) $assigned] : [];
$totalCount = count($orders);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<?php if (!empty($users)): ?>
<form method="get" action="<?= url('/work-orders') ?>" class="mb-4 flex flex-wrap items-center gap-2 text-sm">
  <input type="hidden" name="q" value="<?= e($q ?? '') ?>">
  <input type="hidden" name="status" value="<?= e($status) ?>">
  <label class="text-slate-600">Técnico:</label>
  <select name="assigned" class="input-field w-auto min-w-[10rem]" onchange="this.form.submit()">
    <option value="">Todos</option>
    <?php foreach ($users as $u): ?>
    <option value="<?= (int) $u['id'] ?>" <?= (int) ($assigned ?? 0) === (int) $u['id'] ? 'selected' : '' ?>><?= e($u['name']) ?></option>
    <?php endforeach; ?>
  </select>
</form>
<?php endif; ?>

<div class="data-table-wrap">
  <div class="data-table-head">
    <h3>Listado</h3>
    <?php if (isset($totalCount)): ?>
    <span class="text-xs text-slate-500"><?= (int) $totalCount ?> orden<?= $totalCount === 1 ? '' : 'es' ?></span>
    <?php endif; ?>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Nº OT</th>
          <th>Cliente</th>
          <th>Vehículo</th>
          <th>Técnico</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
      <tr>
        <td class="font-mono font-medium text-accent-600">
          <a href="<?= url('/work-orders/' . $o['id']) ?>" class="hover:underline"><?= e($o['order_number']) ?></a>
        </td>
        <td class="font-medium text-navy-900"><?= e($o['customer_name']) ?></td>
        <td class="text-slate-600"><?= e($o['vehicle_label'] ?? '—') ?></td>
        <td class="text-slate-600"><?= e($o['assigned_name'] ?? '—') ?></td>
        <td class="tabular-nums font-medium"><?= money($o['total']) ?></td>
        <td><span class="<?= wo_status_badge_class((string) $o['status']) ?>"><?= e(wo_status_label((string) $o['status'])) ?></span></td>
        <td class="text-xs text-slate-500"><?= e($o['created_at']) ?></td>
        <td>
          <div class="row-actions">
            <a href="<?= url('/work-orders/' . $o['id']) ?>" class="btn-action">Ver</a>
            <?php if (in_array($o['status'], config('work_order_statuses')['editable'] ?? [], true)): ?>
            <a href="<?= url('/work-orders/' . $o['id'] . '/edit') ?>" class="btn-action">Editar</a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$orders): ?>
      <tr>
        <td colspan="8">
          <?php
          $message = 'No hay órdenes de trabajo' . ($q ? ' para esa búsqueda' : '');
          $actionUrl = url('/work-orders/create');
          $actionLabel = '+ Crear orden';
          require __DIR__ . '/../partials/empty_state.php';
          ?>
        </td>
      </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
