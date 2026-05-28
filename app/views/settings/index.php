<form method="post" action="<?= url('/settings') ?>" class="card max-w-2xl space-y-4">
  <?= csrf_field() ?>
  <h3 class="font-semibold">Empresa</h3>
  <input name="tenant_name" value="<?= e($tenant['name'] ?? '') ?>" required class="input-field" placeholder="Nombre del negocio">
  <input name="tax_id" value="<?= e($tenant['tax_id'] ?? '') ?>" class="input-field" placeholder="CUIT">
  <input name="phone" value="<?= e($tenant['phone'] ?? '') ?>" class="input-field" placeholder="Teléfono">
  <textarea name="address" class="input-field" placeholder="Dirección"><?= e($tenant['address'] ?? '') ?></textarea>

  <h3 class="font-semibold pt-2">Operación</h3>
  <div class="grid gap-4 sm:grid-cols-2">
    <div><label class="label">Moneda</label><input name="currency" value="<?= e($settings['currency'] ?? 'ARS') ?>" class="input-field"></div>
    <div><label class="label">IVA %</label><input type="number" step="0.01" name="tax_rate" value="<?= e($settings['tax_rate'] ?? '0') ?>" class="input-field"></div>
  </div>
  <textarea name="pos_receipt_footer" class="input-field" placeholder="Pie de ticket POS"><?= e($settings['pos_receipt_footer'] ?? '') ?></textarea>
  <label class="inline-flex gap-2"><input type="checkbox" name="low_stock_alert" value="1" <?= ($settings['low_stock_alert'] ?? 1) ? 'checked' : '' ?>> Alertas stock bajo</label>
  <label class="inline-flex gap-2"><input type="checkbox" name="dark_mode" value="1" <?= ($settings['dark_mode'] ?? 1) ? 'checked' : '' ?>> Modo oscuro</label>

  <h3 class="font-semibold pt-2">Módulos activos</h3>
  <p class="text-sm text-slate-400">Según tu rubro podés activar o desactivar funciones.</p>
  <div class="grid gap-2 sm:grid-cols-2">
    <?php
    $moduleLabels = [
        'products' => 'Productos', 'stock' => 'Stock', 'pos' => 'POS', 'suppliers' => 'Proveedores',
        'purchases' => 'Compras', 'cash' => 'Caja', 'variants' => 'Variantes',
        'work_orders' => 'Órdenes trabajo', 'bar' => 'Barras', 'entries' => 'Entradas', 'memberships' => 'Membresías',
    ];
    foreach ($moduleLabels as $key => $label):
    ?>
    <label class="inline-flex gap-2 rounded-lg border border-slate-800 px-3 py-2">
      <input type="checkbox" name="modules[]" value="<?= e($key) ?>" <?= in_array($key, $activeModules, true) ? 'checked' : '' ?>>
      <?= e($label) ?>
    </label>
    <?php endforeach; ?>
  </div>
  <button class="btn-primary">Guardar configuración</button>
</form>
