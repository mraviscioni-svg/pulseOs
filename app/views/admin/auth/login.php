<form method="post" action="<?= url('/admin/login') ?>" class="form-card">
  <?= csrf_field() ?>
  <p class="page-eyebrow">Platform</p>
  <h2 class="mt-1 text-xl font-bold text-navy-900">Ingresar a plataforma</h2>
  <p class="mb-6 mt-1 text-sm text-slate-500">Administración de comercios PulseOS.</p>
  <div class="space-y-4">
    <div>
      <label class="label">Usuario</label>
      <input type="text" name="username" required class="input-field" autocomplete="username">
    </div>
    <div>
      <label class="label">Contraseña</label>
      <input type="password" name="password" required class="input-field" autocomplete="current-password">
    </div>
    <button type="submit" class="btn-primary w-full">Ingresar a plataforma</button>
  </div>
</form>
