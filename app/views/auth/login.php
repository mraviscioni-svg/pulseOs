<form method="post" action="<?= url('/login') ?>" class="form-card">
  <?= csrf_field() ?>
  <?php if (!empty($tenantSlug)): ?>
  <input type="hidden" name="tenant" value="<?= e($tenantSlug) ?>">
  <?php endif; ?>

  <?php if (!empty($loginTenant)): ?>
  <div class="login-tenant-badge">
    Acceso a <strong><?= e($loginTenant['name']) ?></strong>
    <?php if (empty($loginTenant['is_active'])): ?>
    <span class="mt-1 block text-rose-700">Este comercio está suspendido.</span>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <p class="page-eyebrow">Acceso</p>
  <h2 class="mt-1 text-xl font-bold text-navy-900">
    <?= !empty($loginTenant) ? 'Ingresá a tu comercio' : 'Ingresá a ' . app_name() ?>
  </h2>
  <p class="mb-6 mt-1 text-sm text-slate-500">
    <?= !empty($loginTenant)
        ? 'Usuario y contraseña asignados a «' . e($loginTenant['name']) . '».'
        : 'Usuario y contraseña que te dio el administrador.' ?>
  </p>

  <div class="space-y-4">
    <?php
    $name = 'username'; $label = 'Usuario'; $type = 'input';
    $value = $prefillUsername ?? old('username');
    $required = true; $placeholder = 'ej: taller.boedo';
    $hint = 'Mínimo 3 caracteres: letras, números, punto o guión.';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'password'; $label = 'Contraseña'; $type = 'password'; $value = '';
    $required = true; $hint = null; $placeholder = '••••••••';
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <button type="submit" class="btn-primary w-full">Ingresar</button>
  </div>

  <p class="mt-5 text-center text-sm text-slate-500">
    <a href="<?= url('/forgot-password') ?>" class="link-accent">¿Olvidaste tu contraseña?</a>
  </p>
  <p class="mt-4 text-center text-xs text-slate-400">
    El alta de comercios la gestiona el administrador de <?= e(app_name()) ?>.
  </p>
</form>
