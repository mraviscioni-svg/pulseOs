<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
  <form method="get" class="flex gap-2">
    <input name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, email o slug…" class="input-field w-72">
    <button class="rounded-lg border border-slate-700 px-4 py-2 hover:bg-slate-900">Buscar</button>
  </form>
  <p class="text-sm text-slate-400"><?= count($tenants) ?> negocios registrados</p>
</div>

<div class="card overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-left text-slate-500">
      <tr>
        <th class="pb-3">Negocio</th>
        <th>Rubro</th>
        <th>Usuarios</th>
        <th>Productos</th>
        <th>Ventas tot.</th>
        <th>Estado</th>
        <th>Alta</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($tenants as $t): ?>
    <tr class="border-t border-slate-800">
      <td class="py-3">
        <p class="font-medium"><?= e($t['name']) ?></p>
        <p class="text-xs text-slate-500"><?= e($t['slug']) ?> · <?= e($t['email'] ?? '—') ?></p>
      </td>
      <td class="capitalize"><?= e(str_replace('_', ' ', $t['business_type'])) ?></td>
      <td><?= (int) $t['users_count'] ?></td>
      <td><?= (int) $t['products_count'] ?></td>
      <td><?= money($t['sales_total']) ?></td>
      <td>
        <?php if ($t['is_active']): ?>
        <span class="rounded-full bg-emerald-500/20 px-2 py-0.5 text-xs text-emerald-300">Activo</span>
        <?php else: ?>
        <span class="rounded-full bg-rose-500/20 px-2 py-0.5 text-xs text-rose-300">Suspendido</span>
        <?php endif; ?>
      </td>
      <td class="text-slate-500 text-xs"><?= e($t['created_at']) ?></td>
      <td><a href="<?= url('/admin/tenants/' . $t['id']) ?>" class="text-violet-400 hover:underline">Ver</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$tenants): ?>
    <tr><td colspan="8" class="py-8 text-center text-slate-500">No hay tenants<?= $q ? ' para esa búsqueda' : '' ?>.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
