<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
  <form method="get" class="flex gap-2">
    <input name="q" value="<?= e($q) ?>" placeholder="Buscar producto, SKU, código…" class="input-field w-64">
    <button class="rounded-lg border border-slate-700 px-4 py-2 hover:bg-slate-900">Buscar</button>
  </form>
  <a href="<?= url('/products/create') ?>" class="btn-primary">+ Nuevo producto</a>
</div>

<div class="card overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-left text-slate-500">
      <tr><th class="pb-3"></th><th>Producto</th><th>SKU</th><th>Código</th><th>Stock</th><th>Precio</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
    <tr class="border-t border-slate-800">
      <td class="py-3">
        <?php if (!empty($p['image_path'])): ?>
        <img src="<?= upload_url($p['image_path']) ?>" alt="" class="h-10 w-10 rounded object-cover">
        <?php else: ?><span class="inline-block h-10 w-10 rounded bg-slate-800"></span><?php endif; ?>
      </td>
      <td class="py-3 font-medium"><?= e($p['name']) ?></td>
      <td><?= e($p['sku'] ?? '—') ?></td>
      <td><?= e($p['barcode'] ?? '—') ?></td>
      <td class="<?= (float)$p['stock'] <= (float)$p['min_stock'] ? 'text-amber-400' : '' ?>"><?= e($p['stock']) ?></td>
      <td><?= money($p['price']) ?></td>
      <td class="text-right">
        <a href="<?= url('/products/' . $p['id'] . '/edit') ?>" class="text-pulse-400 hover:underline">Editar</a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
