<form method="post" action="<?= url('/login') ?>" class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6 shadow-xl">
  <?= csrf_field() ?>
  <div class="space-y-4">
    <div>
      <label class="mb-1 block text-sm text-slate-400">Usuario</label>
      <input type="text" name="username" value="<?= e(old('username')) ?>" required autocomplete="username"
        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 focus:border-pulse-500 focus:outline-none"
        pattern="[a-zA-Z0-9._-]{3,}" title="Mínimo 3 caracteres: letras, números, punto, guión">
    </div>
    <div>
      <label class="mb-1 block text-sm text-slate-400">Contraseña</label>
      <input type="password" name="password" required autocomplete="current-password"
        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 focus:border-pulse-500 focus:outline-none">
    </div>
    <button type="submit" class="w-full rounded-lg bg-pulse-600 py-2.5 font-medium hover:bg-pulse-700">Ingresar</button>
  </div>
  <p class="mt-4 text-center text-sm text-slate-500">
    <a href="<?= url('/forgot-password') ?>" class="text-pulse-400 hover:underline">¿Olvidaste tu contraseña?</a>
  </p>
  <p class="mt-2 text-center text-sm text-slate-500">
    <a href="<?= url('/register') ?>" class="text-pulse-400 hover:underline">Registrar mi negocio</a>
  </p>
  <p class="mt-2 text-center text-xs text-slate-600">
    <a href="<?= url('/admin/login') ?>" class="hover:text-slate-400">Administración plataforma</a>
  </p>
</form>
