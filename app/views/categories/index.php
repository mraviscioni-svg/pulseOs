<div class="mb-6">
  <h2 class="text-xl font-bold">Categorías y marcas</h2>
  <p class="mt-1 text-sm text-slate-400">Organizá el catálogo para asignarlos a los productos.</p>
</div>

<div class="grid gap-6 lg:grid-cols-2">
  <div class="card">
    <h3 class="mb-4 font-semibold">Categorías</h3>
    <?php if (!$categories): ?>
    <p class="mb-4 text-sm text-slate-500">Todavía no hay categorías.</p>
    <?php else: ?>
    <ul class="mb-4 space-y-2 text-sm">
      <?php foreach ($categories as $c): ?>
      <li class="flex justify-between gap-2 border-b border-slate-800 py-2">
        <span><?= e($c['name']) ?></span>
        <form method="post" action="<?= url('/categories/' . $c['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
          <?= csrf_field() ?>
          <button type="submit" class="text-xs text-rose-400 hover:underline">Eliminar</button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <form method="post" action="<?= url('/categories') ?>" class="flex flex-col gap-2 sm:flex-row">
      <?= csrf_field() ?>
      <input name="name" required placeholder="Nueva categoría" class="input-field flex-1">
      <button type="submit" class="btn-primary shrink-0">Agregar</button>
    </form>
  </div>

  <div class="card">
    <h3 class="mb-4 font-semibold">Marcas</h3>
    <?php if (!$brands): ?>
    <p class="mb-4 text-sm text-slate-500">Todavía no hay marcas.</p>
    <?php else: ?>
    <ul class="mb-4 space-y-2 text-sm">
      <?php foreach ($brands as $b): ?>
      <li class="flex justify-between gap-2 border-b border-slate-800 py-2">
        <span><?= e($b['name']) ?></span>
        <form method="post" action="<?= url('/brands/' . $b['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar esta marca?');">
          <?= csrf_field() ?>
          <button type="submit" class="text-xs text-rose-400 hover:underline">Eliminar</button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <form method="post" action="<?= url('/brands') ?>" class="flex flex-col gap-2 sm:flex-row">
      <?= csrf_field() ?>
      <input name="name" required placeholder="Nueva marca" class="input-field flex-1">
      <button type="submit" class="btn-primary shrink-0">Agregar</button>
    </form>
  </div>
</div>
