<form method="post" action="<?= url('/register') ?>" class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6 shadow-xl">
  <?= csrf_field() ?>
  <div class="space-y-4">
    <div>
      <label class="mb-1 block text-sm text-slate-400">Nombre del negocio</label>
      <input name="company_name" value="<?= e(old('company_name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="mb-1 block text-sm text-slate-400">Tu nombre</label>
      <input name="owner_name" value="<?= e(old('owner_name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="mb-1 block text-sm text-slate-400">Usuario de acceso</label>
      <input name="username" value="<?= e(old('username')) ?>" required minlength="3" pattern="[a-zA-Z0-9._-]+"
        class="input-field" autocomplete="username" placeholder="ej: juan.kiosco">
      <p class="mt-1 text-xs text-slate-500">Único en todo PulseOS. Solo letras, números, . _ -</p>
    </div>
    <div>
      <label class="mb-1 block text-sm text-slate-400">Rubro</label>
      <select name="business_type" required class="input-field">
        <?php foreach ($businessTypes as $key => $type): ?>
        <option value="<?= e($key) ?>" <?= old('business_type') === $key ? 'selected' : '' ?>><?= e($type['label']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="mb-1 block text-sm text-slate-400">Email de contacto</label>
      <input type="email" name="email" value="<?= e(old('email')) ?>" required class="input-field">
    </div>
    <div>
      <label class="mb-1 block text-sm text-slate-400">Teléfono</label>
      <input name="phone" value="<?= e(old('phone')) ?>" class="input-field">
    </div>
    <div>
      <label class="mb-1 block text-sm text-slate-400">Contraseña</label>
      <input type="password" name="password" required minlength="8" class="input-field">
    </div>
    <button type="submit" class="w-full rounded-lg bg-pulse-600 py-2.5 font-medium hover:bg-pulse-700">Crear cuenta</button>
  </div>
  <p class="mt-4 text-center text-sm text-slate-500"><a href="<?= url('/login') ?>" class="text-pulse-400">Ya tengo cuenta</a></p>
</form>
