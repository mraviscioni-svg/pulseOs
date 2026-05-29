<?php
/** @var array<string, mixed> $detail */
/** @var array<string, mixed>|null $ownerUser */
$t = $detail['tenant'];
$stats = $detail['stats'];
$pageEyebrow = 'Platform';
$pageTitle = $t['name'];
$pageDescription = 'Slug ' . ($t['slug'] ?? '') . ' · Rubro ' . str_replace('_', ' ', (string) $t['business_type']);
require __DIR__ . '/../../partials/crud_page_header.php';
?>

<p class="mb-5">
  <a href="<?= url('/admin/tenants') ?>" class="text-sm link-accent">← Volver al listado</a>
</p>

<?php
$context = 'bar';
$showManage = false;
require __DIR__ . '/_tenant_actions.php';
?>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-8">
  <div class="stat-card">
    <p class="stat-card-label">Usuarios</p>
    <p class="stat-card-value"><?= (int) $stats['users'] ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Productos</p>
    <p class="stat-card-value"><?= (int) $stats['products'] ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Ventas</p>
    <p class="stat-card-value"><?= (int) $stats['sales'] ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Alta</p>
    <p class="stat-card-value text-base"><?= e(substr((string) $t['created_at'], 0, 10)) ?></p>
  </div>
</div>

<?php if ($ownerUser): ?>
<div class="mb-6 max-w-3xl rounded-2xl border border-accent-200 bg-accent-50 p-4 text-sm">
  <p class="text-xs font-semibold uppercase tracking-wider text-accent-600">Acceso del comercio</p>
  <p class="mt-2 text-navy-900">
    Usuario <span class="font-mono font-medium"><?= e($ownerUser['username'] ?? '') ?></span>
    · enlace directo al login de este tenant.
  </p>
  <p class="mt-2 break-all text-xs text-slate-600"><?= e($tenantLoginUrl ?? '') ?></p>
</div>
<?php endif; ?>

<div class="grid gap-6 xl:grid-cols-3">
  <div class="form-card xl:col-span-2">
    <h3 class="mb-1 text-base font-semibold text-navy-900">Configuración del comercio</h3>
    <p class="mb-6 text-sm text-slate-500">Datos generales y módulos habilitados en <?= e(app_name()) ?>.</p>
    <form method="post" action="<?= url('/admin/tenants/' . $t['id']) ?>" class="space-y-6">
      <?= csrf_field() ?>
      <div class="grid gap-4 sm:grid-cols-2">
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
        unset($hint, $placeholder, $options);
        $name = 'email'; $label = 'Email'; $type = 'email'; $value = $t['email'] ?? ''; $required = true;
        require __DIR__ . '/../../partials/form_group.php';
        unset($hint, $placeholder, $options);
        $name = 'phone'; $label = 'Teléfono'; $type = 'input'; $value = $t['phone'] ?? '';
        require __DIR__ . '/../../partials/form_group.php';
        ?>
        <div class="sm:col-span-2">
          <?php
          unset($hint, $placeholder, $options);
          $name = 'address'; $label = 'Dirección'; $type = 'input'; $value = $t['address'] ?? '';
          require __DIR__ . '/../../partials/form_group.php';
          ?>
        </div>
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
      <div class="form-actions !border-t-0 !pt-0">
        <button type="submit" class="btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>

  <div class="space-y-4">
    <div class="form-card">
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Estado</p>
      <p class="mt-2">
        <?php if ($t['is_active']): ?>
        <span class="badge badge-success"><span class="badge-dot" aria-hidden="true"></span>Activo</span>
        <?php else: ?>
        <span class="badge badge-muted">Suspendido</span>
        <?php endif; ?>
      </p>
      <p class="mt-4 text-xs text-slate-500">ID interno: <?= (int) $t['id'] ?></p>
    </div>
    <?php if (!empty($tenantLoginUrl)): ?>
    <?php
    $href = $tenantLoginUrl;
    $title = 'Login del comercio';
    $description = 'Abre el acceso con usuario prellenado para «' . ($t['name'] ?? '') . '».';
    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>';
    require __DIR__ . '/../../partials/settings_nav_card.php';
    ?>
    <?php endif; ?>
  </div>
</div>

<div class="data-table-wrap mt-8">
  <div class="data-table-head">
    <h3>Usuarios del comercio</h3>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Último login</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($detail['users'] as $u): ?>
      <tr>
        <td class="font-mono text-sm text-accent-600"><?= e($u['username'] ?? '') ?></td>
        <td class="font-medium text-navy-900"><?= e($u['name']) ?></td>
        <td class="text-xs text-slate-500"><?= e($u['email']) ?></td>
        <td><?= e($u['role_name']) ?></td>
        <td><?php $active = (bool) $u['is_active']; require __DIR__ . '/../../partials/status_badge.php'; ?></td>
        <td class="text-xs text-slate-500"><?= e($u['last_login_at'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$detail['users']): ?>
      <tr><td colspan="6" class="py-8 text-center text-slate-500">Sin usuarios.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$tenant = $t;
require __DIR__ . '/_delete_modal.php';
?>
