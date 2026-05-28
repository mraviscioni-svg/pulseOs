<form method="post" action="<?= url('/reset-password/' . e($token)) ?>" class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
  <?= csrf_field() ?>
  <p class="mb-4 text-sm text-slate-400">Elegí una nueva contraseña (mínimo 8 caracteres).</p>
  <input type="password" name="password" required minlength="8" class="input-field mb-4" placeholder="Nueva contraseña">
  <button class="w-full rounded-lg bg-pulse-600 py-2.5 font-medium">Guardar contraseña</button>
</form>
