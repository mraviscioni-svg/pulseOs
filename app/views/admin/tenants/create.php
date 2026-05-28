<div class="mb-4">
  <a href="<?= url('/admin/tenants') ?>" class="text-sm text-violet-400 hover:underline">← Volver al listado</a>
</div>

<div class="card max-w-2xl">
  <h2 class="mb-1 text-lg font-semibold">Alta de comercio</h2>
  <p class="mb-6 text-sm text-slate-400">Solo el administrador de plataforma puede crear tenants. El usuario owner ingresará en el login de comercios.</p>

  <form method="post" action="<?= url('/admin/tenants') ?>" class="space-y-4">
    <?= csrf_field() ?>
    <div>
      <label class="label">Nombre del negocio</label>
      <input name="company_name" value="<?= e(old('company_name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">Nombre del responsable (owner)</label>
      <input name="owner_name" value="<?= e(old('owner_name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">Usuario de acceso</label>
      <input name="username" value="<?= e(old('username')) ?>" required minlength="3" pattern="[a-zA-Z0-9._-]+"
        class="input-field" autocomplete="off" placeholder="ej: taller.boedo">
      <p class="mt-1 text-xs text-slate-500">Único en todo PulseOS.</p>
    </div>
    <div>
      <label class="label">Rubro</label>
      <select name="business_type" required class="input-field">
        <?php foreach ($businessTypes as $key => $type): ?>
        <option value="<?= e($key) ?>" <?= old('business_type', 'otro') === $key ? 'selected' : '' ?>><?= e($type['label']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="label">Email de contacto</label>
        <input type="email" name="email" value="<?= e(old('email')) ?>" required class="input-field">
      </div>
      <div>
        <label class="label">Teléfono</label>
        <input name="phone" value="<?= e(old('phone')) ?>" class="input-field">
      </div>
    </div>
    <div>
      <label class="label">Contraseña inicial del owner</label>
      <input type="password" name="password" required minlength="8" class="input-field" autocomplete="new-password">
    </div>
    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn-primary">Crear comercio</button>
      <a href="<?= url('/admin/tenants') ?>" class="rounded-lg border border-slate-700 px-4 py-2 hover:bg-slate-900">Cancelar</a>
    </div>
  </form>
</div>
