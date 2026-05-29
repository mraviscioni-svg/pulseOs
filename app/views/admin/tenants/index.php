<?php
$pageEyebrow = 'Platform';
$pageTitle = 'Comercios';
$pageDescription = 'Tenants registrados en la plataforma ' . app_name() . '.';
$createUrl = url('/admin/tenants/create');
$createLabel = '+ Nuevo comercio';
require __DIR__ . '/../../partials/crud_page_header.php';
?>

<?php
$basePath = '/admin/tenants';
$searchPlaceholder = 'Buscar por nombre, email o slug';
$totalCount = count($tenants);
require __DIR__ . '/../../partials/crud_toolbar.php';
?>

<div class="data-table-wrap">
  <div class="data-table-head">
    <h3>Listado</h3>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
    <thead>
      <tr>
        <th>Negocio</th>
        <th>Rubro</th>
        <th>Usuarios</th>
        <th>Productos</th>
        <th>Ventas</th>
        <th>Estado</th>
        <th>Alta</th>
        <th class="text-right">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($tenants as $t): ?>
    <tr class="<?= !$t['is_active'] ? 'opacity-60' : '' ?>">
      <td>
        <p class="font-medium text-navy-900"><?= e($t['name']) ?></p>
        <p class="text-xs text-slate-500"><?= e($t['slug']) ?> · <?= e($t['email'] ?? '—') ?></p>
      </td>
      <td class="capitalize text-slate-600"><?= e(str_replace('_', ' ', (string) $t['business_type'])) ?></td>
      <td><?= (int) $t['users_count'] ?></td>
      <td><?= (int) $t['products_count'] ?></td>
      <td><?= money($t['sales_total']) ?></td>
      <td>
        <?php if ($t['is_active']): ?>
        <span class="badge badge-success">Activo</span>
        <?php else: ?>
        <span class="badge badge-muted">Suspendido</span>
        <?php endif; ?>
      </td>
      <td class="text-xs text-slate-500"><?= e($t['created_at']) ?></td>
      <td>
        <div class="row-actions">
          <a href="<?= url('/admin/tenants/' . $t['id']) ?>" class="btn-action btn-action-edit">Gestionar</a>
          <form method="post" action="<?= url('/admin/tenants/' . $t['id'] . '/toggle') ?>" class="inline">
            <?= csrf_field() ?>
            <input type="hidden" name="return" value="list">
            <input type="hidden" name="is_active" value="<?= $t['is_active'] ? '0' : '1' ?>">
            <button type="submit" class="btn-action <?= $t['is_active'] ? 'btn-action-warn' : 'btn-action-ok' ?>">
              <?= $t['is_active'] ? 'Suspender' : 'Activar' ?>
            </button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$tenants): ?>
    <tr><td colspan="8"><?php $message = 'No hay comercios' . ($q ? ' para esa búsqueda' : ''); $actionUrl = url('/admin/tenants/create'); require __DIR__ . '/../../partials/empty_state.php'; ?></td></tr>
    <?php endif; ?>
    </tbody>
    </table>
  </div>
</div>
