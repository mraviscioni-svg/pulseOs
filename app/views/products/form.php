<?php $isEdit = !empty($product); ?>
<form method="post" enctype="multipart/form-data" action="<?= $isEdit ? url('/products/' . $product['id']) : url('/products') ?>" class="card max-w-3xl space-y-4">
  <?= csrf_field() ?>
  <div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2 flex gap-4 items-start">
      <?php if (!empty($product['image_path'])): ?>
      <img src="<?= upload_url($product['image_path']) ?>" alt="" class="h-20 w-20 rounded-lg object-cover border border-slate-700">
      <?php endif; ?>
      <div class="flex-1">
        <label class="label">Imagen</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="input-field">
      </div>
    </div>
    <div class="sm:col-span-2">
      <label class="label">Nombre *</label>
      <input name="name" value="<?= e($product['name'] ?? old('name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">Categoría</label>
      <select name="category_id" class="input-field">
        <option value="">—</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= (int)($product['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="label">Marca</label>
      <select name="brand_id" class="input-field">
        <option value="">—</option>
        <?php foreach ($brands as $b): ?>
        <option value="<?= (int)$b['id'] ?>" <?= (int)($product['brand_id'] ?? 0) === (int)$b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="label">Proveedor</label>
      <select name="supplier_id" class="input-field">
        <option value="">—</option>
        <?php foreach ($suppliers as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= (int)($product['supplier_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
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
    <div class="flex flex-wrap gap-4">
      <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>> Activo</label>
      <label class="inline-flex items-center gap-2"><input type="checkbox" name="has_variants" value="1" <?= ($product['has_variants'] ?? 0) ? 'checked' : '' ?>> Usa variantes</label>
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

<?php if (module_enabled('variants') || module_enabled('ropa') || !empty($product['has_variants'])): ?>
<div class="card mt-6">
  <h3 class="mb-4 font-semibold">Variantes (talle, color, etc.)</h3>
  <table class="mb-4 w-full text-sm">
    <thead class="text-slate-500"><tr><th>Nombre</th><th>SKU</th><th>Barras</th><th>Stock</th><th>Precio</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($variants as $v): ?>
    <tr class="border-t border-slate-800">
      <td class="py-2"><?= e($v['name']) ?></td>
      <td><?= e($v['sku'] ?? '—') ?></td>
      <td><?= e($v['barcode'] ?? '—') ?></td>
      <td><?= e($v['stock']) ?></td>
      <td><?= money($v['price']) ?></td>
      <td>
        <form method="post" action="<?= url('/products/' . $product['id'] . '/variants/' . $v['id'] . '/delete') ?>" class="inline">
          <?= csrf_field() ?>
          <button class="text-rose-400 text-xs">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <form method="post" action="<?= url('/products/' . $product['id'] . '/variants') ?>" class="grid gap-3 sm:grid-cols-3">
    <?= csrf_field() ?>
    <input name="name" placeholder="Nombre variante *" required class="input-field">
    <input name="sku" placeholder="SKU" class="input-field">
    <input name="barcode" placeholder="Código barras" class="input-field">
    <input name="talle" placeholder="Talle" class="input-field">
    <input name="color" placeholder="Color" class="input-field">
    <input name="modelo" placeholder="Modelo" class="input-field">
    <input type="number" step="0.01" name="price" placeholder="Precio" class="input-field">
    <input type="number" step="0.001" name="stock" placeholder="Stock" value="0" class="input-field">
    <button class="btn-primary sm:col-span-3">Agregar variante</button>
  </form>
</div>
<?php endif; ?>
<?php endif; ?>
