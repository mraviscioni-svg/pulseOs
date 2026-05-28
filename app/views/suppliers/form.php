<?php $isEdit = !empty($supplier); ?>
<form method="post" action="<?= $isEdit ? url('/suppliers/' . $supplier['id']) : url('/suppliers') ?>" class="card max-w-xl space-y-4">
  <?= csrf_field() ?>
  <div><label class="label">Nombre *</label><input name="name" value="<?= e($supplier['name'] ?? '') ?>" required class="input-field"></div>
  <div><label class="label">Empresa</label><input name="company" value="<?= e($supplier['company'] ?? '') ?>" class="input-field"></div>
  <div><label class="label">CUIT</label><input name="tax_id" value="<?= e($supplier['tax_id'] ?? '') ?>" class="input-field"></div>
  <div><label class="label">Email</label><input type="email" name="email" value="<?= e($supplier['email'] ?? '') ?>" class="input-field"></div>
  <div><label class="label">Teléfono</label><input name="phone" value="<?= e($supplier['phone'] ?? '') ?>" class="input-field"></div>
  <div><label class="label">Dirección</label><textarea name="address" class="input-field"><?= e($supplier['address'] ?? '') ?></textarea></div>
  <div><label class="label">Observaciones</label><textarea name="notes" class="input-field"><?= e($supplier['notes'] ?? '') ?></textarea></div>
  <?php if ($isEdit): ?>
  <label class="inline-flex gap-2"><input type="checkbox" name="is_active" value="1" <?= $supplier['is_active'] ? 'checked' : '' ?>> Activo</label>
  <?php endif; ?>
  <button class="btn-primary">Guardar</button>
</form>
