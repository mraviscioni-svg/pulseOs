<?php
$pageEyebrow = 'Equipo';
$pageTitle = 'Usuarios';
$pageDescription = 'Equipo con acceso al panel de tu comercio.';
$createUrl = url('/users?invite=1');
$createLabel = '+ Invitar usuario';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<?php
$basePath = '/users';
$searchPlaceholder = 'Buscar por nombre, usuario o email';
$showStatusFilter = true;
$totalCount = count($users);
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
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($users as $u): ?>
      <tr class="<?= !$u['is_active'] ? 'opacity-60' : '' ?>">
        <td class="font-mono text-sm text-accent-600"><?= e($u['username'] ?? '') ?></td>
        <td class="font-medium text-navy-900"><?= e($u['name']) ?></td>
        <td class="text-xs text-slate-500"><?= e($u['email']) ?></td>
        <td class="text-slate-600"><?= e($u['role_name']) ?></td>
        <td><?php $active = (bool) $u['is_active']; require __DIR__ . '/../partials/status_badge.php'; ?></td>
        <td>
          <?php
          $editUrl = url('/users/' . $u['id'] . '/edit');
          $isActive = (bool) $u['is_active'];
          $currentUserId = (int) \App\Core\Session::get('user_id');
          if ((int) $u['id'] !== $currentUserId) {
              $toggleUrl = url('/users/' . $u['id'] . '/toggle');
              $deleteUrl = url('/users/' . $u['id'] . '/delete');
              $deleteConfirm = '¿Eliminar al usuario «' . $u['name'] . '»? Esta acción no se puede deshacer.';
          } else {
              $toggleUrl = null;
              $deleteUrl = null;
          }
          require __DIR__ . '/../partials/row_actions.php';
          ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$users): ?>
      <tr><td colspan="6"><?php $message = 'No hay usuarios'; require __DIR__ . '/../partials/empty_state.php'; ?></td></tr>
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
$closeUrl = $modal['closeUrl'] ?? url('/users');
require __DIR__ . '/../partials/form_modal.php';
?>
<?php endif; ?>
