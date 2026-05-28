<?php /** @var string $message @var string|null $actionUrl @var string|null $actionLabel */ ?>
<div class="empty-state">
  <p><?= e($message) ?></p>
  <?php if (!empty($actionUrl)): ?>
  <a href="<?= e($actionUrl) ?>" class="btn-primary mt-4"><?= e($actionLabel ?? 'Crear') ?></a>
  <?php endif; ?>
</div>
