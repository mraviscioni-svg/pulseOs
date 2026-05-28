<div class="page-header">
  <h2>Compras</h2>
  <p>Órdenes de compra y recepción de mercadería.</p>
</div>

<?php
$basePath = '/purchases';
$searchPlaceholder = 'Proveedor, número de orden…';
$createUrl = url('/purchases/create');
$createLabel = '+ Nueva compra';
$showStatusFilter = true;
$status = $status ?? 'all';
$statusOptions = ['all' => 'Todos', 'pendiente' => 'Pendientes', 'completada' => 'Recibidas'];
$totalCount = count($purchases);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<div class="data-table-wrap">
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
      <td class="font-mono font-medium text-indigo-300">#<?= (int) $p['id'] ?></td>
      <td><?= e($p['supplier_name'] ?? '—') ?></td>
      <td class="font-medium"><?= money($p['total']) ?></td>
      <td>
        <?php if ($p['status'] === 'pendiente'): ?>
        <span class="badge badge-warning">Pendiente</span>
        <?php else: ?>
        <span class="badge badge-success"><?= e($p['status']) ?></span>
        <?php endif; ?>
      </td>
      <td class="text-slate-500 text-xs"><?= e($p['created_at']) ?></td>
      <td>
        <div class="row-actions">
          <?php if ($p['status'] === 'pendiente'): ?>
          <form method="post" action="<?= url('/purchases/' . $p['id'] . '/receive') ?>" class="inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn-action btn-action-ok">Recibir</button>
          </form>
          <?php else: ?>
          <span class="text-xs text-slate-500">—</span>
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
