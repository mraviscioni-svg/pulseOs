<?php
/**
 * Marca PulseOS sobre fondo blanco: badge navy + Pulse (navy) + OS (dorado).
 *
 * @var string $logoSize sm|md|lg
 * @var bool $logoIconOnly
 * @var string|null $logoHref
 * @var string $logoClass
 * @var string|null $logoTagline
 * @var string $logoAlign left|center
 * @var string $logoLayout inline|stacked
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

$wordmark = $iconOnly
    ? ''
    : '<span class="' . e($wordClass) . '" aria-label="' . e($brandName) . '">'
        . '<span class="brand-pulse">Pulse</span><span class="brand-os">OS</span>'
        . '</span>';

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
