<div class="page-header">
  <h2>Usuarios</h2>
  <p>Equipo con acceso al panel de tu comercio.</p>
</div>

<?php
$basePath = '/users';
$searchPlaceholder = 'Nombre, usuario o email…';
$showStatusFilter = true;
$totalCount = count($users);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<div class="grid gap-6 xl:grid-cols-3">
  <div class="data-table-wrap xl:col-span-2">
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
        <td class="font-mono text-sm text-indigo-300"><?= e($u['username'] ?? '') ?></td>
        <td class="font-medium"><?= e($u['name']) ?></td>
        <td class="text-slate-400 text-xs"><?= e($u['email']) ?></td>
        <td><?= e($u['role_name']) ?></td>
        <td><?php $active = (bool) $u['is_active']; require __DIR__ . '/../partials/status_badge.php'; ?></td>
        <td>
          <?php
          $editUrl = url('/users/' . $u['id'] . '/edit');
          $isActive = (bool) $u['is_active'];
          if ((int) $u['id'] !== (int) \App\Core\Session::get('user_id')) {
              $toggleUrl = url('/users/' . $u['id'] . '/toggle');
          } else {
              $toggleUrl = null;
          }
          $deleteUrl = null;
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

  <form method="post" action="<?= url('/users') ?>" class="form-card h-fit space-y-4">
    <?= csrf_field() ?>
    <h3 class="text-lg font-semibold">Invitar usuario</h3>
    <p class="text-sm text-slate-400">Creá un acceso con contraseña temporal.</p>
    <?php
    $name = 'name'; $label = 'Nombre completo'; $type = 'input'; $value = ''; $required = true;
    require __DIR__ . '/../partials/form_group.php';
    $name = 'username'; $label = 'Usuario'; $type = 'input'; $placeholder = 'unico.en.pulseos';
    $hint = 'Solo letras, números, punto y guión.';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'email'; $label = 'Email'; $type = 'email'; $hint = null;
    require __DIR__ . '/../partials/form_group.php';
    $name = 'password'; $label = 'Contraseña temporal'; $type = 'password'; $required = true;
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <div class="form-group">
      <label class="label">Rol</label>
      <select name="role_id" required class="input-field">
        <?php foreach ($roles as $r): ?>
        <option value="<?= (int) $r['id'] ?>"><?= e($r['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn-primary w-full">Crear usuario</button>
  </form>
</div>
