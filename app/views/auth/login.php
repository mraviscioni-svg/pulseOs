<form method="post" action="<?= url('/login') ?>" class="form-card shadow-xl">
  <?= csrf_field() ?>
  <h2 class="mb-1 text-lg font-semibold text-white">Ingresá a tu comercio</h2>
  <p class="mb-6 text-sm text-slate-400">Usuario y contraseña que te dio el administrador.</p>

  <div class="space-y-4">
    <?php
    $name = 'username'; $label = 'Usuario'; $type = 'input'; $value = old('username');
    $required = true; $placeholder = 'ej: taller.boedo';
    $hint = 'Mínimo 3 caracteres: letras, números, punto o guión.';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'password'; $label = 'Contraseña'; $type = 'password'; $value = '';
    $required = true; $hint = null;
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <button type="submit" class="btn-primary w-full">Ingresar</button>
  </div>

  <p class="mt-5 text-center text-sm text-slate-500">
    <a href="<?= url('/forgot-password') ?>" class="text-indigo-400 hover:underline">¿Olvidaste tu contraseña?</a>
  </p>
  <p class="mt-4 text-center text-xs text-slate-500">
    El alta de comercios la gestiona el administrador de la plataforma.
  </p>
  <p class="mt-2 text-center text-xs text-slate-600">
    <a href="<?= url('/admin/login') ?>" class="hover:text-slate-400">Administración plataforma</a>
  </p>
</form>
