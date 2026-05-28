<?php $t = $detail['tenant']; $stats = $detail['stats']; ?>
<div class="mb-4">
  <a href="<?= url('/admin/tenants') ?>" class="text-sm text-violet-400 hover:underline">← Volver al listado</a>
</div>

<div class="grid gap-4 lg:grid-cols-3">
  <div class="card lg:col-span-2">
    <div class="flex flex-wrap items-start justify-between gap-3">
      <div>
        <h2 class="text-xl font-bold"><?= e($t['name']) ?></h2>
        <p class="text-sm text-slate-400">Slug: <?= e($t['slug']) ?> · ID: <?= (int) $t['id'] ?></p>
      </div>
      <form method="post" action="<?= url('/admin/tenants/' . $t['id'] . '/toggle') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="is_active" value="<?= $t['is_active'] ? '0' : '1' ?>">
        <button class="<?= $t['is_active'] ? 'bg-rose-600 hover:bg-rose-500' : 'bg-emerald-600 hover:bg-emerald-500' ?> rounded-lg px-4 py-2 text-sm font-medium text-white">
          <?= $t['is_active'] ? 'Suspender' : 'Activar' ?>
        </button>
      </form>
    </div>
  </div>
  <div class="card space-y-2 text-sm">
    <p><span class="text-slate-400">Usuarios</span> <strong class="float-right"><?= (int) $stats['users'] ?></strong></p>
    <p><span class="text-slate-400">Productos</span> <strong class="float-right"><?= (int) $stats['products'] ?></strong></p>
    <p><span class="text-slate-400">Ventas</span> <strong class="float-right"><?= (int) $stats['sales'] ?></strong></p>
    <p><span class="text-slate-400">Alta</span> <strong class="float-right text-xs"><?= e($t['created_at']) ?></strong></p>
  </div>
</div>

<div class="card mt-6 max-w-2xl">
  <h3 class="mb-4 font-semibold">Configuración del comercio</h3>
  <form method="post" action="<?= url('/admin/tenants/' . $t['id']) ?>" class="space-y-4">
    <?= csrf_field() ?>
    <div>
      <label class="label">Nombre</label>
      <input name="name" value="<?= e($t['name']) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">Rubro</label>
      <select name="business_type" required class="input-field">
        <?php foreach ($businessTypes as $key => $type): ?>
        <option value="<?= e($key) ?>" <?= $t['business_type'] === $key ? 'selected' : '' ?>><?= e($type['label']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="label">Email</label>
        <input type="email" name="email" value="<?= e($t['email'] ?? '') ?>" required class="input-field">
      </div>
      <div>
        <label class="label">Teléfono</label>
        <input name="phone" value="<?= e($t['phone'] ?? '') ?>" class="input-field">
      </div>
    </div>
    <div>
      <label class="label">Dirección</label>
      <input name="address" value="<?= e($t['address'] ?? '') ?>" class="input-field">
    </div>
    <button type="submit" class="btn-primary">Guardar cambios</button>
  </form>
</div>

<div class="card mt-6 overflow-x-auto">
  <h3 class="mb-4 font-semibold">Usuarios del comercio</h3>
  <table class="w-full text-sm">
    <thead class="text-slate-500"><tr><th class="pb-2">Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Último login</th></tr></thead>
    <tbody>
    <?php foreach ($detail['users'] as $u): ?>
    <tr class="border-t border-slate-800">
      <td class="py-2 font-mono text-violet-300"><?= e($u['username'] ?? '') ?></td>
      <td class="py-2"><?= e($u['name']) ?></td>
      <td><?= e($u['email']) ?></td>
      <td><?= e($u['role_name']) ?></td>
      <td><?= $u['is_active'] ? 'Activo' : 'Inactivo' ?></td>
      <td class="text-slate-500"><?= e($u['last_login_at'] ?? '—') ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$detail['users']): ?><tr><td colspan="6" class="py-4 text-slate-500">Sin usuarios.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
