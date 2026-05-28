<div class="grid gap-6 lg:grid-cols-2">
  <div class="card">
    <h3 class="mb-4 font-semibold">Estado actual</h3>
    <?php if ($openCash): ?>
    <p class="text-emerald-400">Caja abierta desde <?= e($openCash['opened_at']) ?></p>
    <p class="mt-2">Monto apertura: <?= money($openCash['opening_amount']) ?></p>
    <form method="post" action="<?= url('/cash/close') ?>" class="mt-4 space-y-3">
      <?= csrf_field() ?>
      <input type="number" step="0.01" name="closing_amount" placeholder="Monto en caja al cerrar" required class="input-field">
      <textarea name="notes" placeholder="Notas de cierre" class="input-field"></textarea>
      <button class="rounded-lg bg-rose-600 px-4 py-2 hover:bg-rose-700">Cerrar caja</button>
    </form>
      <form method="post" action="<?= url('/cash/movement') ?>" class="mt-6 space-y-3 border-t border-slate-800 pt-4">
      <?= csrf_field() ?>
      <h4 class="font-medium">Movimiento manual</h4>
      <select name="type" class="input-field">
        <option value="ingreso">Ingreso</option>
        <option value="egreso">Egreso</option>
        <option value="retiro">Retiro</option>
      </select>
      <input type="number" step="0.01" name="amount" required class="input-field" placeholder="Monto">
      <input name="description" class="input-field" placeholder="Descripción">
      <button class="btn-primary">Registrar</button>
    </form>
    <?php else: ?>
    <form method="post" action="<?= url('/cash/open') ?>" class="space-y-3">
      <?= csrf_field() ?>
      <input type="number" step="0.01" name="opening_amount" value="0" class="input-field" placeholder="Monto inicial">
      <button class="btn-primary">Abrir caja</button>
    </form>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3 class="mb-4 font-semibold">Movimientos (caja actual)</h3>
    <ul class="space-y-2 text-sm">
      <?php foreach ($movements as $m): ?>
      <li class="flex justify-between border-b border-slate-800 py-2">
        <span class="capitalize"><?= e($m['type']) ?> — <?= e($m['description'] ?? '') ?></span>
        <span><?= money($m['amount']) ?></span>
      </li>
      <?php endforeach; ?>
      <?php if (!$movements): ?><li class="text-slate-500">Sin movimientos</li><?php endif; ?>
    </ul>
  </div>
</div>
<div class="card mt-6">
  <h3 class="mb-4 font-semibold">Historial</h3>
  <table class="w-full text-sm">
    <thead class="text-slate-500"><tr><th>Apertura</th><th>Cierre</th><th>Estado</th><th>Diferencia</th></tr></thead>
    <tbody>
    <?php foreach ($history as $h): ?>
    <tr class="border-t border-slate-800">
      <td class="py-2"><?= e($h['opened_at']) ?></td>
      <td><?= e($h['closed_at'] ?? '—') ?></td>
      <td><?= e($h['status']) ?></td>
      <td><?= $h['difference'] !== null ? money($h['difference']) : '—' ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
