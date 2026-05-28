<div class="mb-6">
  <h2 class="text-xl font-bold">Categorías y marcas</h2>
  <p class="mt-1 text-sm text-slate-400">
    Rubro <strong class="text-slate-300"><?= e($businessTypeLabel) ?></strong> — lo que agregues queda en el catálogo compartido para futuros comercios del mismo tipo.
  </p>
</div>

<div class="grid gap-6 lg:grid-cols-2">
  <div class="form-card">
    <h3 class="mb-1 text-lg font-semibold">Categorías</h3>
    <p class="mb-4 text-sm text-slate-500">Usalas al crear productos.</p>

    <?php if (!empty($suggestedCategories)): ?>
    <div class="mb-4 rounded-xl border border-indigo-500/20 bg-indigo-950/30 p-3">
      <p class="mb-2 text-xs font-medium uppercase tracking-wide text-indigo-300">Sugeridas del rubro</p>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($suggestedCategories as $s): ?>
        <form method="post" action="<?= url('/categories/import') ?>" class="inline">
          <?= csrf_field() ?>
          <input type="hidden" name="name" value="<?= e($s['name']) ?>">
          <button type="submit" class="chip-suggestion" title="Usada <?= (int) ($s['use_count'] ?? 0) ?> veces en el rubro">+ <?= e($s['name']) ?></button>
        </form>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!$categories): ?>
    <p class="mb-4 text-sm text-slate-500">Todavía no hay categorías en este comercio.</p>
    <?php else: ?>
    <ul class="mb-4 divide-y divide-slate-800 text-sm">
      <?php foreach ($categories as $c): ?>
      <li class="flex justify-between gap-2 py-2.5">
        <span><?= e($c['name']) ?></span>
        <form method="post" action="<?= url('/categories/' . $c['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
          <?= csrf_field() ?>
          <button type="submit" class="btn-ghost text-rose-400">Eliminar</button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <form method="post" action="<?= url('/categories') ?>" class="form-section !p-4">
      <?= csrf_field() ?>
      <?php
      $name = 'name';
      $label = 'Nueva categoría';
      $type = 'input';
      $value = '';
      $placeholder = 'Ej: Bebidas, Repuestos…';
      $required = true;
      require __DIR__ . '/../partials/form_group.php';
      ?>
      <button type="submit" class="btn-primary w-full sm:w-auto">Agregar categoría</button>
    </form>
  </div>

  <div class="form-card">
    <h3 class="mb-1 text-lg font-semibold">Marcas</h3>
    <p class="mb-4 text-sm text-slate-500">Opcional, para filtrar y etiquetar productos.</p>

    <?php if (!empty($suggestedBrands)): ?>
    <div class="mb-4 rounded-xl border border-indigo-500/20 bg-indigo-950/30 p-3">
      <p class="mb-2 text-xs font-medium uppercase tracking-wide text-indigo-300">Sugeridas del rubro</p>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($suggestedBrands as $s): ?>
        <form method="post" action="<?= url('/brands/import') ?>" class="inline">
          <?= csrf_field() ?>
          <input type="hidden" name="name" value="<?= e($s['name']) ?>">
          <button type="submit" class="chip-suggestion">+ <?= e($s['name']) ?></button>
        </form>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!$brands): ?>
    <p class="mb-4 text-sm text-slate-500">Todavía no hay marcas en este comercio.</p>
    <?php else: ?>
    <ul class="mb-4 divide-y divide-slate-800 text-sm">
      <?php foreach ($brands as $b): ?>
      <li class="flex justify-between gap-2 py-2.5">
        <span><?= e($b['name']) ?></span>
        <form method="post" action="<?= url('/brands/' . $b['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar esta marca?');">
          <?= csrf_field() ?>
          <button type="submit" class="btn-ghost text-rose-400">Eliminar</button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <form method="post" action="<?= url('/brands') ?>" class="form-section !p-4">
      <?= csrf_field() ?>
      <?php
      $name = 'name';
      $label = 'Nueva marca';
      $type = 'input';
      $value = '';
      $placeholder = 'Ej: Samsung, Arcor…';
      $required = true;
      require __DIR__ . '/../partials/form_group.php';
      ?>
      <button type="submit" class="btn-primary w-full sm:w-auto">Agregar marca</button>
    </form>
  </div>
</div>
