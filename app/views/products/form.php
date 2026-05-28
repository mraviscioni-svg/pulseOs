<?php $isEdit = !empty($product); ?>
<form method="post" action="<?= $isEdit ? url('/products/' . $product['id']) : url('/products') ?>" class="card max-w-2xl space-y-4">
  <?= csrf_field() ?>
  <div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
      <label class="label">Nombre *</label>
      <input name="name" value="<?= e($product['name'] ?? old('name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">SKU</label>
      <input name="sku" value="<?= e($product['sku'] ?? old('sku')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Código de barras</label>
      <input name="barcode" value="<?= e($product['barcode'] ?? old('barcode')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Código interno</label>
      <input name="internal_code" value="<?= e($product['internal_code'] ?? old('internal_code')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Unidad</label>
      <input name="unit" value="<?= e($product['unit'] ?? old('unit', 'unidad')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Costo</label>
      <input type="number" step="0.01" name="cost" value="<?= e($product['cost'] ?? old('cost', '0')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Precio *</label>
      <input type="number" step="0.01" name="price" value="<?= e($product['price'] ?? old('price')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">Precio mayorista</label>
      <input type="number" step="0.01" name="wholesale_price" value="<?= e($product['wholesale_price'] ?? old('wholesale_price')) ?>" class="input-field">
    </div>
    <?php if (!$isEdit): ?>
    <div>
      <label class="label">Stock inicial</label>
      <input type="number" step="0.001" name="stock" value="<?= e(old('stock', '0')) ?>" class="input-field">
    </div>
    <?php endif; ?>
    <div>
      <label class="label">Stock mínimo</label>
      <input type="number" step="0.001" name="min_stock" value="<?= e($product['min_stock'] ?? old('min_stock', '0')) ?>" class="input-field">
    </div>
    <div class="sm:col-span-2">
      <label class="label">Descripción</label>
      <textarea name="description" rows="3" class="input-field"><?= e($product['description'] ?? old('description')) ?></textarea>
    </div>
    <div>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
        <span class="text-sm">Activo</span>
      </label>
    </div>
  </div>
  <div class="flex gap-3 pt-2">
    <button type="submit" class="btn-primary">Guardar</button>
    <a href="<?= url('/products') ?>" class="rounded-lg border border-slate-700 px-4 py-2">Cancelar</a>
  </div>
</form>

<?php if ($isEdit): ?>
<form method="post" action="<?= url('/products/' . $product['id'] . '/stock') ?>" class="card mt-6 max-w-md space-y-3">
  <?= csrf_field() ?>
  <h3 class="font-semibold">Ajuste de stock</h3>
  <select name="type" class="input-field">
    <option value="ingreso">Ingreso</option>
    <option value="ajuste">Ajuste (+/-)</option>
    <option value="devolucion">Devolución</option>
  </select>
  <input type="number" step="0.001" name="quantity" placeholder="Cantidad (+ o -)" required class="input-field">
  <input name="notes" placeholder="Notas" class="input-field">
  <button class="btn-primary">Aplicar</button>
</form>
<?php endif; ?>
