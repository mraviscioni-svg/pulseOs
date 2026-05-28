<?php
$pageEyebrow = 'Compras';
$pageTitle = 'Proveedores';
$pageDescription = 'Gestioná quién te provee mercadería y servicios.';
$createUrl = url('/suppliers/create');
$createLabel = '+ Nuevo proveedor';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<?php
$basePath = '/suppliers';
$searchPlaceholder = 'Buscar por nombre, empresa o CUIT';
$showStatusFilter = true;
$totalCount = count($suppliers);
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
        <td class="font-medium text-navy-900"><?= e($s['name']) ?></td>
        <td class="text-slate-600"><?= e($s['company'] ?? '—') ?></td>
        <td class="font-mono text-xs text-slate-500"><?= e($s['tax_id'] ?? '—') ?></td>
        <td class="text-xs text-slate-500"><?= e($s['phone'] ?? '—') ?><?= !empty($s['email']) ? ' · ' . e($s['email']) : '' ?></td>
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
      <tr><td colspan="6"><?php $message = 'No hay proveedores'; $actionUrl = url('/suppliers/create'); $actionLabel = '+ Crear proveedor'; require __DIR__ . '/../partials/empty_state.php'; ?></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if (!empty($modal)): ?>
<?php
ob_start();
require __DIR__ . '/_form_modal.php';
$modalContent = ob_get_clean();
$modalTitle = $modal['title'];
$modalSubtitle = $modal['subtitle'] ?? null;
$closeUrl = $modal['closeUrl'] ?? url('/suppliers');
require __DIR__ . '/../partials/form_modal.php';
?>
<?php endif; ?>
