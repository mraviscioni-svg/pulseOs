<?php
$pageEyebrow = 'Clientes';
$pageTitle = $customer['name'];
$pageDescription = trim(($customer['phone'] ?? '') . ' ' . ($customer['email'] ?? ''));
$createUrl = null;
require __DIR__ . '/../partials/crud_page_header.php';
?>

<p class="mb-4 flex flex-wrap gap-3">
  <a href="<?= url('/customers') ?>" class="text-sm text-accent-600 hover:underline">← Listado</a>
  <a href="<?= url('/customers/' . $customer['id'] . '/edit') ?>" class="btn-secondary text-sm">Editar</a>
  <?php if (module_enabled('work_orders') && can('work_orders.manage')): ?>
  <a href="<?= url('/work-orders/create?customer_id=' . $customer['id']) ?>" class="btn-primary text-sm">+ Nueva orden de trabajo</a>
  <?php endif; ?>
</p>

<div class="grid gap-6 lg:grid-cols-3">
  <div class="card lg:col-span-2 space-y-4">
    <h3 class="font-semibold text-navy-900">Datos del cliente</h3>
    <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
      <div><dt class="text-slate-500">Nombre</dt><dd class="font-medium"><?= e($customer['name']) ?></dd></div>
      <div><dt class="text-slate-500">Empresa</dt><dd><?= e($customer['company'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">CUIT / DNI</dt><dd class="font-mono text-xs"><?= e($customer['tax_id'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">Teléfono</dt><dd><?= e($customer['phone'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">Email</dt><dd><?= e($customer['email'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">Estado</dt><dd><?php $active = (bool) $customer['is_active']; require __DIR__ . '/../partials/status_badge.php'; ?></dd></div>
      <?php if (!empty($customer['address'])): ?>
      <div class="sm:col-span-2"><dt class="text-slate-500">Dirección</dt><dd><?= nl2br(e($customer['address'])) ?></dd></div>
      <?php endif; ?>
      <?php if (!empty($customer['notes'])): ?>
      <div class="sm:col-span-2"><dt class="text-slate-500">Observaciones</dt><dd><?= nl2br(e($customer['notes'])) ?></dd></div>
      <?php endif; ?>
    </dl>
  </div>

  <div class="space-y-4">
    <?php if ($showVehicles): ?>
    <div class="card">
      <h3 class="font-semibold text-navy-900 mb-3">Vehículos</h3>
      <?php if (!empty($customer['vehicles'])): ?>
      <ul class="space-y-2 text-sm mb-4">
        <?php foreach ($customer['vehicles'] as $v): ?>
        <li class="flex justify-between gap-2 border-b border-slate-100 pb-2">
          <div>
            <span class="font-mono font-medium"><?= e($v['label']) ?></span>
            <?php if (!empty($v['description'])): ?>
            <span class="text-slate-500"> — <?= e($v['description']) ?></span>
            <?php endif; ?>
          </div>
          <form method="post" action="<?= url('/customers/' . $customer['id'] . '/vehicles/' . $v['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar este vehículo?')">
            <?= csrf_field() ?>
            <button type="submit" class="text-xs text-rose-500 hover:underline">Eliminar</button>
          </form>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php else: ?>
      <p class="text-sm text-slate-500 mb-4">Sin vehículos cargados.</p>
      <?php endif; ?>
      <form method="post" action="<?= url('/customers/' . $customer['id'] . '/vehicles') ?>" class="space-y-2 border-t border-slate-100 pt-3">
        <?= csrf_field() ?>
        <label class="label">Patente / referencia</label>
        <input name="label" required class="input-field text-sm" placeholder="Ej. AB123CD">
        <label class="label">Marca y modelo</label>
        <input name="description" class="input-field text-sm" placeholder="Ej. Ford Ka 2018">
        <button type="submit" class="btn-secondary w-full text-sm">+ Agregar vehículo</button>
      </form>
    </div>
    <?php endif; ?>

    <?php if (module_enabled('work_orders') && $workOrders): ?>
    <div class="card">
      <h3 class="font-semibold text-navy-900 mb-3">Órdenes recientes</h3>
      <ul class="space-y-2 text-sm">
        <?php foreach ($workOrders as $wo): ?>
        <li>
          <a href="<?= url('/work-orders/' . $wo['id']) ?>" class="font-mono text-accent-600 hover:underline"><?= e($wo['order_number']) ?></a>
          <span class="text-slate-500"> · <?= e(wo_status_label((string) $wo['status'])) ?></span>
          <span class="block text-xs text-slate-400"><?= e($wo['created_at']) ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>
