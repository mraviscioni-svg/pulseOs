<?php
/**
 * @var list<array<string, mixed>> $suggestions
 * @var string $importUrl POST import one suggestion
 * @var string $resetUrl POST reset catalog defaults
 * @var string $dismissUrl POST hide panel
 * @var bool $suggestionsHidden
 * @var string $businessTypeLabel
 * @var string $itemLabel singular: categoría|marca
 */
if ($suggestionsHidden): ?>
<div class="catalog-suggestions-muted form-card !p-4">
  <p class="text-sm text-slate-600">Sugerencias del rubro ocultas.</p>
  <div class="mt-3 flex flex-wrap gap-2">
    <form method="post" action="<?= e($resetUrl) ?>" class="inline">
      <?= csrf_field() ?>
      <button type="submit" class="btn-secondary !py-2 text-xs">Restaurar sugerencias</button>
    </form>
  </div>
</div>
<?php return; endif; ?>

<?php if (empty($suggestions)): ?>
<div class="catalog-suggestions-muted form-card !p-4">
  <p class="text-sm text-slate-600">No hay sugerencias pendientes para el rubro <strong><?= e($businessTypeLabel) ?></strong>.</p>
  <form method="post" action="<?= e($resetUrl) ?>" class="mt-3 inline">
    <?= csrf_field() ?>
    <button type="submit" class="btn-secondary !py-2 text-xs">Restaurar sugerencias del rubro</button>
  </form>
</div>
<?php return; endif; ?>

<div class="catalog-suggestions form-card !p-4">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <p class="text-xs font-semibold uppercase tracking-wider text-accent-600">Sugerencias del rubro</p>
      <p class="mt-1 text-sm text-slate-600"><?= e($businessTypeLabel) ?> — clic para agregar la <?= e($itemLabel) ?> a tu comercio.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <form method="post" action="<?= e($resetUrl) ?>" class="inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn-secondary !py-1.5 !text-xs" title="Vuelve a cargar las sugerencias por defecto del rubro">Restaurar</button>
      </form>
      <form method="post" action="<?= e($dismissUrl) ?>" class="inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn-ghost !py-1.5 !text-xs">Ocultar</button>
      </form>
    </div>
  </div>
  <div class="mt-3 flex flex-wrap gap-2">
    <?php foreach ($suggestions as $s): ?>
    <form method="post" action="<?= e($importUrl) ?>" class="inline">
      <?= csrf_field() ?>
      <input type="hidden" name="name" value="<?= e($s['name']) ?>">
      <button type="submit" class="chip-suggestion" title="<?= isset($s['use_count']) ? 'Usada ' . (int) $s['use_count'] . ' veces en el rubro' : '' ?>">
        + <?= e($s['name']) ?>
      </button>
    </form>
    <?php endforeach; ?>
  </div>
</div>
