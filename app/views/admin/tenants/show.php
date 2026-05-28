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
        <p class="mt-1 text-sm">Rubro: <span class="capitalize"><?= e($businessTypes[$t['business_type']]['label'] ?? $t['business_type']) ?></span></p>
        <p class="text-sm">Email: <?= e($t['email'] ?? '—') ?> · Tel: <?= e($t['phone'] ?? '—') ?></p>
        <?php if (!empty($t['address'])): ?><p class="text-sm text-slate-400"><?= e($t['address']) ?></p><?php endif; ?>
      </div>
      <form method="post" action="<?= url('/admin/tenants/' . $t['id'] . '/toggle') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="is_active" value="<?= $t['is_active'] ? '0' : '1' ?>">
        <button class="<?= $t['is_active'] ? 'bg-rose-600 hover:bg-rose-500' : 'bg-emerald-600 hover:bg-emerald-500' ?> rounded-lg px-4 py-2 text-sm font-medium text-white">
          <?= $t['is_active'] ? 'Suspender tenant' : 'Activar tenant' ?>
        </button>
      </form>
    </div>
  </div>
  <div class="card space-y-2 text-sm">
    <p><span class="text-slate-400">Usuarios</span> <strong class="float-right"><?= (int) $stats['users'] ?></strong></p>
    <p><span class="text-slate-400">Productos</span> <strong class="float-right"><?= (int) $stats['products'] ?></strong></p>
    <p><span class="text-slate-400">Ventas</span> <strong class="float-right"><?= (int) $stats['sales'] ?></strong></p>
    <p><span class="text-slate-400">Registrado</span> <strong class="float-right text-xs"><?= e($t['created_at']) ?></strong></p>
  </div>
</div>

<div class="card mt-6 overflow-x-auto">
  <h3 class="mb-4 font-semibold">Usuarios del negocio</h3>
  <table class="w-full text-sm">
    <thead class="text-slate-500"><tr><th class="pb-2">Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Último login</th></tr></thead>
    <tbody>
    <?php foreach ($detail['users'] as $u): ?>
    <tr class="border-t border-slate-800">
      <td class="py-2"><?= e($u['name']) ?></td>
      <td><?= e($u['email']) ?></td>
      <td><?= e($u['role_name']) ?></td>
      <td><?= $u['is_active'] ? 'Activo' : 'Inactivo' ?></td>
      <td class="text-slate-500"><?= e($u['last_login_at'] ?? '—') ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$detail['users']): ?><tr><td colspan="5" class="py-4 text-slate-500">Sin usuarios.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
