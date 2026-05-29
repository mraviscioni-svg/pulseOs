<?php
/**
 * @var string $modalId
 * @var array<string, mixed>|null $tenant
 */
$modalId = $modalId ?? 'delete-tenant-modal';
$slug = (string) ($tenant['slug'] ?? '');
$tenantId = (int) ($tenant['id'] ?? 0);
$action = $tenantId > 0 ? url('/admin/tenants/' . $tenantId . '/delete') : '';
?>
<div id="<?= e($modalId) ?>" class="modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="delete-tenant-title" aria-hidden="true">
  <div class="modal-panel modal-panel-danger">
    <div class="modal-header">
      <div class="modal-header-text">
        <h3 id="delete-tenant-title">Eliminar comercio</h3>
        <p>Se borrarán usuarios, productos, ventas y todo lo asociado. Esta acción no se puede deshacer.</p>
      </div>
      <button type="button" class="modal-close" data-dismiss-modal aria-label="Cerrar">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <form method="post" action="<?= e($action) ?>" data-tenant-delete-form>
        <?= csrf_field() ?>
        <p class="mb-3 text-sm text-slate-600">
          Para confirmar, escribí el slug del comercio:
          <strong class="text-navy-900" data-tenant-slug-display><?= e($slug) ?></strong>
        </p>
        <label class="label" for="confirm_slug_<?= e($modalId) ?>">Slug</label>
        <input type="text" id="confirm_slug_<?= e($modalId) ?>" name="confirm_slug" class="input-field mb-4" placeholder="<?= e($slug) ?>" autocomplete="off" required>
        <div class="modal-form-footer !mt-0 !border-0 !pt-0">
          <button type="button" class="btn-secondary" data-dismiss-modal>Cancelar</button>
          <button type="submit" class="btn-danger">Eliminar definitivamente</button>
        </div>
      </form>
    </div>
  </div>
</div>
