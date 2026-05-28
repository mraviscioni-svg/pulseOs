<?php
/** @var string $logoSize sm|md|lg */
/** @var bool $logoIconOnly */
/** @var string|null $logoHref ruta interna o null sin enlace */
/** @var string $logoClass clases extra del contenedor */
/** @var string $logoTagline subtítulo opcional debajo del logo */

$size = $logoSize ?? 'md';
$iconOnly = $logoIconOnly ?? false;
$href = $logoHref ?? null;
$tagline = $logoTagline ?? null;

$heights = ['sm' => 'h-8', 'md' => 'h-10', 'lg' => 'h-12'];
$imgClass = ($heights[$size] ?? $heights['md']) . ' w-auto';

$inner = $iconOnly
    ? '<img src="' . e(asset('images/logo-icon.svg')) . '" alt="PulseOS" class="' . e($imgClass) . '">'
    : '<img src="' . e(asset('images/logo.svg')) . '" alt="PulseOS" class="' . e($imgClass) . ' max-w-[200px]">';

$wrapClass = 'inline-flex flex-col items-center ' . ($logoClass ?? '');
?>
<?php if ($href !== null): ?>
<a href="<?= url($href) ?>" class="<?= e(trim($wrapClass . ' hover:opacity-90 transition-opacity')) ?>">
  <?= $inner ?>
  <?php if ($tagline): ?><span class="mt-1 text-sm text-slate-400"><?= e($tagline) ?></span><?php endif; ?>
</a>
<?php else: ?>
<div class="<?= e(trim($wrapClass)) ?>">
  <?= $inner ?>
  <?php if ($tagline): ?><p class="mt-1 text-sm text-slate-400"><?= e($tagline) ?></p><?php endif; ?>
</div>
<?php endif; ?>
