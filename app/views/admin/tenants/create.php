<div class="mb-4">
  <a href="<?= url('/admin/tenants') ?>" class="text-sm text-violet-400 hover:underline">← Volver al listado</a>
</div>

<div class="form-card max-w-2xl">
  <h2 class="mb-1 text-lg font-semibold">Alta de comercio</h2>
  <p class="mb-6 text-sm text-slate-400">Se precargan categorías y marcas del rubro elegido. El owner ingresa en el login de comercios.</p>

  <form method="post" action="<?= url('/admin/tenants') ?>" class="space-y-6">
    <?= csrf_field() ?>

    <div class="form-section space-y-4">
      <p class="form-section-title">Negocio y owner</p>
      <?php
      $name = 'company_name'; $label = 'Nombre del negocio'; $type = 'input'; $value = old('company_name'); $required = true;
      require __DIR__ . '/../../partials/form_group.php';
      $name = 'owner_name'; $label = 'Nombre del responsable'; $type = 'input'; $value = old('owner_name'); $required = true;
      require __DIR__ . '/../../partials/form_group.php';
      $name = 'username'; $label = 'Usuario de acceso'; $type = 'input'; $value = old('username'); $required = true;
      $placeholder = 'ej: taller.boedo';
      $hint = 'Único en todo ' . app_name() . '. Mínimo 3 caracteres.';
      require __DIR__ . '/../../partials/form_group.php';
      $name = 'business_type'; $label = 'Rubro'; $type = 'select'; $value = old('business_type', 'otro'); $required = true;
      $options = [];
      foreach ($businessTypes as $key => $typeRow) {
          $options[] = ['value' => $key, 'label' => $typeRow['label']];
      }
      require __DIR__ . '/../../partials/form_group.php';
      ?>
      <div class="grid gap-4 sm:grid-cols-2">
        <?php
        $name = 'email'; $label = 'Email de contacto'; $type = 'email'; $value = old('email'); $required = true; $hint = null;
        require __DIR__ . '/../../partials/form_group.php';
        $name = 'phone'; $label = 'Teléfono'; $type = 'input'; $value = old('phone');
        require __DIR__ . '/../../partials/form_group.php';
        ?>
      </div>
      <?php
      $name = 'password'; $label = 'Contraseña inicial del owner'; $type = 'password'; $value = ''; $required = true;
      $hint = 'Mínimo 8 caracteres.';
      require __DIR__ . '/../../partials/form_group.php';
      ?>
    </div>

    <div class="form-section">
      <p class="form-section-title mb-3">Módulos activos</p>
      <p class="form-hint mb-3">Por defecto según el rubro; podés ajustarlos antes de crear.</p>
      <?php
      $moduleLabels = $moduleLabels ?? config('platform_modules');
      $activeModules = $activeModules ?? [];
      require __DIR__ . '/../partials/module_checkboxes.php';
      ?>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-primary">Crear comercio</button>
      <a href="<?= url('/admin/tenants') ?>" class="btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
