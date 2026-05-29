<?php
$pageEyebrow = 'Taller';
$pageTitle = $order['order_number'];
$pageDescription = e($order['customer_name']) . ($order['vehicle_label'] ? ' · ' . e($order['vehicle_label']) : '');
$createUrl = null;
require __DIR__ . '/../partials/crud_page_header.php';
?>

<p class="mb-4 flex flex-wrap items-center gap-3">
  <a href="<?= url('/work-orders') ?>" class="text-sm text-accent-600 hover:underline">← Listado</a>
  <?php if ($editable): ?>
  <a href="<?= url('/work-orders/' . $order['id'] . '/edit') ?>" class="btn-secondary text-sm">Editar</a>
  <?php endif; ?>
  <span class="<?= wo_status_badge_class((string) $order['status']) ?>"><?= e(wo_status_label((string) $order['status'])) ?></span>
</p>

<div class="grid gap-6 lg:grid-cols-3">
  <div class="lg:col-span-2 space-y-6">
    <div class="card">
      <h3 class="font-semibold text-navy-900 mb-4">Cliente y vehículo</h3>
      <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
        <div><dt class="text-slate-500">Cliente</dt><dd class="font-medium"><?= e($order['customer_name']) ?></dd></div>
        <div><dt class="text-slate-500">Teléfono</dt><dd><?= e($order['customer_phone'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Vehículo / ref.</dt><dd><?= e($order['vehicle_label'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Detalle</dt><dd><?= e($order['vehicle_notes'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Km</dt><dd><?= $order['odometer_km'] ? e((string) $order['odometer_km']) : '—' ?></dd></div>
        <div><dt class="text-slate-500">Técnico</dt><dd><?= e($order['assigned_name'] ?? '—') ?></dd></div>
      </dl>
      <?php if (!empty($order['reported_issue'])): ?>
      <p class="mt-4 text-sm"><span class="text-slate-500">Motivo:</span> <?= nl2br(e($order['reported_issue'])) ?></p>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3 class="font-semibold text-navy-900 mb-4">Detalle de la orden</h3>
      <table class="data-table text-sm">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
            <th>Stock</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($lines as $line): ?>
        <tr>
          <td class="capitalize text-slate-500"><?= $line['line_type'] === 'part' ? 'Repuesto' : ($line['line_type'] === 'labor' ? 'Mano obra' : 'Otro') ?></td>
          <td class="font-medium"><?= e($line['description']) ?></td>
          <td class="tabular-nums"><?= e($line['quantity']) ?></td>
          <td class="tabular-nums"><?= money($line['unit_price']) ?></td>
          <td class="tabular-nums font-medium"><?= money($line['line_total']) ?></td>
          <td>
            <?php if ($line['line_type'] === 'part' && $line['product_id']): ?>
              <?php if ((int) $line['stock_consumed']): ?>
              <span class="text-xs text-emerald-600">Consumido</span>
              <?php else: ?>
              <span class="text-xs text-amber-600">Pendiente</span>
              <?php endif; ?>
            <?php else: ?>
            <span class="text-slate-400">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="border-t border-slate-200">
            <td colspan="4" class="py-3 text-right font-semibold text-slate-600">Total</td>
            <td colspan="2" class="py-3 font-bold tabular-nums text-navy-900"><?= money($order['total']) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <?php if (!empty($order['notes_internal']) || !empty($order['notes_customer'])): ?>
    <div class="card text-sm space-y-2">
      <?php if (!empty($order['notes_internal'])): ?>
      <p><span class="text-slate-500">Notas internas:</span> <?= nl2br(e($order['notes_internal'])) ?></p>
      <?php endif; ?>
      <?php if (!empty($order['notes_customer'])): ?>
      <p><span class="text-slate-500">Notas cliente:</span> <?= nl2br(e($order['notes_customer'])) ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>

  <div class="space-y-4">
    <div class="card space-y-3">
      <h3 class="font-semibold text-navy-900">Acciones</h3>

      <?php foreach ($statusActions as $target => $label): ?>
      <form method="post" action="<?= url('/work-orders/' . $order['id'] . '/status') ?>" class="space-y-2">
        <?= csrf_field() ?>
        <input type="hidden" name="status" value="<?= e($target) ?>">
        <?php if ($target === 'en_espera' || $target === 'cancelada'): ?>
        <input type="text" name="note" placeholder="Motivo (opcional)" class="input-field text-sm">
        <?php endif; ?>
        <button type="submit" class="btn-secondary w-full text-sm"><?= e($label) ?></button>
      </form>
      <?php endforeach; ?>

      <?php
      $hasPartsPending = false;
      foreach ($lines as $line) {
          if ($line['line_type'] === 'part' && $line['product_id'] && !(int) $line['stock_consumed']) {
              $hasPartsPending = true;
              break;
          }
      }
      ?>
      <?php if ($hasPartsPending && !in_array($order['status'], ['cerrada', 'cancelada'], true)): ?>
      <form method="post" action="<?= url('/work-orders/' . $order['id'] . '/consume') ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn-secondary w-full text-sm">Descontar repuestos del stock</button>
      </form>
      <?php endif; ?>

      <?php if ($order['status'] === 'lista_entregar' && empty($order['sale_id'])): ?>
      <form method="post" action="<?= url('/work-orders/' . $order['id'] . '/charge') ?>" class="space-y-2 border-t border-slate-100 pt-3">
        <?= csrf_field() ?>
        <p class="text-xs text-slate-500">Cobro en caja (requiere caja abierta).</p>
        <label class="label">Forma de pago</label>
        <select name="payment_method" class="input-field text-sm">
          <option value="efectivo">Efectivo</option>
          <option value="transferencia">Transferencia</option>
          <option value="tarjeta">Tarjeta</option>
          <option value="mercado_pago">Mercado Pago</option>
        </select>
        <button type="submit" class="btn-primary w-full">Cobrar en POS</button>
      </form>
      <form method="post" action="<?= url('/work-orders/' . $order['id'] . '/close') ?>" class="space-y-2">
        <?= csrf_field() ?>
        <input type="text" name="note" placeholder="Nota de cierre" class="input-field text-sm">
        <button type="submit" class="btn-secondary w-full text-sm">Cerrar sin cobrar en POS</button>
      </form>
      <?php endif; ?>

      <?php if (!empty($order['sale_id'])): ?>
      <div class="border-t border-slate-100 pt-3 text-sm">
        <p class="text-slate-600">Venta asociada:</p>
        <a href="<?= url('/pos/ticket/' . $order['sale_id']) ?>" class="text-accent-600 font-medium hover:underline" target="_blank" rel="noopener">
          <?= e($order['sale_number'] ?? ('#' . $order['sale_id'])) ?>
        </a>
      </div>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3 class="font-semibold text-navy-900 mb-3">Historial</h3>
      <ul class="space-y-3 text-sm max-h-64 overflow-y-auto">
        <?php foreach ($history as $h): ?>
        <li class="border-l-2 border-slate-200 pl-3">
          <p class="font-medium text-navy-900">
            <?= e(wo_status_label((string) $h['to_status'])) ?>
            <?php if ($h['from_status']): ?>
            <span class="text-slate-400 font-normal">← <?= e(wo_status_label((string) $h['from_status'])) ?></span>
            <?php endif; ?>
          </p>
          <?php if (!empty($h['note'])): ?><p class="text-slate-600"><?= e($h['note']) ?></p><?php endif; ?>
          <p class="text-xs text-slate-400"><?= e($h['user_name'] ?? 'Sistema') ?> · <?= e($h['created_at']) ?></p>
        </li>
        <?php endforeach; ?>
        <?php if (!$history): ?>
        <li class="text-slate-500">Sin movimientos aún.</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>
