<?php $isEdit = !empty($supplier); ?>
<div class="page-header">
  <h2><?= e($title ?? ($isEdit ? 'Editar proveedor' : 'Nuevo proveedor')) ?></h2>
  <p><a href="<?= url('/suppliers') ?>" class="text-indigo-400 hover:underline">← Volver al listado</a></p>
</div>

<form method="post" action="<?= $isEdit ? url('/suppliers/' . $supplier['id']) : url('/suppliers') ?>" class="form-card max-w-xl">
  <?= csrf_field() ?>
  <div class="form-section space-y-4">
    <?php
    $name = 'name'; $label = 'Nombre'; $type = 'input'; $value = $supplier['name'] ?? ''; $required = true;
    require __DIR__ . '/../partials/form_group.php';
    $name = 'company'; $label = 'Empresa'; $type = 'input'; $value = $supplier['company'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'tax_id'; $label = 'CUIT'; $type = 'input'; $value = $supplier['tax_id'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'email'; $label = 'Email'; $type = 'email'; $value = $supplier['email'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'phone'; $label = 'Teléfono'; $type = 'input'; $value = $supplier['phone'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'address'; $label = 'Dirección'; $type = 'textarea'; $value = $supplier['address'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    $name = 'notes'; $label = 'Observaciones'; $type = 'textarea'; $value = $supplier['notes'] ?? '';
    require __DIR__ . '/../partials/form_group.php';
    ?>
    <?php if ($isEdit): ?>
    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-800 px-4 py-3">
      <input type="checkbox" name="is_active" value="1" class="h-4 w-4" <?= $supplier['is_active'] ? 'checked' : '' ?>>
      <span class="text-sm">Proveedor activo</span>
    </label>
    <?php endif; ?>
  </div>
  <div class="form-actions mt-6">
    <button type="submit" class="btn-primary">Guardar</button>
    <a href="<?= url('/suppliers') ?>" class="btn-secondary">Cancelar</a>
  </div>
</form>
