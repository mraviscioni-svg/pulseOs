<?php
/**
 * Isotipo PulseOS — proporciones balanceadas (más ancho, pico bajo, edificio cuadrado).
 * @var string $logoTheme light|dark
 */
$palette = brand_palette($logoTheme ?? null);
$g = config('brand_isotype');
$sw = (string) $g['stroke'];
$sd = (string) $g['stroke_detail'];
$b = $g['building'];
$d = $g['door'];
$dot = $g['pulse_dot'];
?>
<svg class="brand-mark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="<?= e($g['viewBox']) ?>" fill="none" aria-hidden="true">
  <path d="<?= e($g['pulse_path']) ?>" stroke="<?= $palette['pulse'] ?>" stroke-width="<?= $sw ?>" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="<?= $dot['cx'] ?>" cy="<?= $dot['cy'] ?>" r="<?= $dot['r'] ?>" fill="<?= $palette['pulse'] ?>"/>
  <rect x="<?= $b['x'] ?>" y="<?= $b['y'] ?>" width="<?= $b['w'] ?>" height="<?= $b['h'] ?>" rx="<?= $b['rx'] ?>" stroke="<?= $palette['structure'] ?>" stroke-width="<?= $sw ?>"/>
  <?php foreach ($g['windows'] as $w): ?>
  <rect x="<?= $w['x'] ?>" y="<?= $w['y'] ?>" width="<?= $w['w'] ?>" height="<?= $w['h'] ?>" rx="0.5" stroke="<?= $palette['structure'] ?>" stroke-width="<?= $sd ?>"/>
  <?php endforeach; ?>
  <rect x="<?= $d['x'] ?>" y="<?= $d['y'] ?>" width="<?= $d['w'] ?>" height="<?= $d['h'] ?>" rx="1" stroke="<?= $palette['structure'] ?>" stroke-width="<?= $sd ?>"/>
  <path d="<?= e($g['building_line']) ?>" stroke="<?= $palette['pulse'] ?>" stroke-width="<?= $sw ?>" stroke-linecap="round"/>
</svg>
