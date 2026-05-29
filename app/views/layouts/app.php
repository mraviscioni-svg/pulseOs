<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>
<body class="min-h-full">
<?php require __DIR__ . '/../partials/sidebar.php'; ?>
<div class="min-h-screen pl-64">
  <?php if (is_platform_impersonating()): ?>
  <div class="impersonation-banner">
    <span>Estás viendo el panel como administrador de plataforma.</span>
    <form method="post" action="<?= url('/admin/stop-impersonate') ?>" class="inline">
      <?= csrf_field() ?>
      <button type="submit" class="btn-secondary !py-1.5 !text-xs">← Volver al admin</button>
    </form>
  </div>
  <?php elseif (has_dual_auth_sessions()): ?>
  <div class="impersonation-banner">
    <span>Sesión de comercio en paralelo con el admin de plataforma.</span>
    <a href="<?= url('/admin/tenants') ?>" class="btn-secondary !py-1.5 !text-xs" target="_blank" rel="noopener">Admin plataforma ↗</a>
  </div>
  <?php endif; ?>
  <main class="p-8">
    <?php require __DIR__ . '/../partials/flash.php'; ?>
    <?= $content ?>
  </main>
</div>
</body>
</html>
