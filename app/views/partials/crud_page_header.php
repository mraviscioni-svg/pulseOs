<?php
/**
 * @var string|null $pageEyebrow
 * @var string|null $pageTitle
 * @var string|null $pageDescription
 * @var string|null $createUrl
 * @var string|null $createLabel
 */
$pageTitle = $pageTitle ?? ($title ?? '');
?>
<div class="page-header">
  <div class="page-header-main">
    <?php if (!empty($pageEyebrow)): ?>
    <p class="page-eyebrow"><?= e($pageEyebrow) ?></p>
    <?php endif; ?>
    <h2><?= e($pageTitle) ?></h2>
    <?php if (!empty($pageDescription)): ?>
    <p><?= e($pageDescription) ?></p>
    <?php endif; ?>
  </div>
  <?php if (!empty($createUrl)): ?>
  <div class="page-header-actions">
    <a href="<?= e($createUrl) ?>" class="btn-primary"><?= e($createLabel ?? '+ Nuevo') ?></a>
  </div>
  <?php endif; ?>
</div>
