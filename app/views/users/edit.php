<form method="post" action="<?= url('/users/' . $user['id']) ?>" class="card max-w-md space-y-4">
  <?= csrf_field() ?>
  <input name="name" value="<?= e($user['name']) ?>" required class="input-field">
  <input type="email" name="email" value="<?= e($user['email']) ?>" required class="input-field">
  <input name="phone" value="<?= e($user['phone'] ?? '') ?>" class="input-field" placeholder="Teléfono">
  <select name="role_id" class="input-field">
    <?php foreach ($roles as $r): ?>
    <option value="<?= (int)$r['id'] ?>" <?= (int)$user['role_id'] === (int)$r['id'] ? 'selected' : '' ?>><?= e($r['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <input type="password" name="password" class="input-field" placeholder="Nueva contraseña (opcional)">
  <button class="btn-primary">Guardar</button>
  <a href="<?= url('/users') ?>" class="ml-2 text-sm text-slate-400">Volver</a>
</form>
