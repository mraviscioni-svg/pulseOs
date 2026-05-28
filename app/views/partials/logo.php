<?php
/** @var string $logoSize sm|md|lg */
/** @var bool $logoIconOnly */
/** @var string|null $logoHref */
/** @var string $logoClass */
/** @var string $logoTagline */
/** @var string $logoAlign center|left */

$size = $logoSize ?? 'md';
$iconOnly = $logoIconOnly ?? false;
$href = $logoHref ?? null;
$tagline = $logoTagline ?? null;
$align = $logoAlign ?? 'center';

$heights = ['sm' => 'h-8', 'md' => 'h-10', 'lg' => 'h-14'];
$height = $heights[$size] ?? $heights['md'];

$src = $iconOnly ? asset('images/logo-icon.svg') : asset('images/logo.svg');
$maxW = $iconOnly ? 'max-w-[3.5rem]' : 'max-w-[220px]';

if ($align === 'left') {
    $imgClass = trim("$height w-auto $maxW object-contain object-left");
    $wrapClass = 'inline-flex flex-col items-start ' . ($logoClass ?? '');
} else {
    $imgClass = trim("$height w-auto $maxW object-contain mx-auto block");
    $wrapClass = 'flex w-full flex-col items-center text-center ' . ($logoClass ?? '');
}

$inner = '<img src="' . e($src) . '" alt="PulseOS" class="' . e($imgClass) . '" width="' . ($iconOnly ? '56' : '212') . '" height="' . ($iconOnly ? '56' : '52') . '">';

?>
<?php if ($href !== null): ?>
<a href="<?= url($href) ?>" class="<?= e(trim($wrapClass . ' hover:opacity-90 transition-opacity')) ?>">
  <?= $inner ?>
  <?php if ($tagline): ?><span class="mt-2 text-sm text-slate-400"><?= e($tagline) ?></span><?php endif; ?>
</a>
<?php else: ?>
<div class="<?= e(trim($wrapClass)) ?>">
  <?= $inner ?>
  <?php if ($tagline): ?><p class="mt-2 text-sm text-slate-400"><?= e($tagline) ?></p><?php endif; ?>
</div>
<?php endif; ?>
