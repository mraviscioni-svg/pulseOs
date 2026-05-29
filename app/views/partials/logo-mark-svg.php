<?php
/**
 * Isotipo PulseOS — variante A en fondo claro: pulso dorado + edificio navy.
 * @var string $logoTheme light|dark
 */
$palette = brand_palette($logoTheme ?? null);
?>
<svg class="brand-mark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 52" fill="none" aria-hidden="true">
  <path d="M4 34 H14 L18 16 L22 34 H26" stroke="<?= $palette['pulse'] ?>" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="18" cy="10" r="2.5" fill="<?= $palette['pulse'] ?>"/>
  <path d="M26 34 H30" stroke="<?= $palette['pulse'] ?>" stroke-width="2.5" stroke-linecap="round"/>
  <rect x="28" y="14" width="32" height="36" rx="7" stroke="<?= $palette['structure'] ?>" stroke-width="2"/>
  <rect x="40" y="38" width="8" height="12" rx="1" stroke="<?= $palette['structure'] ?>" stroke-width="1.5"/>
  <rect x="32" y="24" width="7" height="7" rx="1" stroke="<?= $palette['structure'] ?>" stroke-width="1.5"/>
  <rect x="49" y="24" width="7" height="7" rx="1" stroke="<?= $palette['structure'] ?>" stroke-width="1.5"/>
  <path d="M30 32 H58" stroke="<?= $palette['pulse'] ?>" stroke-width="2.5" stroke-linecap="round"/>
</svg>
