<form method="post" action="<?= url('/admin/login') ?>" class="rounded-2xl border border-violet-900/40 bg-slate-900/50 p-6">
  <?= csrf_field() ?>
  <div class="space-y-4">
    <div>
      <label class="label">Usuario</label>
      <input type="text" name="username" required class="input-field" autocomplete="username">
    </div>
    <div>
      <label class="label">Contraseña</label>
      <input type="password" name="password" required class="input-field" autocomplete="current-password">
    </div>
    <button type="submit" class="w-full rounded-lg bg-violet-600 py-2.5 font-medium hover:bg-violet-500">Ingresar a plataforma</button>
  </div>
</form>
