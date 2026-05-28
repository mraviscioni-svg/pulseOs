<div x-data="purchaseForm()" class="grid gap-6 lg:grid-cols-3">
  <div class="card lg:col-span-2">
    <h3 class="mb-4 font-semibold">Productos</h3>
    <input x-model="search" @input.debounce.300ms="filterProducts" placeholder="Buscar producto…" class="input-field mb-4">
    <div class="max-h-64 overflow-y-auto space-y-2 text-sm">
      <template x-for="p in filtered" :key="p.id">
        <button type="button" @click="addItem(p)" class="flex w-full justify-between rounded-lg border border-slate-800 px-3 py-2 hover:bg-slate-900">
          <span x-text="p.name"></span>
          <span class="text-slate-500" x-text="'$' + p.price"></span>
        </button>
      </template>
    </div>
    <table class="mt-4 w-full text-sm">
      <thead class="text-slate-500"><tr><th>Producto</th><th>Cant.</th><th>Costo</th><th></th></tr></thead>
      <tbody>
        <template x-for="(item, i) in items" :key="i">
          <tr class="border-t border-slate-800">
            <td class="py-2" x-text="item.name"></td>
            <td><input type="number" step="0.001" x-model.number="item.quantity" class="w-20 rounded border border-slate-700 bg-slate-950 px-2 py-1"></td>
            <td><input type="number" step="0.01" x-model.number="item.unit_cost" class="w-24 rounded border border-slate-700 bg-slate-950 px-2 py-1"></td>
            <td><button type="button" @click="items.splice(i,1)" class="text-rose-400">×</button></td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
  <form method="post" action="<?= url('/purchases') ?>" class="card space-y-4">
    <?= csrf_field() ?>
    <input type="hidden" name="items_json" :value="JSON.stringify(items.map(i => ({product_id: i.product_id, quantity: i.quantity, unit_cost: i.unit_cost})))">
    <div>
      <label class="label">Proveedor</label>
      <select name="supplier_id" class="input-field">
        <option value="">— Sin proveedor —</option>
        <?php foreach ($suppliers as $s): ?>
        <option value="<?= (int)$s['id'] ?>"><?= e($s['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label class="label">Notas</label><textarea name="notes" class="input-field"></textarea></div>
    <button type="submit" class="btn-primary w-full" :disabled="items.length === 0">Crear orden</button>
  </form>
</div>
<script>
const allProducts = <?= json_encode($products, JSON_UNESCAPED_UNICODE) ?>;
function purchaseForm() {
  return {
    search: '', items: [], filtered: allProducts,
    filterProducts() {
      const q = this.search.toLowerCase();
      this.filtered = allProducts.filter(p => p.name.toLowerCase().includes(q));
    },
    addItem(p) {
      this.items.push({ product_id: p.id, name: p.name, quantity: 1, unit_cost: parseFloat(p.cost) || 0 });
    }
  }
}
</script>
