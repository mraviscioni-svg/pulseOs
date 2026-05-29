<?php
/**
 * Marca PulseOS sobre fondo blanco: badge navy + Pulse (navy) + OS (dorado).
 */
$size = $logoSize ?? 'md';
$iconOnly = $logoIconOnly ?? false;
$href = $logoHref ?? null;
$tagline = $logoTagline ?? null;
$align = $logoAlign ?? 'center';
$layout = $logoLayout ?? ($align === 'left' ? 'inline' : 'stacked');

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

$wrapClass = trim("brand-lockup $layoutClass " . ($logoClass ?? ''));

ob_start();
require __DIR__ . '/logo-mark-svg.php';
$markSvg = ob_get_clean();

$mark = '<span class="' . e($markWrapClass) . '">' . $markSvg . '</span>';

$pulseStyle = 'color:#0c1524';
$osStyle = 'color:#E8B44A';

$wordmark = $iconOnly
    ? ''
    : '<span class="' . e($wordClass) . '" aria-label="' . e($brandName) . '">'
        . '<span class="brand-pulse" style="' . $pulseStyle . '">Pulse</span>'
        . '<span class="brand-os" style="' . $osStyle . '">OS</span>'
        . '</span>';

$taglineHtml = $tagline && !$iconOnly
    ? '<span class="brand-tagline" style="color:#64748b">' . e($tagline) . '</span>'
    : '';

$inner = $mark . $wordmark . $taglineHtml;
?>
<?php if ($href !== null): ?>
<a href="<?= url($href) ?>" class="<?= e(trim($wrapClass . ' brand-link')) ?>" title="<?= e($brandName) ?>" style="color:#0c1524;text-decoration:none">
  <?= $inner ?>
</a>
<?php else: ?>
<div class="<?= e($wrapClass) ?>" style="color:#0c1524">
  <?= $inner ?>
</div>
<?php endif; ?>
