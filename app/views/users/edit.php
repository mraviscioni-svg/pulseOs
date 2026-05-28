<div class="page-header">
  <h2>Editar usuario</h2>
  <p><a href="<?= url('/users') ?>" class="text-indigo-400 hover:underline">← Volver al equipo</a></p>
</div>

<form method="post" action="<?= url('/users/' . $user['id']) ?>" class="form-card max-w-lg">
  <?= csrf_field() ?>
  <div class="form-section space-y-4">
    <?php
    $name = 'name'; $label = 'Nombre'; $type = 'input'; $value = $user['name']; $required = true;
    require __DIR__ . '/../partials/form_group.php';
    $name = 'username'; $label = 'Usuario'; $type = 'input'; $value = $user['username'] ?? ''; $required = true;
    require __DIR__ . '/../partials/form_group.php';
    $name = 'email'; $label = 'Email'; $type = 'email'; $value = $user['email']; $required = true;
    require __DIR__ . '/../partials/form_group.php';
    $name = 'phone'; $label = 'Teléfono'; $type = 'input'; $value = $user['phone'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <div class="form-group">
      <label class="label">Rol</label>
      <select name="role_id" class="input-field">
        <?php foreach ($roles as $r): ?>
        <option value="<?= (int) $r['id'] ?>" <?= (int) $user['role_id'] === (int) $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php
    $name = 'password'; $label = 'Nueva contraseña'; $type = 'password'; $value = '';
    $placeholder = 'Dejar vacío para no cambiar'; $required = false;
    require __DIR__ . '/../partials/form_group.php';
    ?>
  </div>
  <div class="form-actions mt-6">
    <button type="submit" class="btn-primary">Guardar cambios</button>
    <a href="<?= url('/users') ?>" class="btn-secondary">Cancelar</a>
  </div>
</form>
