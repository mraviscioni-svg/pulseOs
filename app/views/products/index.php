<div class="page-header">
  <h2>Productos</h2>
  <p>Catálogo, precios y stock de tu comercio.</p>
</div>

<?php
$basePath = '/products';
$searchPlaceholder = 'Nombre, SKU, código de barras…';
$createUrl = url('/products/create');
$createLabel = '+ Nuevo producto';
$showStatusFilter = true;
$totalCount = count($products);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<div class="data-table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <th class="w-12"></th>
        <th>Producto</th>
        <th>SKU</th>
        <th>Código</th>
        <th>Stock</th>
        <th>Precio</th>
        <th>Estado</th>
        <th class="text-right">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
    <tr class="<?= !$p['is_active'] ? 'opacity-60' : '' ?>">
      <td>
        <?php if (!empty($p['image_path'])): ?>
        <img src="<?= upload_url($p['image_path']) ?>" alt="" class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-700">
        <?php else: ?>
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-800 text-xs text-slate-500">—</span>
        <?php endif; ?>
      </td>
      <td>
        <p class="font-medium text-white"><?= e($p['name']) ?></p>
        <?php if (!empty($p['category_name'])): ?>
        <p class="text-xs text-slate-500"><?= e($p['category_name']) ?></p>
        <?php endif; ?>
      </td>
      <td class="font-mono text-xs text-slate-400"><?= e($p['sku'] ?? '—') ?></td>
      <td class="font-mono text-xs text-slate-400"><?= e($p['barcode'] ?? '—') ?></td>
      <td class="<?= (float)$p['stock'] <= (float)$p['min_stock'] ? 'font-medium text-amber-400' : '' ?>"><?= e($p['stock']) ?></td>
      <td class="font-medium"><?= money($p['price']) ?></td>
      <td><?php $active = (bool) $p['is_active']; require __DIR__ . '/../partials/status_badge.php'; ?></td>
      <td>
        <?php
        $editUrl = url('/products/' . $p['id'] . '/edit');
        $toggleUrl = url('/products/' . $p['id'] . '/toggle');
        $isActive = (bool) $p['is_active'];
        $deleteUrl = url('/products/' . $p['id'] . '/delete');
        $deleteConfirm = '¿Eliminar el producto «' . $p['name'] . '»?';
        require __DIR__ . '/../partials/row_actions.php';
        ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$products): ?>
    <tr><td colspan="8"><?php $message = 'No hay productos' . ($q ? ' para esa búsqueda' : ''); $actionUrl = url('/products/create'); $actionLabel = '+ Crear producto'; require __DIR__ . '/../partials/empty_state.php'; ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
