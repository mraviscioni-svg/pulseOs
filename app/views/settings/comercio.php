<?php
$pageEyebrow = 'Configuración';
$pageTitle = 'Datos del comercio';
$pageDescription = 'Información pública y fiscal de tu negocio.';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<p class="mb-5">
  <a href="<?= url('/settings') ?>" class="text-sm link-accent">← Volver a configuración</a>
</p>

<form method="post" action="<?= url('/settings/comercio') ?>" class="form-card max-w-2xl">
  <?= csrf_field() ?>
  <div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
      <?php
      $name = 'tenant_name'; $label = 'Nombre del negocio'; $type = 'input'; $value = $tenant['name'] ?? ''; $required = true;
      require __DIR__ . '/../partials/form_group.php';
      ?>
    </div>
    <?php
    $name = 'tax_id'; $label = 'CUIT'; $type = 'input'; $value = $tenant['tax_id'] ?? ''; $placeholder = '20-12345678-9';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'phone'; $label = 'Teléfono'; $type = 'input'; $value = $tenant['phone'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <div class="sm:col-span-2">
      <?php
      $name = 'address'; $label = 'Dirección'; $type = 'textarea'; $value = $tenant['address'] ?? '';
      require __DIR__ . '/../partials/form_group.php';
      ?>
    </div>
    <div class="sm:col-span-2 rounded-xl border border-slate-200 bg-slate-50/80 px-4 py-3 text-xs text-slate-500">
      <span class="font-semibold uppercase tracking-wider text-slate-400">Slug técnico</span>
      <p class="mt-1 font-mono text-sm text-navy-900"><?= e($tenant['slug'] ?? '—') ?></p>
      <p class="mt-1">Identificador interno del comercio en <?= e(app_name()) ?> (solo lectura).</p>
    </div>
  </div>
  <div class="form-actions mt-6">
    <button type="submit" class="btn-primary">Guardar datos</button>
    <a href="<?= url('/settings') ?>" class="btn-secondary">Cancelar</a>
  </div>
</form>
