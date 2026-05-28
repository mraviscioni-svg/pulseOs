<div class="mb-4 flex justify-end">
  <a href="<?= url('/suppliers/create') ?>" class="btn-primary">+ Proveedor</a>
</div>
<div class="card overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="text-slate-500"><tr><th class="pb-3">Nombre</th><th>Empresa</th><th>CUIT</th><th>Teléfono</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($suppliers as $s): ?>
    <tr class="border-t border-slate-800">
      <td class="py-3"><?= e($s['name']) ?></td>
      <td><?= e($s['company'] ?? '—') ?></td>
      <td><?= e($s['tax_id'] ?? '—') ?></td>
      <td><?= e($s['phone'] ?? '—') ?></td>
      <td><a href="<?= url('/suppliers/' . $s['id'] . '/edit') ?>" class="text-pulse-400">Editar</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
