<?php
$pageEyebrow = 'Catálogo';
$pageTitle = 'Productos';
$pageDescription = 'Búsqueda, alta y edición. El código de barras es único por tenant.';
$createUrl = url('/products/create');
$createLabel = '+ Nuevo producto';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<?php
$basePath = '/products';
$searchPlaceholder = 'Buscar por nombre o código de barras';
$showStatusFilter = true;
$totalCount = count($products);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<div class="data-table-wrap">
  <div class="data-table-head">
    <h3>Listado</h3>
    <?php if (isset($totalCount)): ?>
    <span class="text-xs text-slate-500"><?= (int) $totalCount ?> producto<?= $totalCount === 1 ? '' : 's' ?></span>
    <?php endif; ?>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Producto</th>
          <th>Barcode</th>
          <th>Categoría</th>
          <th>Compra</th>
          <th>Venta</th>
          <th>Stock</th>
          <th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($products as $p): ?>
      <?php $stockLow = (float) $p['stock'] <= (float) $p['min_stock'] && (float) $p['min_stock'] > 0; ?>
      <tr class="<?= !$p['is_active'] ? 'opacity-60' : '' ?>">
        <td class="font-medium text-navy-900"><?= e($p['name']) ?></td>
        <td class="font-mono text-xs text-slate-500"><?= e($p['barcode'] ?? '—') ?></td>
        <td class="text-slate-600"><?= e($p['category_name'] ?? '—') ?></td>
        <td class="tabular-nums text-slate-600"><?= money($p['cost'] ?? 0) ?></td>
        <td class="tabular-nums font-medium text-navy-900"><?= money($p['price']) ?></td>
        <td>
          <span class="stock-cell <?= $stockLow ? 'stock-cell-low' : '' ?>"><?= e($p['stock']) ?></span>
          <span class="stock-cell-min"> / min <?= e($p['min_stock']) ?></span>
        </td>
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
</div>

<?php if (!empty($modal)): ?>
<?php
ob_start();
require __DIR__ . '/_form_modal.php';
$modalContent = ob_get_clean();
$modalTitle = $modal['title'];
$modalSubtitle = $modal['subtitle'] ?? null;
$closeUrl = $modal['closeUrl'] ?? url('/products');
$modalWide = true;
require __DIR__ . '/../partials/form_modal.php';
?>
<?php endif; ?>
