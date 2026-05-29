<?php
/**
 * Marca PulseOS: ícono SVG + nombre en HTML (tipografía Inter, siempre «PulseOS»).
 *
 * @var string $logoSize sm|md|lg
 * @var bool $logoIconOnly solo ícono
 * @var string|null $logoHref
 * @var string $logoClass
 * @var string|null $logoTagline
 * @var string $logoAlign left|center
 * @var string $logoVariant light|dark  light = fondos claros, dark = fondos navy
 * @var string $logoLayout inline|stacked
 */
$size = $logoSize ?? 'md';
$iconOnly = $logoIconOnly ?? false;
$href = $logoHref ?? null;
$tagline = $logoTagline ?? null;
$align = $logoAlign ?? 'center';
$variant = $logoVariant ?? 'light';
$layout = $logoLayout ?? ($align === 'left' ? 'inline' : 'stacked');

$brandName = app_name();

$markHeights = ['sm' => 'brand-mark-sm', 'md' => 'brand-mark-md', 'lg' => 'brand-mark-lg'];
$wordSizes = ['sm' => 'brand-wordmark-sm', 'md' => 'brand-wordmark-md', 'lg' => 'brand-wordmark-lg'];
$markClass = $markHeights[$size] ?? $markHeights['md'];
$wordClass = $wordSizes[$size] ?? $wordSizes['md'];

$markSrc = asset($variant === 'dark' ? 'images/logo-mark-light.svg' : 'images/logo-mark.svg');

$layoutClass = $layout === 'inline'
    ? 'brand-inline'
    : 'brand-stacked ' . ($align === 'center' ? 'items-center text-center' : 'items-start text-left');

$wrapClass = trim("brand-lockup $layoutClass " . ($logoClass ?? ''));

$mark = '<img src="' . e($markSrc) . '" alt="" class="brand-mark ' . e($markClass) . '" width="56" height="48" decoding="async">';

$wordmark = $iconOnly ? '' : '<span class="brand-wordmark ' . e($wordClass) . '">' . e($brandName) . '</span>';

$taglineHtml = $tagline && !$iconOnly
    ? '<span class="brand-tagline">' . e($tagline) . '</span>'
    : '';

$inner = $mark . $wordmark . $taglineHtml;
?>
<?php if ($href !== null): ?>
<a href="<?= url($href) ?>" class="<?= e(trim($wrapClass . ' brand-link')) ?>" title="<?= e($brandName) ?>">
  <?= $inner ?>
</a>
<?php else: ?>
<div class="<?= e($wrapClass) ?>">
  <?= $inner ?>
</div>
<?php endif; ?>
