<div class="page-header">
  <div class="page-header-main">
    <p class="page-eyebrow">Comercio</p>
    <h2>Configuración</h2>
    <p>Datos de tu negocio y preferencias de operación.</p>
  </div>
</div>

<form method="post" action="<?= url('/settings') ?>" class="form-card max-w-2xl">
  <?= csrf_field() ?>
  <div class="form-section mb-6 space-y-4">
    <p class="form-section-title">Empresa</p>
    <?php
    $name = 'tenant_name'; $label = 'Nombre del negocio'; $type = 'input'; $value = $tenant['name'] ?? ''; $required = true;
    require __DIR__ . '/../partials/form_group.php';
    $name = 'tax_id'; $label = 'CUIT'; $type = 'input'; $value = $tenant['tax_id'] ?? ''; $placeholder = '20-12345678-9';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'phone'; $label = 'Teléfono'; $type = 'input'; $value = $tenant['phone'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'address'; $label = 'Dirección'; $type = 'textarea'; $value = $tenant['address'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    ?>
  </div>

  <div class="form-section mb-6 space-y-4">
    <p class="form-section-title">Operación</p>
    <div class="grid gap-4 sm:grid-cols-2">
      <?php
      $name = 'currency'; $label = 'Moneda'; $type = 'input'; $value = $settings['currency'] ?? 'ARS';
      require __DIR__ . '/../partials/form_group.php';
      $name = 'tax_rate'; $label = 'IVA %'; $type = 'number'; $value = $settings['tax_rate'] ?? '0';
      require __DIR__ . '/../partials/form_group.php';
      ?>
    </div>
    <?php
    $name = 'pos_receipt_footer'; $label = 'Pie de ticket POS'; $type = 'textarea'; $value = $settings['pos_receipt_footer'] ?? '';
    $placeholder = 'Gracias por su compra';
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-slate-50">
      <input type="checkbox" name="low_stock_alert" value="1" class="h-4 w-4 rounded border-slate-600" <?= ($settings['low_stock_alert'] ?? 1) ? 'checked' : '' ?>>
      <span class="text-sm">Alertas de stock bajo</span>
    </label>
    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-slate-50">
      <input type="checkbox" name="dark_mode" value="1" class="h-4 w-4 rounded border-slate-600" <?= ($settings['dark_mode'] ?? 1) ? 'checked' : '' ?>>
      <span class="text-sm">Modo oscuro</span>
    </label>
  </div>

  <p class="mb-4 text-sm text-slate-500">Los módulos del sistema (POS, compras, reportes, etc.) los habilita el administrador de PulseOS.</p>
  <div class="form-actions">
    <button type="submit" class="btn-primary">Guardar configuración</button>
  </div>
</form>
