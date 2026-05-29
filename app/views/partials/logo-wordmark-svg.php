<?php
/**
 * Wordmark variante A: Pulse (navy) + OS (dorado) en fondo claro.
 * @var string $logoTheme light|dark
 * @var string $logoSize sm|md|lg
 */
$palette = brand_palette($logoTheme ?? null);

$viewBox = match ($logoSize ?? 'sm') {
    'lg' => '0 0 118 36',
    'md' => '0 0 106 32',
    default => '0 0 96 28',
};
$fontSize = match ($logoSize ?? 'sm') {
    'lg' => '28',
    'md' => '24',
    default => '20',
};
$y = match ($logoSize ?? 'sm') {
    'lg' => '28',
    'md' => '24',
    default => '21',
};
?>
<svg class="brand-wordmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="<?= $viewBox ?>" fill="none" aria-hidden="true">
  <text x="0" y="<?= $y ?>" font-family="Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif" font-size="<?= $fontSize ?>" font-weight="700" letter-spacing="-0.02em">
    <tspan fill="<?= $palette['word_pulse'] ?>">Pulse</tspan><tspan fill="<?= $palette['word_os'] ?>">OS</tspan>
  </text>
</svg>
