<?php
/** @var array<string, mixed>|null $user */
/** @var list<array<string, mixed>> $roles */
$isEdit = !empty($user);
$field = static function (string $key, mixed $default = '') use ($user): string {
    $v = old($key);
    if ($v !== '' && $v !== null) {
        return (string) $v;
    }
    if ($user !== null && array_key_exists($key, $user)) {
        return (string) $user[$key];
    }

    return (string) $default;
};
?>
<form method="post" action="<?= $isEdit ? url('/users/' . $user['id']) : url('/users') ?>">
  <?= csrf_field() ?>
  <div class="modal-form-grid">
    <div class="sm:col-span-2">
      <label class="label">Nombre completo</label>
      <input name="name" value="<?= e($field('name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">Usuario</label>
      <input name="username" value="<?= e($field('username')) ?>" required class="input-field" placeholder="nombre.apellido">
    </div>
    <div>
      <label class="label">Email</label>
      <input type="email" name="email" value="<?= e($field('email')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label"><?= $isEdit ? 'Nueva contraseña' : 'Contraseña temporal' ?></label>
      <input type="password" name="password" <?= $isEdit ? '' : 'required' ?> class="input-field" placeholder="<?= $isEdit ? 'Dejar vacío para no cambiar' : '' ?>">
    </div>
    <div>
      <label class="label">Teléfono</label>
      <input name="phone" value="<?= e($field('phone')) ?>" class="input-field">
    </div>
    <div class="sm:col-span-2">
      <label class="label">Rol</label>
      <select name="role_id" required class="input-field">
        <?php foreach ($roles as $r): ?>
        <option value="<?= (int) $r['id'] ?>" <?= (int) $field('role_id', (string) ($user['role_id'] ?? '')) === (int) $r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="modal-form-footer">
    <a href="<?= url('/users') ?>" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary"><?= $isEdit ? 'Guardar' : 'Crear usuario' ?></button>
  </div>
</form>
