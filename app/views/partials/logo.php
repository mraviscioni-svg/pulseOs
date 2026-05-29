<?php
/**
 * Marca PulseOS: badge (ícono) + wordmark «Pulse» + «OS».
 *
 * @var string $logoSize sm|md|lg
 * @var bool $logoIconOnly
 * @var string|null $logoHref
 * @var string $logoClass
 * @var string|null $logoTagline
 * @var string $logoAlign left|center
 * @var string $logoVariant light|dark
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

$markWrapSizes = ['sm' => 'brand-mark-wrap-sm', 'md' => 'brand-mark-wrap-md', 'lg' => 'brand-mark-wrap-lg'];
$wordSizes = ['sm' => 'brand-wordmark-sm', 'md' => 'brand-wordmark-md', 'lg' => 'brand-wordmark-lg'];
$markWrapClass = $markWrapSizes[$size] ?? $markWrapSizes['md'];
$wordClass = $wordSizes[$size] ?? $wordSizes['md'];

$markSrc = asset($variant === 'dark' ? 'images/logo-mark-light.svg' : 'images/logo-mark.svg');

$layoutClass = $layout === 'inline'
    ? 'brand-inline'
    : 'brand-stacked ' . ($align === 'center' ? 'items-center text-center' : 'items-start text-left');

$wrapClass = trim("brand-lockup $layoutClass " . ($logoClass ?? ''));

$mark = '<span class="brand-mark-wrap ' . e($markWrapClass) . '">'
    . '<img src="' . e($markSrc) . '" alt="" class="brand-mark" width="40" height="40" decoding="async">'
    . '</span>';

$wordmark = $iconOnly
    ? ''
    : '<span class="brand-wordmark ' . e($wordClass) . '" aria-label="' . e($brandName) . '">'
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
