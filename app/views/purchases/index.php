<div class="mb-4 flex justify-end"><a href="<?= url('/purchases/create') ?>" class="btn-primary">+ Nueva compra</a></div>
<div class="card overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500"><tr><th class="pb-3">Ref</th><th>Proveedor</th><th>Total</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($purchases as $p): ?>
    <tr class="border-t border-slate-800">
      <td class="py-3">#<?= (int) $p['id'] ?></td>
      <td><?= e($p['supplier_name'] ?? '—') ?></td>
      <td><?= money($p['total']) ?></td>
      <td><span class="rounded-full px-2 py-0.5 text-xs <?= $p['status'] === 'pendiente' ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-700' ?>"><?= e($p['status']) ?></span></td>
      <td class="text-slate-500"><?= e($p['created_at']) ?></td>
      <td>
        <?php if ($p['status'] === 'pendiente'): ?>
        <form method="post" action="<?= url('/purchases/' . $p['id'] . '/receive') ?>" class="inline">
          <?= csrf_field() ?>
          <button class="text-emerald-400 hover:underline">Recibir</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
