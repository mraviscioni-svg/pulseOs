<form method="post" action="<?= url('/forgot-password') ?>" class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
  <?= csrf_field() ?>
  <p class="mb-4 text-sm text-slate-400">Ingresá tu email y te enviaremos un enlace (MVP).</p>
  <input type="email" name="email" required class="input-field mb-4">
  <button class="w-full rounded-lg bg-pulse-600 py-2.5">Enviar</button>
</form>
