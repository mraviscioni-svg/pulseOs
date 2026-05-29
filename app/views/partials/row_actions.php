<?php
/**
 * @var string|null $editUrl
 * @var string|null $toggleUrl
 * @var bool $isActive
 * @var string|null $deleteUrl
 * @var string|null $deleteConfirm
 * @var string|null $toggleConfirm
 * @var list<array{url: string, label: string, class?: string}> $extra
 */
$isActive = $isActive ?? true;
$deleteConfirm = $deleteConfirm ?? '¿Eliminar este registro?';
$toggleConfirm = $toggleConfirm ?? null;
$extra = $extra ?? [];
?>
<div class="row-actions">
  <?php if (!empty($editUrl)): ?>
  <a href="<?= e($editUrl) ?>" class="btn-action btn-action-edit" title="Editar">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
    Editar
  </a>
  <?php endif; ?>
  <?php if (!empty($toggleUrl)): ?>
  <form method="post" action="<?= e($toggleUrl) ?>" class="inline"
    <?php if ($toggleConfirm): ?>
    data-confirm-title="Confirmar" data-confirm-message="<?= e($toggleConfirm) ?>" data-confirm-label="<?= $isActive ? 'Desactivar' : 'Activar' ?>" data-confirm-danger="<?= $isActive ? '1' : '0' ?>"
    <?php endif; ?>>
    <?= csrf_field() ?>
    <input type="hidden" name="is_active" value="<?= $isActive ? '0' : '1' ?>">
    <button type="submit" class="btn-action <?= $isActive ? 'btn-action-warn' : 'btn-action-ok' ?>" title="<?= $isActive ? 'Desactivar' : 'Activar' ?>">
      <?= $isActive ? 'Desactivar' : 'Activar' ?>
    </button>
  </form>
  <?php endif; ?>
  <?php foreach ($extra as $action): ?>
  <a href="<?= e($action['url']) ?>" class="btn-action <?= e($action['class'] ?? '') ?>"><?= e($action['label']) ?></a>
  <?php endforeach; ?>
  <?php if (!empty($deleteUrl)): ?>
  <form method="post" action="<?= e($deleteUrl) ?>" class="inline"
    data-confirm-title="Eliminar" data-confirm-message="<?= e($deleteConfirm) ?>" data-confirm-label="Eliminar" data-confirm-danger="1">
    <?= csrf_field() ?>
    <button type="submit" class="btn-action btn-action-danger" title="Eliminar">Eliminar</button>
  </form>
  <?php endif; ?>
</div>
