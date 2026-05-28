<div x-data="inventoryApp()" class="grid gap-6 lg:grid-cols-2">
  <div class="card">
    <h3 class="mb-4 font-semibold">Escanear / buscar</h3>
    <div id="qr-reader" class="mb-4 overflow-hidden rounded-lg border border-slate-700"></div>
    <button type="button" @click="toggleCamera" class="mb-4 text-sm text-pulse-400" x-text="cameraOn ? 'Detener cámara' : 'Escanear con cámara'"></button>
    <input x-ref="barcode" x-model="code" @keydown.enter.prevent="lookup" placeholder="Código de barras (lector USB o manual)" class="input-field mb-2" autofocus>
    <button type="button" @click="lookup" class="btn-primary w-full">Buscar producto</button>
  </div>
  <div class="card" x-show="product">
    <template x-if="product">
      <div>
        <h3 class="text-lg font-semibold" x-text="product.name"></h3>
        <p class="text-sm text-slate-400">Stock actual: <span x-text="product.stock"></span></p>
        <div class="mt-4 space-y-3">
          <div>
            <label class="label">Conteo físico (stock real)</label>
            <input type="number" step="0.001" x-model.number="targetStock" class="input-field">
            <button type="button" @click="setCount" class="btn-primary mt-2 w-full">Aplicar conteo</button>
          </div>
          <div>
            <label class="label">Ajuste rápido (+/-)</label>
            <input type="number" step="0.001" x-model.number="adjustQty" class="input-field">
            <button type="button" @click="adjust" class="mt-2 w-full rounded-lg border border-slate-700 py-2">Ajustar</button>
          </div>
        </div>
      </div>
    </template>
    <p x-show="!product" class="text-slate-500">Escaneá o buscá un producto.</p>
  </div>
</div>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const csrfToken = <?= json_encode(\App\Core\Csrf::token()) ?>;
function inventoryApp() {
  return {
    code: '', product: null, targetStock: 0, adjustQty: 0, cameraOn: false, scanner: null,
    async lookup() {
      if (!this.code) return;
      const r = await fetch(`<?= url('/api/products/barcode') ?>?code=${encodeURIComponent(this.code)}`);
      const j = await r.json();
      if (j.data) { this.product = j.data; this.targetStock = parseFloat(j.data.stock); }
      else alert('No encontrado');
    },
    async setCount() {
      const r = await fetch('<?= url('/inventory/count') ?>', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ product_id: this.product.id, target_stock: this.targetStock })
      });
      const j = await r.json();
      if (j.success) { this.product.stock = j.stock; alert('Stock actualizado'); } else alert(j.error);
    },
    async adjust() {
      const r = await fetch('<?= url('/inventory/adjust') ?>', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ product_id: this.product.id, quantity: this.adjustQty })
      });
      const j = await r.json();
      if (j.success) { this.product.stock = j.stock; this.adjustQty = 0; } else alert(j.error);
    },
    toggleCamera() {
      if (this.cameraOn) {
        this.scanner.stop().then(() => { this.cameraOn = false; });
        return;
      }
      this.scanner = new Html5Qrcode('qr-reader');
      this.scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 200 },
        (text) => { this.code = text; this.lookup(); },
        () => {}
      ).then(() => { this.cameraOn = true; });
    }
  };
}
</script>
