<?php
/**
 * @var array<string, mixed> $t
 * @var string $context 'row' | 'bar'
 * @var bool $showManage
 */
$context = $context ?? 'row';
$showManage = $showManage ?? ($context === 'row');
$isActive = (bool) $t['is_active'];
$id = (int) $t['id'];
$slug = (string) $t['slug'];
$toggleLabel = $isActive
    ? ($context === 'bar' ? 'Suspender comercio' : 'Suspender')
    : ($context === 'bar' ? 'Activar comercio' : 'Activar');
$deleteLabel = $context === 'bar' ? 'Eliminar comercio' : 'Eliminar';
$btnSize = $context === 'bar' ? 'btn-action-md' : '';
$wrapClass = $context === 'bar' ? 'tenant-action-bar' : 'row-actions';
$iconPause = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
$iconPlay = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
$iconTrash = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
$iconManage = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
?>
<div class="<?= e($wrapClass) ?>">
  <?php if ($showManage): ?>
  <a href="<?= url('/admin/tenants/' . $id) ?>" class="btn-action btn-action-edit <?= e($btnSize) ?>" title="Gestionar comercio">
    <?= $iconManage ?>
    Gestionar
  </a>
  <?php endif; ?>

  <form method="post" action="<?= url('/admin/tenants/' . $id . '/toggle') ?>" class="inline"
    data-confirm-title="<?= $isActive ? 'Suspender comercio' : 'Activar comercio' ?>"
    data-confirm-message="<?= $isActive ? 'El comercio no podrá ingresar hasta que lo reactives.' : '¿Reactivar este comercio?' ?>"
    data-confirm-label="<?= $isActive ? 'Suspender' : 'Activar' ?>"
    data-confirm-danger="<?= $isActive ? '1' : '0' ?>">
    <?= csrf_field() ?>
    <?php if ($context === 'row'): ?>
    <input type="hidden" name="return" value="list">
    <?php endif; ?>
    <input type="hidden" name="is_active" value="<?= $isActive ? '0' : '1' ?>">
    <button type="submit" class="btn-action <?= $isActive ? 'btn-action-warn' : 'btn-action-ok' ?> <?= e($btnSize) ?>" title="<?= e($toggleLabel) ?>">
      <?= $isActive ? $iconPause : $iconPlay ?>
      <?= e($toggleLabel) ?>
    </button>
  </form>

  <?php if ($context === 'bar'): ?>
  <span class="action-bar-sep" aria-hidden="true"></span>
  <?php endif; ?>

  <button type="button" class="btn-action btn-action-danger <?= e($btnSize) ?>"
    data-open-modal="delete-tenant-modal"
    data-tenant-slug="<?= e($slug) ?>"
    data-delete-url="<?= url('/admin/tenants/' . $id . '/delete') ?>"
    title="<?= e($deleteLabel) ?>">
    <?= $iconTrash ?>
    <?= e($deleteLabel) ?>
  </button>
</div>
