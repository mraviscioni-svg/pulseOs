<?php
$pageEyebrow = 'Configuración';
$pageTitle = 'Comercio';
$pageDescription = 'Nombre público del comercio y usuarios con acceso al panel. Los cambios aplican a todo el equipo.';
require __DIR__ . '/../partials/crud_page_header.php';
?>

<div class="grid gap-4 sm:grid-cols-2 xl:max-w-3xl">
  <?php
  $href = url('/settings/comercio');
  $title = 'Datos del comercio';
  $description = 'Nombre visible, CUIT, teléfono y dirección de tu negocio.';
  $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>';
  require __DIR__ . '/../partials/settings_nav_card.php';

  if (can('users.manage')):
  $href = url('/users');
  $title = 'Equipo';
  $description = 'Alta de usuarios, roles y activación del personal.';
  $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>';
  require __DIR__ . '/../partials/settings_nav_card.php';
  endif;

  $href = url('/settings/operacion');
  $title = 'Operación';
  $description = 'Moneda, IVA, tickets POS y alertas de stock bajo.';
  $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>';
  require __DIR__ . '/../partials/settings_nav_card.php';
  ?>
</div>

<div class="mt-6 max-w-3xl rounded-2xl border border-slate-200/80 bg-white p-5 text-sm text-slate-500 shadow-card">
  Los módulos del sistema (POS, compras, reportes, etc.) los habilita el administrador de <strong class="text-navy-900"><?= e(app_name()) ?></strong>.
</div>
