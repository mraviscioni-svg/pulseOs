<?php
$pageEyebrow = 'Clientes';
$pageTitle = 'Clientes';
$pageDescription = 'Directorio de clientes para ventas y órdenes de trabajo.';
$createUrl = url('/customers/create');
$createLabel = '+ Nuevo cliente';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<?php
$basePath = '/customers';
$searchPlaceholder = 'Buscar por nombre, teléfono, CUIT o email';
$showStatusFilter = true;
$totalCount = count($customers);
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
          <th>CUIT/DNI</th>
          <th>Contacto</th>
          <?php if (module_enabled('work_orders')): ?>
          <th>OT</th>
          <?php endif; ?>
          <th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($customers as $c): ?>
      <tr class="<?= !$c['is_active'] ? 'opacity-60' : '' ?>">
        <td class="font-medium text-navy-900">
          <a href="<?= url('/customers/' . $c['id']) ?>" class="hover:text-accent-600"><?= e($c['name']) ?></a>
        </td>
        <td class="text-slate-600"><?= e($c['company'] ?? '—') ?></td>
        <td class="font-mono text-xs text-slate-500"><?= e($c['tax_id'] ?? '—') ?></td>
        <td class="text-xs text-slate-500">
          <?= e($c['phone'] ?? '—') ?>
          <?php if (!empty($c['email'])): ?><br><?= e($c['email']) ?><?php endif; ?>
        </td>
        <?php if (module_enabled('work_orders')): ?>
        <td class="tabular-nums text-slate-600"><?= (int) ($c['work_orders_count'] ?? 0) ?></td>
        <?php endif; ?>
        <td><?php $active = (bool) $c['is_active']; require __DIR__ . '/../partials/status_badge.php'; ?></td>
        <td>
          <?php
          $editUrl = url('/customers/' . $c['id'] . '/edit');
          $toggleUrl = url('/customers/' . $c['id'] . '/toggle');
          $isActive = (bool) $c['is_active'];
          $deleteUrl = url('/customers/' . $c['id'] . '/delete');
          $deleteConfirm = '¿Eliminar el cliente «' . $c['name'] . '»?';
          require __DIR__ . '/../partials/row_actions.php';
          ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$customers): ?>
      <tr>
        <td colspan="<?= module_enabled('work_orders') ? 7 : 6 ?>">
          <?php
          $message = 'No hay clientes' . ($q ? ' para esa búsqueda' : '');
          $actionUrl = url('/customers/create');
          $actionLabel = '+ Crear cliente';
          require __DIR__ . '/../partials/empty_state.php';
          ?>
        </td>
      </tr>
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
$closeUrl = $modal['closeUrl'] ?? url('/customers');
require __DIR__ . '/../partials/form_modal.php';
?>
<?php endif; ?>
