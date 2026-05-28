<div class="grid gap-6 lg:grid-cols-2">
  <div class="card">
    <h3 class="mb-4 font-semibold">Categorías</h3>
    <ul class="mb-4 space-y-2 text-sm">
      <?php foreach ($categories as $c): ?>
      <li class="flex justify-between border-b border-slate-800 py-2">
        <span><?= e($c['name']) ?></span>
        <form method="post" action="<?= url('/categories/' . $c['id'] . '/delete') ?>" class="inline"><?= csrf_field() ?><button class="text-rose-400 text-xs">Eliminar</button></form>
      </li>
      <?php endforeach; ?>
    </ul>
    <form method="post" action="<?= url('/categories') ?>" class="flex gap-2">
      <?= csrf_field() ?>
      <input name="name" required placeholder="Nueva categoría" class="input-field">
      <button class="btn-primary">Agregar</button>
    </form>
  </div>
  <div class="card">
    <h3 class="mb-4 font-semibold">Marcas</h3>
    <ul class="mb-4 space-y-2 text-sm">
      <?php foreach ($brands as $b): ?>
      <li class="flex justify-between border-b border-slate-800 py-2">
        <span><?= e($b['name']) ?></span>
        <form method="post" action="<?= url('/brands/' . $b['id'] . '/delete') ?>" class="inline"><?= csrf_field() ?><button class="text-rose-400 text-xs">Eliminar</button></form>
      </li>
      <?php endforeach; ?>
    </ul>
    <form method="post" action="<?= url('/brands') ?>" class="flex gap-2">
      <?= csrf_field() ?>
      <input name="name" required placeholder="Nueva marca" class="input-field">
      <button class="btn-primary">Agregar</button>
    </form>
  </div>
</div>
