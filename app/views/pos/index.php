<?php if (!$openCash): ?>
<div class="mb-4 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-amber-200">
  No hay caja abierta. <a href="<?= url('/cash') ?>" class="underline">Abrir caja</a> antes de vender.
</div>
<?php endif; ?>

<div x-data="posApp()" x-init="init()" class="grid gap-4 lg:grid-cols-3">
  <div class="card lg:col-span-2">
    <input x-ref="barcode" x-model="barcode" @keydown.enter.prevent="scanBarcode"
      placeholder="Escanear código de barras o buscar…"
      class="input-field mb-4 text-lg" autofocus>
    <input x-model="search" @input.debounce.200ms="searchProducts" placeholder="Buscar por nombre…" class="input-field mb-4">
    <div class="grid max-h-80 gap-2 overflow-y-auto sm:grid-cols-2">
      <template x-for="p in results" :key="p.id">
        <button type="button" @click="addToCart(p)" class="rounded-lg border border-slate-800 p-3 text-left hover:border-pulse-500">
          <p class="font-medium" x-text="p.name"></p>
          <p class="text-sm text-slate-400" x-text="money(p.price) + ' · Stock: ' + p.stock"></p>
        </button>
      </template>
    </div>
  </div>
  <div class="card flex flex-col">
    <h3 class="mb-3 font-semibold">Carrito</h3>
    <div class="flex-1 space-y-2 overflow-y-auto text-sm">
      <template x-for="(item, i) in cart" :key="i">
        <div class="flex items-center justify-between border-b border-slate-800 py-2">
          <div>
            <p x-text="item.name"></p>
            <div class="mt-1 flex items-center gap-2">
              <button type="button" @click="item.quantity = Math.max(0.001, item.quantity - 1)" class="h-6 w-6 rounded bg-slate-800">-</button>
              <input type="number" step="0.001" x-model.number="item.quantity" class="w-16 rounded border border-slate-700 bg-slate-950 px-1 text-center">
              <button type="button" @click="item.quantity++" class="h-6 w-6 rounded bg-slate-800">+</button>
            </div>
          </div>
          <div class="text-right">
            <p x-text="money(item.unit_price * item.quantity)"></p>
            <button type="button" @click="cart.splice(i,1)" class="text-xs text-rose-400">Quitar</button>
          </div>
        </div>
      </template>
    </div>
    <div class="mt-4 space-y-2 border-t border-slate-800 pt-4">
      <div class="flex justify-between"><span>Subtotal</span><span x-text="money(subtotal)"></span></div>
      <div class="flex items-center gap-2">
        <span>Descuento</span>
        <input type="number" step="0.01" x-model.number="discount" class="w-24 rounded border border-slate-700 bg-slate-950 px-2 py-1 text-right">
      </div>
      <div class="flex justify-between text-lg font-bold"><span>Total</span><span x-text="money(total)"></span></div>
      <select x-model="paymentMethod" class="input-field">
        <option value="efectivo">Efectivo</option>
        <option value="transferencia">Transferencia</option>
        <option value="tarjeta">Tarjeta</option>
        <option value="mercado_pago">Mercado Pago</option>
        <option value="cuenta_corriente">Cuenta corriente</option>
        <option value="mixto">Pago mixto</option>
      </select>
      <button type="button" @click="checkout" :disabled="cart.length === 0 || !canSell"
        class="btn-primary w-full py-3 text-lg">Cobrar</button>
    </div>
  </div>
</div>
<script>
const csrfToken = <?= json_encode(\App\Core\Csrf::token()) ?>;
const canSell = <?= $openCash ? 'true' : 'false' ?>;
function posApp() {
  return {
    barcode: '', search: '', cart: [], results: [], discount: 0, paymentMethod: 'efectivo', canSell,
    get subtotal() { return this.cart.reduce((s,i) => s + i.unit_price * i.quantity, 0); },
    get total() { return Math.max(0, this.subtotal - this.discount); },
    init() { this.results = <?= json_encode(array_slice($products, 0, 20), JSON_UNESCAPED_UNICODE) ?>; },
    money(n) { return '$ ' + Number(n).toFixed(2).replace('.', ','); },
    addToCart(p) {
      const ex = this.cart.find(i => i.product_id === p.id);
      if (ex) ex.quantity++; else this.cart.push({ product_id: p.id, name: p.name, unit_price: parseFloat(p.price), quantity: 1 });
      this.barcode = '';
      this.$refs.barcode?.focus();
    },
    async searchProducts() {
      const r = await fetch(`<?= url('/api/products/search') ?>?q=${encodeURIComponent(this.search)}`);
      const j = await r.json(); this.results = j.data || [];
    },
    async scanBarcode() {
      if (!this.barcode) return;
      const r = await fetch(`<?= url('/api/products/barcode') ?>?code=${encodeURIComponent(this.barcode)}`);
      const j = await r.json();
      if (j.data) this.addToCart(j.data); else alert('Producto no encontrado');
      this.barcode = '';
    },
    async checkout() {
      const r = await fetch('<?= url('/pos/complete') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ items_json: JSON.stringify(this.cart), discount: this.discount, payment_method: this.paymentMethod })
      });
      const j = await r.json();
      if (j.success) window.location = j.redirect;
      else alert(j.error || 'Error al vender');
    }
  };
}
</script>
