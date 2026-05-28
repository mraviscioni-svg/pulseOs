<?php
$pageEyebrow = 'Configuración';
$pageTitle = 'Operación';
$pageDescription = 'Preferencias de venta, moneda y alertas del día a día.';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<p class="mb-5">
  <a href="<?= url('/settings') ?>" class="text-sm link-accent">← Volver a configuración</a>
</p>

<form method="post" action="<?= url('/settings/operacion') ?>" class="form-card max-w-2xl">
  <?= csrf_field() ?>
  <div class="grid gap-4 sm:grid-cols-2">
    <?php
    $name = 'currency'; $label = 'Moneda'; $type = 'input'; $value = $settings['currency'] ?? 'ARS';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'tax_rate'; $label = 'IVA %'; $type = 'number'; $value = $settings['tax_rate'] ?? '0';
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <div class="sm:col-span-2">
      <?php
      $name = 'pos_receipt_footer'; $label = 'Pie de ticket POS'; $type = 'textarea'; $value = $settings['pos_receipt_footer'] ?? '';
      $placeholder = 'Gracias por su compra';
      require __DIR__ . '/../partials/form_group.php';
      ?>
    </div>
  </div>
  <div class="mt-4 space-y-3">
    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-slate-50">
      <input type="checkbox" name="low_stock_alert" value="1" class="h-4 w-4 rounded border-slate-300" <?= ($settings['low_stock_alert'] ?? 1) ? 'checked' : '' ?>>
      <span class="text-sm text-slate-700">Alertas de stock bajo en el dashboard</span>
    </label>
    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 hover:bg-slate-50">
      <input type="checkbox" name="dark_mode" value="1" class="h-4 w-4 rounded border-slate-300" <?= ($settings['dark_mode'] ?? 0) ? 'checked' : '' ?>>
      <span class="text-sm text-slate-700">Modo oscuro (experimental)</span>
    </label>
  </div>
  <div class="form-actions mt-6">
    <button type="submit" class="btn-primary">Guardar preferencias</button>
    <a href="<?= url('/settings') ?>" class="btn-secondary">Cancelar</a>
  </div>
</form>
