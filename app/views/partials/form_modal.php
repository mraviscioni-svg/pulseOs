<?php
/**
 * @var string $modalTitle
 * @var string|null $modalSubtitle
 * @var string $closeUrl
 */
?>
<div class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal-panel <?= !empty($modalWide) ? '!max-w-xl' : '' ?>">
    <div class="modal-header">
      <div class="modal-header-text">
        <h3 id="modal-title"><?= e($modalTitle) ?></h3>
        <?php if (!empty($modalSubtitle)): ?>
        <p><?= e($modalSubtitle) ?></p>
        <?php endif; ?>
      </div>
      <a href="<?= e($closeUrl) ?>" class="modal-close" aria-label="Cerrar">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </a>
    </div>
    <div class="modal-body">
      <?= $modalContent ?? '' ?>
    </div>
  </div>
</div>
