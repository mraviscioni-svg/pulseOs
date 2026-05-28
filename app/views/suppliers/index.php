<div class="page-header">
  <div class="page-header-main">
    <p class="page-eyebrow">Proveedores</p>
    <h2>Proveedores</h2>
    <p>Gestioná quién te provee mercadería y servicios.</p>
  </div>
</div>

<?php
$basePath = '/suppliers';
$searchPlaceholder = 'Nombre, empresa, CUIT, teléfono…';
$createUrl = url('/suppliers/create');
$createLabel = '+ Nuevo proveedor';
$showStatusFilter = true;
$totalCount = count($suppliers);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<div class="data-table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Empresa</th>
        <th>CUIT</th>
        <th>Contacto</th>
        <th>Estado</th>
        <th class="text-right">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($suppliers as $s): ?>
    <tr class="<?= !$s['is_active'] ? 'opacity-60' : '' ?>">
      <td class="font-medium"><?= e($s['name']) ?></td>
      <td><?= e($s['company'] ?? '—') ?></td>
      <td class="font-mono text-xs"><?= e($s['tax_id'] ?? '—') ?></td>
      <td class="text-slate-400 text-xs"><?= e($s['phone'] ?? '—') ?><?= !empty($s['email']) ? ' · ' . e($s['email']) : '' ?></td>
      <td><?php $active = (bool) $s['is_active']; require __DIR__ . '/../partials/status_badge.php'; ?></td>
      <td>
        <?php
        $editUrl = url('/suppliers/' . $s['id'] . '/edit');
        $toggleUrl = url('/suppliers/' . $s['id'] . '/toggle');
        $isActive = (bool) $s['is_active'];
        $deleteUrl = url('/suppliers/' . $s['id'] . '/delete');
        $deleteConfirm = '¿Eliminar el proveedor «' . $s['name'] . '»?';
        require __DIR__ . '/../partials/row_actions.php';
        ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$suppliers): ?>
    <tr><td colspan="6"><?php $message = 'No hay proveedores'; $actionUrl = url('/suppliers/create'); require __DIR__ . '/../partials/empty_state.php'; ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
