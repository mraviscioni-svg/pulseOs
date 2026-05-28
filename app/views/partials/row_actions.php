<?php
/**
 * @var string|null $editUrl
 * @var string|null $toggleUrl
 * @var bool $isActive
 * @var string|null $deleteUrl
 * @var string|null $deleteConfirm
 * @var list<array{url: string, label: string, class?: string}> $extra
 */
$isActive = $isActive ?? true;
$deleteConfirm = $deleteConfirm ?? '¿Eliminar este registro?';
$extra = $extra ?? [];
?>
<div class="row-actions">
  <?php if (!empty($editUrl)): ?>
  <a href="<?= e($editUrl) ?>" class="btn-action btn-action-edit" title="Editar">Editar</a>
  <?php endif; ?>
  <?php if (!empty($toggleUrl)): ?>
  <form method="post" action="<?= e($toggleUrl) ?>" class="inline">
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
  <form method="post" action="<?= e($deleteUrl) ?>" class="inline" onsubmit="return confirm(<?= json_encode($deleteConfirm, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);">
    <?= csrf_field() ?>
    <button type="submit" class="btn-action btn-action-danger" title="Eliminar">Eliminar</button>
  </form>
  <?php endif; ?>
</div>
