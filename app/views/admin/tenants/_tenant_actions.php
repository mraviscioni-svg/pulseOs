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
$iconPause = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
$iconPlay = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
$iconTrash = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
$iconManage = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>';
?>

<?php if ($context === 'row'): ?>
<div class="action-group" role="group" aria-label="Acciones del comercio">
  <?php if ($showManage): ?>
  <a href="<?= url('/admin/tenants/' . $id) ?>" class="action-group-btn" title="Gestionar comercio">
    <?= $iconManage ?>
    <span class="sr-only">Gestionar</span>
  </a>
  <?php endif; ?>

  <form method="post" action="<?= url('/admin/tenants/' . $id . '/toggle') ?>"
    data-confirm-title="<?= $isActive ? 'Suspender comercio' : 'Activar comercio' ?>"
    data-confirm-message="<?= $isActive ? 'El comercio no podrá ingresar hasta que lo reactives.' : '¿Reactivar este comercio?' ?>"
    data-confirm-label="<?= $isActive ? 'Suspender' : 'Activar' ?>"
    data-confirm-danger="<?= $isActive ? '1' : '0' ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="return" value="list">
    <input type="hidden" name="is_active" value="<?= $isActive ? '0' : '1' ?>">
    <button type="submit" class="action-group-btn <?= $isActive ? 'action-group-btn-warn' : 'action-group-btn-ok' ?>" title="<?= e($toggleLabel) ?>">
      <?= $isActive ? $iconPause : $iconPlay ?>
      <span class="sr-only"><?= e($toggleLabel) ?></span>
    </button>
  </form>

  <button type="button" class="action-group-btn action-group-btn-danger"
    data-open-modal="delete-tenant-modal"
    data-tenant-slug="<?= e($slug) ?>"
    data-delete-url="<?= url('/admin/tenants/' . $id . '/delete') ?>"
    title="<?= e($deleteLabel) ?>">
    <?= $iconTrash ?>
    <span class="sr-only"><?= e($deleteLabel) ?></span>
  </button>
</div>
<?php else: ?>
<div class="tenant-actions-bar">
  <form method="post" action="<?= url('/admin/tenants/' . $id . '/toggle') ?>"
    data-confirm-title="<?= $isActive ? 'Suspender comercio' : 'Activar comercio' ?>"
    data-confirm-message="<?= $isActive ? 'El comercio no podrá ingresar hasta que lo reactives.' : '¿Reactivar este comercio?' ?>"
    data-confirm-label="<?= $isActive ? 'Suspender' : 'Activar' ?>"
    data-confirm-danger="<?= $isActive ? '1' : '0' ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="is_active" value="<?= $isActive ? '0' : '1' ?>">
    <button type="submit" class="tenant-action-btn <?= $isActive ? 'tenant-action-btn-warn' : 'tenant-action-btn-ok' ?>">
      <?= $isActive ? $iconPause : $iconPlay ?>
      <?= e($toggleLabel) ?>
    </button>
  </form>

  <button type="button" class="tenant-action-btn tenant-action-btn-danger"
    data-open-modal="delete-tenant-modal"
    data-tenant-slug="<?= e($slug) ?>"
    data-delete-url="<?= url('/admin/tenants/' . $id . '/delete') ?>">
    <?= $iconTrash ?>
    <?= e($deleteLabel) ?>
  </button>
</div>
<?php endif; ?>
