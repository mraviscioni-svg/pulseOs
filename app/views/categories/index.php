<?php
$pageEyebrow = 'Catálogo';
$pageTitle = 'Categorías';
$pageDescription = 'Clasificá productos por familia o tipo.';
$createUrl = url('/categories/create');
$createLabel = '+ Nueva categoría';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<p class="mb-4 text-sm">
  <a href="<?= e($brandsUrl) ?>" class="link-accent">Marcas →</a>
  <span class="text-slate-400"> · Rubro <?= e($businessTypeLabel) ?></span>
</p>

<?php
$importUrl = url('/categories/import');
$resetUrl = url('/categories/suggestions/reset');
$dismissUrl = url('/categories/suggestions/dismiss');
$itemLabel = 'categoría';
$suggestions = $suggestedCategories;
require __DIR__ . '/../partials/catalog_suggestions.php';
?>

<?php
$basePath = '/categories';
$searchPlaceholder = 'Buscar categoría';
$showStatusFilter = false;
$totalCount = count($categories);
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
          <th>Descripción</th>
          <th>Productos</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($categories as $c): ?>
      <tr>
        <td class="font-medium text-navy-900"><?= e($c['name']) ?></td>
        <td class="text-slate-600"><?= e($c['description'] ?? '—') ?></td>
        <td class="text-slate-500"><?= (int) ($c['product_count'] ?? 0) ?></td>
        <td>
          <?php
          $editUrl = url('/categories/' . $c['id'] . '/edit');
          $deleteUrl = url('/categories/' . $c['id'] . '/delete');
          $deleteConfirm = '¿Eliminar la categoría «' . $c['name'] . '»? Los productos quedarán sin categoría.';
          require __DIR__ . '/../partials/row_actions.php';
          ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$categories): ?>
      <tr><td colspan="4"><?php $message = 'No hay categorías'; $actionUrl = url('/categories/create'); $actionLabel = '+ Crear categoría'; require __DIR__ . '/../partials/empty_state.php'; ?></td></tr>
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
$closeUrl = $modal['closeUrl'] ?? url('/categories');
require __DIR__ . '/../partials/form_modal.php';
?>
<?php endif; ?>
