<?php
/**
 * @var string $href
 * @var string $title
 * @var string $description
 * @var string $iconSvg inner SVG paths (stroke icon)
 */
?>
<a href="<?= e($href) ?>" class="settings-nav-card group">
  <span class="settings-nav-card-icon" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><?= $iconSvg ?></svg>
  </span>
  <span class="settings-nav-card-body">
    <span class="settings-nav-card-title"><?= e($title) ?></span>
    <span class="settings-nav-card-desc"><?= e($description) ?></span>
  </span>
  <svg class="settings-nav-card-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
</a>
