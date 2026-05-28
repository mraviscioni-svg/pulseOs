<div class="grid gap-6 lg:grid-cols-2">
  <div class="card overflow-x-auto">
    <h3 class="mb-4 font-semibold">Equipo</h3>
    <table class="w-full text-sm">
      <thead class="text-slate-500"><tr><th>Nombre</th><th>Email</th><th>Rol</th></tr></thead>
      <tbody>
      <?php foreach ($users as $u): ?>
      <tr class="border-t border-slate-800">
        <td class="py-2"><?= e($u['name']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= e($u['role_name']) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <form method="post" action="<?= url('/users') ?>" class="card space-y-4">
    <?= csrf_field() ?>
    <h3 class="font-semibold">Invitar usuario</h3>
    <input name="name" required placeholder="Nombre" class="input-field">
    <input type="email" name="email" required placeholder="Email" class="input-field">
    <input type="password" name="password" required minlength="8" placeholder="Contraseña temporal" class="input-field">
    <select name="role_id" required class="input-field">
      <?php foreach ($roles as $r): ?>
      <option value="<?= (int)$r['id'] ?>"><?= e($r['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn-primary">Crear usuario</button>
  </form>
</div>
