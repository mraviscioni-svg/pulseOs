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
        <?php
        $ownerUser = null;
        foreach ($detail['users'] as $u) {
            $role = strtolower((string) ($u['role_name'] ?? ''));
            if (str_contains($role, 'owner') || str_contains($role, 'dueño') || str_contains($role, 'propietario')) {
                $ownerUser = $u;
                break;
            }
        }
        if (!$ownerUser && !empty($detail['users'][0])) {
            $ownerUser = $detail['users'][0];
        }
        if ($ownerUser): ?>
        <p class="mt-1 text-sm text-slate-500">Login comercio: <span class="font-mono text-violet-300"><?= e($ownerUser['username'] ?? '') ?></span></p>
        <?php endif; ?>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php if ($t['is_active'] && !empty($ownerUserId)): ?>
        <form method="post" action="<?= url('/admin/tenants/' . $t['id'] . '/enter') ?>">
          <?= csrf_field() ?>
          <button type="submit" class="btn-primary">Abrir panel del comercio</button>
        </form>
        <?php endif; ?>
        <a href="<?= e($tenantLoginUrl ?? url('/login')) ?>" target="_blank" rel="noopener" class="btn-secondary">Login comercios ↗</a>
        <form method="post" action="<?= url('/admin/tenants/' . $t['id'] . '/toggle') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="is_active" value="<?= $t['is_active'] ? '0' : '1' ?>">
          <button class="<?= $t['is_active'] ? 'bg-rose-600 hover:bg-rose-500' : 'bg-emerald-600 hover:bg-emerald-500' ?> rounded-xl px-4 py-2.5 text-sm font-medium text-white">
            <?= $t['is_active'] ? 'Suspender' : 'Activar' ?>
          </button>
        </form>
      </div>
    </div>
  </div>
  <div class="card space-y-2 text-sm">
    <p><span class="text-slate-400">Usuarios</span> <strong class="float-right"><?= (int) $stats['users'] ?></strong></p>
    <p><span class="text-slate-400">Productos</span> <strong class="float-right"><?= (int) $stats['products'] ?></strong></p>
    <p><span class="text-slate-400">Ventas</span> <strong class="float-right"><?= (int) $stats['sales'] ?></strong></p>
    <p><span class="text-slate-400">Alta</span> <strong class="float-right text-xs"><?= e($t['created_at']) ?></strong></p>
  </div>
</div>

<div class="form-card mt-6 max-w-2xl">
  <h3 class="mb-1 text-lg font-semibold">Configuración del comercio</h3>
  <p class="mb-6 text-sm text-slate-400">Datos generales y permisos de módulos.</p>
  <form method="post" action="<?= url('/admin/tenants/' . $t['id']) ?>" class="space-y-6">
    <?= csrf_field() ?>
    <div class="form-section space-y-4">
      <p class="form-section-title">Datos del negocio</p>
      <?php
      unset($hint, $placeholder, $options);
      $name = 'name'; $label = 'Nombre'; $type = 'input'; $value = $t['name']; $required = true;
      require __DIR__ . '/../../partials/form_group.php';
      unset($hint, $placeholder, $options);
      $name = 'business_type'; $label = 'Rubro'; $type = 'select'; $value = $t['business_type']; $required = true;
      $options = [];
      foreach ($businessTypes as $key => $typeRow) {
          $options[] = ['value' => $key, 'label' => $typeRow['label']];
      }
      require __DIR__ . '/../../partials/form_group.php';
      ?>
      <div class="grid gap-4 sm:grid-cols-2">
        <?php
        unset($hint, $placeholder, $options);
        $name = 'email'; $label = 'Email'; $type = 'email'; $value = $t['email'] ?? ''; $required = true;
        require __DIR__ . '/../../partials/form_group.php';
        unset($hint, $placeholder, $options);
        $name = 'phone'; $label = 'Teléfono'; $type = 'input'; $value = $t['phone'] ?? '';
        require __DIR__ . '/../../partials/form_group.php';
        ?>
      </div>
      <?php
      unset($hint, $placeholder, $options);
      $name = 'address'; $label = 'Dirección'; $type = 'input'; $value = $t['address'] ?? '';
      require __DIR__ . '/../../partials/form_group.php';
      ?>
    </div>
    <div class="form-section">
      <p class="form-section-title mb-3">Módulos activos</p>
      <p class="form-hint mb-3">Solo el administrador de plataforma habilita qué ve este comercio en su menú.</p>
      <?php
      $moduleLabels = $moduleLabels ?? config('platform_modules');
      $activeModules = $activeModules ?? [];
      require __DIR__ . '/../partials/module_checkboxes.php';
      ?>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn-primary">Guardar cambios</button>
    </div>
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
