<?php
$pageEyebrow = 'Catálogo';
$pageTitle = 'Marcas';
$pageDescription = 'Fabricantes o líneas para etiquetar productos.';
$createUrl = url('/brands/create');
$createLabel = '+ Nueva marca';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<p class="mb-4 text-sm">
  <a href="<?= e($categoriesUrl) ?>" class="link-accent">← Categorías</a>
  <span class="text-slate-400"> · Rubro <?= e($businessTypeLabel) ?></span>
</p>

<?php
$importUrl = url('/brands/import');
$resetUrl = url('/brands/suggestions/reset');
$dismissUrl = url('/brands/suggestions/dismiss');
$itemLabel = 'marca';
$suggestions = $suggestedBrands;
require __DIR__ . '/../partials/catalog_suggestions.php';
?>

<?php
$basePath = '/brands';
$searchPlaceholder = 'Buscar marca';
$showStatusFilter = false;
$totalCount = count($brands);
require __DIR__ . '/../partials/crud_toolbar.php';
?>

<div class="data-table-wrap">
  <div class="data-table-head">
    <h3>Listado</h3>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Productos</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($brands as $b): ?>
      <tr>
        <td class="font-medium text-navy-900"><?= e($b['name']) ?></td>
        <td class="text-slate-500"><?= (int) ($b['product_count'] ?? 0) ?></td>
        <td>
          <?php
          $editUrl = url('/brands/' . $b['id'] . '/edit');
          $deleteUrl = url('/brands/' . $b['id'] . '/delete');
          $deleteConfirm = '¿Eliminar la marca «' . $b['name'] . '»? Los productos quedarán sin marca.';
          require __DIR__ . '/../partials/row_actions.php';
          ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$brands): ?>
      <tr><td colspan="3"><?php $message = 'No hay marcas'; $actionUrl = url('/brands/create'); $actionLabel = '+ Crear marca'; require __DIR__ . '/../partials/empty_state.php'; ?></td></tr>
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
$closeUrl = $modal['closeUrl'] ?? url('/brands');
require __DIR__ . '/../partials/form_modal.php';
?>
<?php endif; ?>
