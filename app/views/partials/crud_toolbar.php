<?php
/**
 * @var string $basePath ej. /products
 * @var string $q
 * @var string|null $createUrl
 * @var string|null $createLabel
 * @var string|null $status current status filter
 * @var bool $showStatusFilter
 * @var int|null $totalCount
 */
$createLabel = $createLabel ?? 'Nuevo';
$showStatusFilter = $showStatusFilter ?? false;
$status = $status ?? 'all';
$queryExtra = $queryExtra ?? [];
?>
<div class="crud-toolbar">
  <form method="get" action="<?= url($basePath) ?>" class="crud-toolbar-search">
    <?php foreach ($queryExtra as $k => $v): if ($k === 'q' || $k === 'status' || $k === 'export') continue; ?>
    <input type="hidden" name="<?= e($k) ?>" value="<?= e((string) $v) ?>">
    <?php endforeach; ?>
    <div class="search-input-wrap">
      <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
      <input type="search" name="q" value="<?= e($q ?? '') ?>" placeholder="<?= e($searchPlaceholder ?? 'Buscar…') ?>" class="input-field">
    </div>
    <?php if ($showStatusFilter): ?>
    <select name="status" class="input-field w-auto min-w-[8rem]" onchange="this.form.submit()">
      <?php
      $statusOptions = $statusOptions ?? ['all' => 'Todos', 'active' => 'Activos', 'inactive' => 'Inactivos'];
      foreach ($statusOptions as $val => $label):
      ?>
      <option value="<?= e($val) ?>" <?= $status === $val ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button type="submit" class="btn-secondary">Buscar</button>
  </form>

  <div class="crud-toolbar-actions">
    <?php if (isset($totalCount)): ?>
    <span class="crud-count"><?= (int) $totalCount ?> registro<?= $totalCount === 1 ? '' : 's' ?></span>
    <?php endif; ?>
    <div class="export-group">
      <?php
      $exportQuery = array_merge(['q' => $q ?? ''], $showStatusFilter ? ['status' => $status] : [], $queryExtra);
      $exportQuery = array_filter($exportQuery, static fn ($v) => $v !== '' && $v !== null);
      $csvQs = http_build_query(array_merge($exportQuery, ['export' => 'csv']));
      $pdfQs = http_build_query(array_merge($exportQuery, ['export' => 'pdf']));
      ?>
      <a href="<?= url($basePath) ?>?<?= e($csvQs) ?>" class="btn-export" title="Excel (CSV)">Excel</a>
      <a href="<?= url($basePath) ?>?<?= e($pdfQs) ?>" class="btn-export" title="PDF" target="_blank" rel="noopener">PDF</a>
    </div>
    <?php if (!empty($createUrl)): ?>
    <a href="<?= e($createUrl) ?>" class="btn-primary"><?= e($createLabel) ?></a>
    <?php endif; ?>
  </div>
</div>
