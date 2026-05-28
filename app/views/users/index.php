<div class="grid gap-6 lg:grid-cols-2">
  <div class="card overflow-x-auto">
    <h3 class="mb-4 font-semibold">Equipo</h3>
    <table class="w-full text-sm">
      <thead class="text-slate-500"><tr><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): ?>
      <tr class="border-t border-slate-800">
        <td class="py-2 font-mono text-pulse-400"><?= e($u['username'] ?? '') ?></td>
        <td class="py-2"><?= e($u['name']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= e($u['role_name']) ?></td>
        <td><?= $u['is_active'] ? 'Activo' : 'Inactivo' ?></td>
        <td class="text-right space-x-2">
          <a href="<?= url('/users/' . $u['id'] . '/edit') ?>" class="text-pulse-400 text-xs">Editar</a>
          <?php if ((int)$u['id'] !== (int)\App\Core\Session::get('user_id')): ?>
          <form method="post" action="<?= url('/users/' . $u['id'] . '/toggle') ?>" class="inline"><?= csrf_field() ?>
            <input type="hidden" name="is_active" value="<?= $u['is_active'] ? '0' : '1' ?>">
            <button class="text-xs text-slate-400"><?= $u['is_active'] ? 'Desactivar' : 'Activar' ?></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <form method="post" action="<?= url('/users') ?>" class="card space-y-4">
    <?= csrf_field() ?>
    <h3 class="font-semibold">Invitar usuario</h3>
    <input name="name" required placeholder="Nombre" class="input-field">
    <input name="username" required minlength="3" pattern="[a-zA-Z0-9._-]+" placeholder="Usuario único" class="input-field">
    <input type="email" name="email" required placeholder="Email contacto" class="input-field">
    <input type="password" name="password" required minlength="8" placeholder="Contraseña temporal" class="input-field">
    <select name="role_id" required class="input-field">
      <?php foreach ($roles as $r): ?>
      <option value="<?= (int)$r['id'] ?>"><?= e($r['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn-primary">Crear usuario</button>
  </form>
</div>
