<?php
/** @var array<string, mixed>|null $product */
/** @var list<array<string, mixed>> $categories */
/** @var list<array<string, mixed>> $brands */
/** @var list<array<string, mixed>> $suppliers */
/** @var list<array<string, mixed>> $variants */
$isEdit = !empty($product);
$field = static function (string $key, mixed $default = '') use ($product): string {
    $v = old($key);
    if ($v !== '' && $v !== null) {
        return (string) $v;
    }
    if ($product !== null && array_key_exists($key, $product)) {
        return (string) $product[$key];
    }

    return (string) $default;
};
$isActive = old('is_active') !== '' && old('is_active') !== null
    ? !empty(old('is_active'))
    : (bool) ($product['is_active'] ?? true);
?>
<form method="post" enctype="multipart/form-data" action="<?= $isEdit ? url('/products/' . $product['id']) : url('/products') ?>">
  <?= csrf_field() ?>
  <div class="modal-form-grid">
    <div class="sm:col-span-2">
      <label class="label">Nombre</label>
      <input name="name" value="<?= e($field('name')) ?>" required class="input-field" placeholder="Ej: Agua 500ml">
    </div>
    <div>
      <label class="label">Código de barras</label>
      <input name="barcode" value="<?= e($field('barcode')) ?>" class="input-field" placeholder="7790…">
    </div>
    <div>
      <label class="label">Categoría</label>
      <select name="category_id" class="input-field">
        <option value="">— Sin categoría —</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= (int) $c['id'] ?>" <?= (int) $field('category_id', '0') === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="label">Precio compra</label>
      <input type="number" step="0.01" min="0" name="cost" value="<?= e($field('cost', '0')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Precio venta</label>
      <input type="number" step="0.01" min="0" name="price" value="<?= e($field('price')) ?>" required class="input-field">
    </div>
    <?php if (!$isEdit): ?>
    <div>
      <label class="label">Stock actual</label>
      <input type="number" step="0.001" min="0" name="stock" value="<?= e($field('stock', '0')) ?>" class="input-field">
    </div>
    <?php else: ?>
    <div>
      <label class="label">Stock actual</label>
      <input type="text" value="<?= e($product['stock'] ?? '0') ?>" class="input-field bg-slate-50 text-slate-500" readonly tabindex="-1">
    </div>
    <?php endif; ?>
    <div>
      <label class="label">Stock mínimo</label>
      <input type="number" step="0.001" min="0" name="min_stock" value="<?= e($field('min_stock', '0')) ?>" class="input-field">
    </div>
    <div class="sm:col-span-2">
      <label class="form-check">
        <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
        Producto activo
      </label>
    </div>
  </div>

  <details class="modal-advanced">
    <summary class="mb-3">Más opciones</summary>
    <div class="modal-form-grid">
      <div class="sm:col-span-2 flex gap-4 items-start">
        <?php if (!empty($product['image_path'])): ?>
        <img src="<?= upload_url($product['image_path']) ?>" alt="" class="h-16 w-16 rounded-lg object-cover ring-1 ring-slate-200">
        <?php endif; ?>
        <div class="flex-1">
          <label class="label">Imagen</label>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="input-field !py-2">
        </div>
      </div>
      <div>
        <label class="label">Marca</label>
        <select name="brand_id" class="input-field">
          <option value="">—</option>
          <?php foreach ($brands as $b): ?>
          <option value="<?= (int) $b['id'] ?>" <?= (int) $field('brand_id', '0') === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label">Proveedor</label>
        <select name="supplier_id" class="input-field">
          <option value="">—</option>
          <?php foreach ($suppliers as $s): ?>
          <option value="<?= (int) $s['id'] ?>" <?= (int) $field('supplier_id', '0') === (int) $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label">SKU</label>
        <input name="sku" value="<?= e($field('sku')) ?>" class="input-field">
      </div>
      <div>
        <label class="label">Código interno</label>
        <input name="internal_code" value="<?= e($field('internal_code')) ?>" class="input-field">
      </div>
      <div>
        <label class="label">Unidad</label>
        <input name="unit" value="<?= e($field('unit', 'unidad')) ?>" class="input-field">
      </div>
      <div>
        <label class="label">Precio mayorista</label>
        <input type="number" step="0.01" name="wholesale_price" value="<?= e($field('wholesale_price')) ?>" class="input-field">
      </div>
      <div class="sm:col-span-2">
        <label class="label">Descripción</label>
        <textarea name="description" rows="2" class="input-field"><?= e($field('description')) ?></textarea>
      </div>
      <div class="sm:col-span-2">
        <label class="form-check">
          <input type="checkbox" name="has_variants" value="1" <?= !empty($product['has_variants']) ? 'checked' : '' ?>>
          Usa variantes (talle, color, etc.)
        </label>
      </div>
    </div>
  </details>

  <div class="modal-form-footer">
    <a href="<?= url('/products') ?>" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
  </div>
</form>

<?php if ($isEdit): ?>
<form method="post" action="<?= url('/products/' . $product['id'] . '/stock') ?>" class="modal-advanced mt-4">
  <?= csrf_field() ?>
  <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Ajuste de stock</p>
  <div class="modal-form-grid">
    <div>
      <label class="label">Tipo</label>
      <select name="type" class="input-field">
        <option value="ingreso">Ingreso</option>
        <option value="ajuste">Ajuste (+/-)</option>
        <option value="devolucion">Devolución</option>
      </select>
    </div>
    <div>
      <label class="label">Cantidad</label>
      <input type="number" step="0.001" name="quantity" placeholder="+ o -" required class="input-field">
    </div>
    <div class="sm:col-span-2">
      <label class="label">Notas</label>
      <input name="notes" placeholder="Opcional" class="input-field">
    </div>
    <div class="sm:col-span-2">
      <button type="submit" class="btn-secondary">Aplicar ajuste</button>
    </div>
  </div>
</form>

<?php if (module_enabled('variants') || !empty($product['has_variants'])): ?>
<div class="modal-advanced mt-4">
  <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Variantes</p>
  <?php if ($variants): ?>
  <div class="mb-3 overflow-x-auto rounded-lg border border-slate-200">
    <table class="w-full text-xs">
      <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-3 py-2">Nombre</th><th class="px-3 py-2">Stock</th><th class="px-3 py-2">Precio</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($variants as $v): ?>
      <tr class="border-t border-slate-100">
        <td class="px-3 py-2"><?= e($v['name']) ?></td>
        <td class="px-3 py-2"><?= e($v['stock']) ?></td>
        <td class="px-3 py-2"><?= money($v['price']) ?></td>
        <td class="px-3 py-2 text-right">
          <form method="post" action="<?= url('/products/' . $product['id'] . '/variants/' . $v['id'] . '/delete') ?>" class="inline">
            <?= csrf_field() ?>
            <button class="text-rose-600 text-xs hover:underline">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
  <form method="post" action="<?= url('/products/' . $product['id'] . '/variants') ?>" class="modal-form-grid">
    <?= csrf_field() ?>
    <div class="sm:col-span-2">
      <input name="name" placeholder="Nombre variante *" required class="input-field">
    </div>
    <div><input name="talle" placeholder="Talle" class="input-field"></div>
    <div><input name="color" placeholder="Color" class="input-field"></div>
    <div><input type="number" step="0.01" name="price" placeholder="Precio" class="input-field"></div>
    <div><input type="number" step="0.001" name="stock" placeholder="Stock" value="0" class="input-field"></div>
    <div class="sm:col-span-2">
      <button type="submit" class="btn-secondary">Agregar variante</button>
    </div>
  </form>
</div>
<?php endif; ?>
<?php endif; ?>
