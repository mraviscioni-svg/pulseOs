<?php
$pageEyebrow = 'Operaciones';
$pageTitle = 'Caja';
$pageDescription = 'Abrí el turno, registrá movimientos y cerrá con arqueo de efectivo.';
require __DIR__ . '/../partials/crud_page_header.php';

$movementLabels = [
    'venta' => 'Venta',
    'ingreso' => 'Ingreso',
    'egreso' => 'Egreso',
    'retiro' => 'Retiro',
    'ajuste' => 'Ajuste',
];
$movementBadge = [
    'venta' => 'badge-success',
    'ingreso' => 'badge-success',
    'egreso' => 'badge-warning',
    'retiro' => 'badge-warning',
    'ajuste' => 'badge-muted',
];
?>

<?php if ($openCash): ?>
<div class="cash-status-banner cash-status-open">
  <div class="cash-status-banner-icon" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  </div>
  <div class="min-w-0 flex-1">
    <p class="cash-status-banner-title">Caja abierta</p>
    <p class="cash-status-banner-meta">Desde <?= e($openCash['opened_at']) ?> · Apertura <?= money($openCash['opening_amount']) ?></p>
  </div>
  <?php if (module_enabled('pos') && can('pos.sell')): ?>
  <a href="<?= url('/pos') ?>" class="btn-secondary shrink-0 !py-2 text-xs">Ir al POS</a>
  <?php endif; ?>
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <div class="stat-card">
    <p class="stat-card-label">Monto apertura</p>
    <p class="stat-card-value"><?= money($openCash['opening_amount']) ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Efectivo esperado</p>
    <p class="stat-card-value text-navy-900"><?= money($expectedAmount ?? 0) ?></p>
    <p class="stat-card-sub">Según movimientos del turno</p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Ventas en efectivo</p>
    <p class="stat-card-value"><?= money($movementTotals['ventas'] ?? 0) ?></p>
    <p class="stat-card-sub"><?= (int) ($movementTotals['count'] ?? 0) ?> movimientos</p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Ingresos / egresos</p>
    <p class="stat-card-value text-base">
      <span class="text-emerald-700">+<?= money($movementTotals['ingresos'] ?? 0) ?></span>
      <span class="mx-1 text-slate-300">/</span>
      <span class="text-amber-700">−<?= money(($movementTotals['egresos'] ?? 0) + ($movementTotals['retiros'] ?? 0)) ?></span>
    </p>
  </div>
</div>

<div class="grid gap-6 xl:grid-cols-5">
  <div class="cash-close-card xl:col-span-2">
    <div class="cash-panel-head">
      <h3>Cerrar turno</h3>
      <p>Contá el efectivo y confirmá el arqueo.</p>
    </div>
    <form method="post" action="<?= url('/cash/close') ?>" class="space-y-4"
      x-data="{ expected: <?= json_encode((float) ($expectedAmount ?? 0)) ?>, counted: '' }"
      data-confirm-title="Cerrar caja"
      data-confirm-message="Se registrará el cierre del turno con el monto que ingresaste."
      data-confirm-label="Cerrar caja"
      data-confirm-danger="1">
      <?= csrf_field() ?>
      <div class="cash-expected-box">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Debería haber</span>
        <p class="mt-1 text-2xl font-bold text-navy-900"><?= money($expectedAmount ?? 0) ?></p>
      </div>
      <div class="form-group">
        <label class="label" for="closing_amount">Efectivo contado</label>
        <input type="number" step="0.01" min="0" id="closing_amount" name="closing_amount"
          x-model="counted" required class="input-field text-lg font-semibold" placeholder="0,00">
      </div>
      <p class="text-sm" x-show="counted !== '' && counted !== null" x-cloak>
        <span class="text-slate-500">Diferencia: </span>
        <span :class="(parseFloat(counted || 0) - expected) >= -0.009 ? 'font-semibold text-emerald-700' : 'font-semibold text-rose-700'"
          x-text="new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(parseFloat(counted || 0) - expected)"></span>
      </p>
      <div class="form-group">
        <label class="label" for="close_notes">Notas (opcional)</label>
        <textarea id="close_notes" name="notes" rows="2" class="input-field" placeholder="Observaciones del cierre"></textarea>
      </div>
      <button type="submit" class="btn-danger w-full justify-center">Cerrar caja</button>
    </form>
  </div>

  <div class="form-card xl:col-span-3">
    <div class="cash-panel-head !mb-4">
      <h3>Movimiento manual</h3>
      <p>Ingresos, egresos o retiros fuera del POS.</p>
    </div>
    <form method="post" action="<?= url('/cash/movement') ?>" class="grid gap-4 sm:grid-cols-2">
      <?= csrf_field() ?>
      <div class="form-group sm:col-span-1">
        <label class="label" for="movement_type">Tipo</label>
        <select id="movement_type" name="type" class="input-field">
          <option value="ingreso">Ingreso</option>
          <option value="egreso">Egreso</option>
          <option value="retiro">Retiro</option>
        </select>
      </div>
      <div class="form-group sm:col-span-1">
        <label class="label" for="movement_amount">Monto</label>
        <input type="number" step="0.01" min="0.01" id="movement_amount" name="amount" required class="input-field" placeholder="0,00">
      </div>
      <div class="form-group sm:col-span-2">
        <label class="label" for="movement_description">Descripción</label>
        <input id="movement_description" name="description" class="input-field" placeholder="Ej. Pago proveedor, cambio, etc.">
      </div>
      <div class="sm:col-span-2">
        <button type="submit" class="btn-primary">Registrar movimiento</button>
      </div>
    </form>
  </div>
</div>

<div class="data-table-wrap mt-6">
  <div class="data-table-head">
    <h3>Movimientos del turno</h3>
    <span class="text-xs text-slate-500"><?= count($movements) ?> registros</span>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Tipo</th>
          <th>Descripción</th>
          <th class="text-right">Monto</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($movements as $m): ?>
      <?php
        $type = (string) $m['type'];
        $badge = $movementBadge[$type] ?? 'badge-muted';
        $label = $movementLabels[$type] ?? ucfirst($type);
        $isOut = in_array($type, ['egreso', 'retiro'], true);
      ?>
      <tr>
        <td class="text-xs text-slate-500 whitespace-nowrap"><?= e($m['created_at']) ?></td>
        <td><span class="badge <?= $badge ?>"><?= e($label) ?></span></td>
        <td><?= e($m['description'] ?? '—') ?></td>
        <td class="text-right font-medium tabular-nums <?= $isOut ? 'text-amber-800' : 'text-emerald-800' ?>">
          <?= $isOut ? '−' : '+' ?><?= money($m['amount']) ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$movements): ?>
      <tr>
        <td colspan="4" class="py-10 text-center text-slate-500">Todavía no hay movimientos en este turno.</td>
      </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php else: ?>
<div class="cash-status-banner cash-status-closed">
  <div class="cash-status-banner-icon" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
  </div>
  <div class="min-w-0 flex-1">
    <p class="cash-status-banner-title">Caja cerrada</p>
    <p class="cash-status-banner-meta">Abrí un turno para vender en el POS y registrar movimientos.</p>
  </div>
</div>

<div class="form-card max-w-lg">
  <div class="cash-panel-head">
    <h3>Abrir caja</h3>
    <p>Indicá el efectivo con el que empezás el turno.</p>
  </div>
  <form method="post" action="<?= url('/cash/open') ?>" class="space-y-4">
    <?= csrf_field() ?>
    <div class="form-group">
      <label class="label" for="opening_amount">Monto inicial en caja</label>
      <input type="number" step="0.01" min="0" id="opening_amount" name="opening_amount" value="0" class="input-field text-lg font-semibold" placeholder="0,00">
      <p class="form-hint">Puede ser $0 si arrancás sin efectivo.</p>
    </div>
    <button type="submit" class="btn-primary w-full justify-center">Abrir caja</button>
  </form>
</div>
<?php endif; ?>

<div class="data-table-wrap mt-6">
  <div class="data-table-head">
    <h3>Historial de turnos</h3>
  </div>
  <div class="data-table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <th>Apertura</th>
          <th>Cierre</th>
          <th>Apertura $</th>
          <th>Cierre $</th>
          <th>Esperado</th>
          <th>Diferencia</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($history as $h): ?>
      <?php
        $isOpen = ($h['status'] ?? '') === 'abierta';
        $diff = $h['difference'] !== null ? (float) $h['difference'] : null;
      ?>
      <tr class="<?= $isOpen ? 'bg-emerald-50/40' : '' ?>">
        <td class="text-xs whitespace-nowrap"><?= e($h['opened_at']) ?></td>
        <td class="text-xs whitespace-nowrap"><?= e($h['closed_at'] ?? '—') ?></td>
        <td class="tabular-nums"><?= money($h['opening_amount']) ?></td>
        <td class="tabular-nums"><?= $h['closing_amount'] !== null ? money($h['closing_amount']) : '—' ?></td>
        <td class="tabular-nums"><?= $h['expected_amount'] !== null ? money($h['expected_amount']) : '—' ?></td>
        <td class="tabular-nums font-medium <?= $diff === null ? '' : ($diff >= 0 ? 'text-emerald-700' : 'text-rose-700') ?>">
          <?= $diff !== null ? money($diff) : '—' ?>
        </td>
        <td>
          <?php if ($isOpen): ?>
          <span class="badge badge-success">Abierta</span>
          <?php else: ?>
          <span class="badge badge-muted">Cerrada</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$history): ?>
      <tr><td colspan="7" class="py-10 text-center text-slate-500">Sin turnos registrados.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
