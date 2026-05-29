<?php
/**
 * Marca PulseOS — variante A (fondos claros en toda la app).
 * Isotipo: pulso dorado + edificio navy. Wordmark: Pulse navy + OS dorado.
 */
$brand = config('brand');
$size = $logoSize ?? 'md';
$logoSize = $size;
$iconOnly = $logoIconOnly ?? false;
$href = $logoHref ?? null;
$tagline = $logoTagline ?? null;
$align = $logoAlign ?? 'center';
$layout = $logoLayout ?? ($align === 'left' ? 'inline' : 'stacked');
$logoTheme = $logoTheme ?? (string) ($brand['default_theme'] ?? 'light');

$brandName = app_name();

$markWrapClass = match ($size) {
    'lg' => 'brand-mark-wrap brand-mark-wrap-lg',
    'md' => 'brand-mark-wrap brand-mark-wrap-md',
    default => 'brand-mark-wrap',
};
$wordClass = match ($size) {
    'lg' => 'brand-wordmark brand-wordmark-lg',
    'md' => 'brand-wordmark brand-wordmark-md',
    default => 'brand-wordmark brand-wordmark-sm',
};

$layoutClass = $layout === 'inline'
    ? 'brand-inline'
    : 'brand-stacked ' . ($align === 'center' ? '' : 'items-start text-left');

$isLight = $logoTheme !== 'dark';
$themeClass = $isLight ? 'brand-theme-light brand-variant-a' : 'brand-theme-dark';
$wrapClass = trim("brand-lockup $themeClass $layoutClass " . ($logoClass ?? ''));

ob_start();
require __DIR__ . '/logo-mark-svg.php';
$markSvg = ob_get_clean();

$mark = '<span class="' . e($markWrapClass) . '">' . $markSvg . '</span>';

ob_start();
require __DIR__ . '/logo-wordmark-svg.php';
$wordmarkSvg = ob_get_clean();

$wordmark = $iconOnly
    ? ''
    : '<span class="' . e($wordClass) . '" aria-label="' . e($brandName) . '">' . $wordmarkSvg . '</span>';

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
