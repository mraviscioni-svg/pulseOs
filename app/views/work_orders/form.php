<?php
$order = $order ?? [];
$isEdit = !empty($order['id']);
$formAction = $isEdit ? url('/work-orders/' . $order['id']) : url('/work-orders');
$pageEyebrow = 'Taller';
$pageTitle = $title ?? ($isEdit ? 'Editar orden' : 'Nueva orden de trabajo');
$pageDescription = 'Cliente, vehículo, mano de obra y repuestos.';
$createUrl = null;
require __DIR__ . '/../partials/crud_page_header.php';
?>

<p class="mb-4">
  <a href="<?= url($isEdit ? '/work-orders/' . $order['id'] : '/work-orders') ?>" class="text-sm text-accent-600 hover:underline">← Volver</a>
</p>

<?php
$prefill = $prefillCustomer ?? [];
$initialCustomerId = (int) ($order['customer_id'] ?? ($prefill['id'] ?? 0));
$initialVehicleId = (int) ($order['customer_vehicle_id'] ?? 0);
?>
<div x-data="workOrderForm()" class="grid gap-6 xl:grid-cols-3">
  <div class="card xl:col-span-2 space-y-6">
    <div>
      <h3 class="mb-3 font-semibold text-navy-900">Mano de obra y otros</h3>
      <div class="flex gap-2 mb-3">
        <input x-model="laborDesc" type="text" placeholder="Descripción del trabajo…" class="input-field flex-1">
        <input x-model.number="laborPrice" type="number" step="0.01" min="0" placeholder="Precio" class="input-field w-28">
        <button type="button" @click="addLabor()" class="btn-secondary shrink-0">+ Agregar</button>
      </div>
    </div>

    <div>
      <h3 class="mb-3 font-semibold text-navy-900">Repuestos</h3>
      <input x-model="search" @input.debounce.300ms="filterProducts" placeholder="Buscar producto…" class="input-field mb-3">
      <div class="max-h-40 overflow-y-auto space-y-1 text-sm mb-3">
        <template x-for="p in filtered" :key="p.id">
          <button type="button" @click="addPart(p)" class="flex w-full justify-between rounded-lg border border-slate-200 px-3 py-2 text-left hover:bg-slate-50">
            <span x-text="p.name"></span>
            <span class="text-slate-500" x-text="'$' + parseFloat(p.price).toFixed(2)"></span>
          </button>
        </template>
      </div>
    </div>

    <div>
      <h3 class="mb-2 font-semibold text-navy-900">Ítems de la orden</h3>
      <table class="w-full text-sm">
        <thead class="text-slate-500">
          <tr><th class="text-left py-1">Tipo</th><th>Descripción</th><th>Cant.</th><th>Precio</th><th>Subtotal</th><th></th></tr>
        </thead>
        <tbody>
          <template x-for="(item, i) in items" :key="i">
            <tr class="border-t border-slate-100">
              <td class="py-2 capitalize text-xs text-slate-500" x-text="item.line_type === 'part' ? 'Repuesto' : (item.line_type === 'labor' ? 'Mano obra' : 'Otro')"></td>
              <td class="py-2" x-text="item.description"></td>
              <td class="py-2"><input type="number" step="0.001" min="0.001" x-model.number="item.quantity" @input="recalcItem(item)" class="input-field !w-20 !py-1"></td>
              <td class="py-2"><input type="number" step="0.01" min="0" x-model.number="item.unit_price" @input="recalcItem(item)" class="input-field !w-24 !py-1"></td>
              <td class="py-2 tabular-nums" x-text="'$' + lineTotal(item).toFixed(2)"></td>
              <td class="py-2"><button type="button" @click="items.splice(i,1)" class="text-rose-500">×</button></td>
            </tr>
          </template>
        </tbody>
        <tfoot>
          <tr class="border-t border-slate-200 font-semibold">
            <td colspan="4" class="py-3 text-right text-slate-600">Total estimado</td>
            <td colspan="2" class="py-3 tabular-nums text-navy-900" x-text="'$' + grandTotal().toFixed(2)"></td>
          </tr>
        </tfoot>
      </table>
      <p x-show="items.length === 0" class="text-sm text-slate-500 mt-2">Agregá mano de obra o repuestos.</p>
    </div>
  </div>

  <form method="post" action="<?= $formAction ?>" class="card space-y-4 h-fit">
    <?= csrf_field() ?>
    <input type="hidden" name="lines_json" :value="JSON.stringify(items)">
    <input type="hidden" name="customer_id" :value="customerId || ''">
    <input type="hidden" name="customer_vehicle_id" :value="vehicleId || ''">

    <?php if (!empty($customersEnabled)): ?>
    <div>
      <label class="label">Buscar en directorio</label>
      <input type="search" x-model="customerSearch" @input.debounce.300ms="searchCustomers" placeholder="Nombre o teléfono…" class="input-field">
      <div x-show="customerResults.length" class="mt-2 max-h-36 overflow-y-auto rounded-lg border border-slate-200 text-sm">
        <template x-for="c in customerResults" :key="c.id">
          <button type="button" @click="pickCustomer(c)" class="flex w-full justify-between px-3 py-2 text-left hover:bg-slate-50">
            <span x-text="c.name"></span>
            <span class="text-slate-400" x-text="c.phone || ''"></span>
          </button>
        </template>
      </div>
      <p x-show="customerId" class="mt-2 text-xs text-emerald-700">
        Cliente del directorio seleccionado.
        <button type="button" @click="clearCustomer()" class="text-accent-600 hover:underline ml-1">Quitar</button>
        <?php if (can('customers.manage')): ?>
        · <a href="<?= url('/customers/create') ?>" target="_blank" rel="noopener" class="text-accent-600 hover:underline">+ Nuevo cliente</a>
        <?php endif; ?>
      </p>
    </div>
    <?php endif; ?>

    <div>
      <label class="label">Cliente *</label>
      <input name="customer_name" required class="input-field" x-model="customerName" value="<?= e($order['customer_name'] ?? old('customer_name', $prefill['name'] ?? '')) ?>">
    </div>
    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="label">Teléfono</label>
        <input name="customer_phone" class="input-field" x-model="customerPhone" value="<?= e($order['customer_phone'] ?? old('customer_phone', $prefill['phone'] ?? '')) ?>">
      </div>
      <div>
        <label class="label">Email</label>
        <input name="customer_email" type="email" class="input-field" x-model="customerEmail" value="<?= e($order['customer_email'] ?? old('customer_email', $prefill['email'] ?? '')) ?>">
      </div>
    </div>

    <?php if (!empty($customersEnabled)): ?>
    <div x-show="vehicles.length">
      <label class="label">Vehículo del cliente</label>
      <select class="input-field" x-model="vehicleId" @change="pickVehicle()">
        <option value="">— Manual / otro —</option>
        <template x-for="v in vehicles" :key="v.id">
          <option :value="v.id" x-text="v.label + (v.description ? ' — ' + v.description : '')"></option>
        </template>
      </select>
    </div>
    <?php endif; ?>

    <div>
      <label class="label">Patente / referencia vehículo</label>
      <input name="vehicle_label" class="input-field" placeholder="Ej. AB123CD" x-model="vehicleLabel" value="<?= e($order['vehicle_label'] ?? old('vehicle_label', '')) ?>">
    </div>
    <div>
      <label class="label">Datos del vehículo</label>
      <input name="vehicle_notes" class="input-field" placeholder="Marca, modelo, color…" x-model="vehicleNotes" value="<?= e($order['vehicle_notes'] ?? old('vehicle_notes', '')) ?>">
    </div>
    <div>
      <label class="label">Kilometraje</label>
      <input name="odometer_km" type="number" min="0" class="input-field" value="<?= e((string) ($order['odometer_km'] ?? old('odometer_km', ''))) ?>">
    </div>
    <div>
      <label class="label">Motivo / falla reportada</label>
      <textarea name="reported_issue" class="input-field" rows="2"><?= e($order['reported_issue'] ?? old('reported_issue', '')) ?></textarea>
    </div>

    <div>
      <label class="label">Técnico asignado</label>
      <select name="assigned_user_id" class="input-field">
        <option value="">— Sin asignar —</option>
        <?php foreach ($users as $u): ?>
        <option value="<?= (int) $u['id'] ?>" <?= (int) (($order['assigned_user_id'] ?? null) ?: 0) === (int) $u['id'] ? 'selected' : '' ?>><?= e($u['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="label">Prioridad</label>
      <select name="priority" class="input-field">
        <?php foreach (['baja' => 'Baja', 'normal' => 'Normal', 'alta' => 'Alta'] as $val => $lab): ?>
        <option value="<?= $val ?>" <?= (($order['priority'] ?? null) ?: 'normal') === $val ? 'selected' : '' ?>><?= $lab ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="label">Notas internas</label>
      <textarea name="notes_internal" class="input-field" rows="2"><?= e($order['notes_internal'] ?? old('notes_internal', '')) ?></textarea>
    </div>
    <div>
      <label class="label">Notas para el cliente</label>
      <textarea name="notes_customer" class="input-field" rows="2"><?= e($order['notes_customer'] ?? old('notes_customer', '')) ?></textarea>
    </div>

    <button type="submit" class="btn-primary w-full" :disabled="items.length === 0">
      <?= $isEdit ? 'Guardar cambios' : 'Crear orden' ?>
    </button>
  </form>
</div>

<script>
const allProducts = <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>;
const initialLines = <?= json_encode(array_map(static function ($l) {
    return [
        'line_type' => $l['line_type'],
        'product_id' => $l['product_id'] ? (int) $l['product_id'] : null,
        'description' => $l['description'],
        'quantity' => (float) $l['quantity'],
        'unit_price' => (float) $l['unit_price'],
    ];
}, $lines), JSON_UNESCAPED_UNICODE) ?>;
const customersApiBase = <?= json_encode(url('/api/customers'), JSON_UNESCAPED_UNICODE) ?>;
const initialCustomerId = <?= (int) $initialCustomerId ?>;
const initialVehicleId = <?= (int) $initialVehicleId ?>;
const initialVehicles = <?= json_encode($customerVehicles ?? [], JSON_UNESCAPED_UNICODE) ?>;

function workOrderForm() {
  return {
    search: '',
    laborDesc: '',
    laborPrice: 0,
    items: initialLines,
    filtered: allProducts,
    customerSearch: '',
    customerResults: [],
    customerId: initialCustomerId || null,
    vehicleId: initialVehicleId || '',
    vehicles: initialVehicles,
    customerName: <?= json_encode($order['customer_name'] ?? $prefill['name'] ?? '') ?>,
    customerPhone: <?= json_encode($order['customer_phone'] ?? $prefill['phone'] ?? '') ?>,
    customerEmail: <?= json_encode($order['customer_email'] ?? $prefill['email'] ?? '') ?>,
    vehicleLabel: <?= json_encode($order['vehicle_label'] ?? '') ?>,
    vehicleNotes: <?= json_encode($order['vehicle_notes'] ?? '') ?>,
    filterProducts() {
      const q = this.search.toLowerCase();
      this.filtered = allProducts.filter(p => p.name.toLowerCase().includes(q));
    },
    addLabor() {
      const d = (this.laborDesc || '').trim();
      if (!d) return;
      this.items.push({
        line_type: 'labor',
        product_id: null,
        description: d,
        quantity: 1,
        unit_price: parseFloat(this.laborPrice) || 0,
      });
      this.laborDesc = '';
      this.laborPrice = 0;
    },
    addPart(p) {
      this.items.push({
        line_type: 'part',
        product_id: p.id,
        description: p.name,
        quantity: 1,
        unit_price: parseFloat(p.price) || 0,
      });
    },
    recalcItem(item) {},
    lineTotal(item) {
      return (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
    },
    grandTotal() {
      return this.items.reduce((s, i) => s + this.lineTotal(i), 0);
    },
    async searchCustomers() {
      const q = this.customerSearch.trim();
      if (q.length < 2) {
        this.customerResults = [];
        return;
      }
      try {
        const res = await fetch(customersApiBase + '/search?q=' + encodeURIComponent(q));
        const json = await res.json();
        this.customerResults = json.data || [];
      } catch (e) {
        this.customerResults = [];
      }
    },
    async pickCustomer(c) {
      this.customerId = c.id;
      this.customerName = c.name;
      this.customerPhone = c.phone || '';
      this.customerEmail = c.email || '';
      this.customerResults = [];
      this.customerSearch = c.name;
      this.vehicleId = '';
      try {
        const res = await fetch(customersApiBase + '/' + c.id);
        const json = await res.json();
        this.vehicles = json.data?.vehicles || [];
      } catch (e) {
        this.vehicles = [];
      }
    },
    clearCustomer() {
      this.customerId = null;
      this.vehicleId = '';
      this.vehicles = [];
      this.customerSearch = '';
    },
    pickVehicle() {
      const v = this.vehicles.find(x => String(x.id) === String(this.vehicleId));
      if (v) {
        this.vehicleLabel = v.label;
        this.vehicleNotes = v.description || '';
      }
    },
  };
}
</script>
