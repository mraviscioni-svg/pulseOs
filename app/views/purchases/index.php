<?php
$pageEyebrow = 'Compras';
$pageTitle = 'Compras';
$pageDescription = 'Órdenes de compra y recepción de mercadería.';
$createUrl = url('/purchases/create');
$createLabel = '+ Nueva compra';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<?php
$basePath = '/purchases';
$searchPlaceholder = 'Buscar por proveedor o número de orden';
$showStatusFilter = true;
$status = $status ?? 'all';
$statusOptions = ['all' => 'Todos', 'pendiente' => 'Pendientes', 'completada' => 'Recibidas'];
$totalCount = count($purchases);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<div class="data-table-wrap">
  <div class="data-table-head">
    <h3>Listado</h3>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Ref</th>
          <th>Proveedor</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Fecha</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($purchases as $p): ?>
      <tr>
        <td class="font-mono font-medium text-accent-600">#<?= (int) $p['id'] ?></td>
        <td class="font-medium text-navy-900"><?= e($p['supplier_name'] ?? '—') ?></td>
        <td class="font-medium tabular-nums"><?= money($p['total']) ?></td>
        <td>
          <?php if ($p['status'] === 'pendiente'): ?>
          <span class="badge badge-warning">Pendiente</span>
          <?php else: ?>
          <span class="badge badge-success"><span class="badge-dot" aria-hidden="true"></span><?= e($p['status']) ?></span>
          <?php endif; ?>
        </td>
        <td class="text-xs text-slate-500"><?= e($p['created_at']) ?></td>
        <td>
          <div class="row-actions">
            <?php if ($p['status'] === 'pendiente'): ?>
            <form method="post" action="<?= url('/purchases/' . $p['id'] . '/receive') ?>" class="inline">
              <?= csrf_field() ?>
              <button type="submit" class="btn-action btn-action-ok">Recibir</button>
            </form>
            <?php else: ?>
            <span class="text-xs text-slate-400">—</span>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$purchases): ?>
      <tr><td colspan="6"><?php $message = 'No hay compras registradas'; $actionUrl = url('/purchases/create'); require __DIR__ . '/../partials/empty_state.php'; ?></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
