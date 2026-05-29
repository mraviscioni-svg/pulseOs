<?php
/** @var array<string, mixed>|null $customer */
$isEdit = !empty($customer);
$field = static function (string $key, mixed $default = '') use ($customer): string {
    $v = old($key);
    if ($v !== '' && $v !== null) {
        return (string) $v;
    }
    if ($customer !== null && array_key_exists($key, $customer)) {
        return (string) $customer[$key];
    }

    return (string) $default;
};
$isActive = old('is_active') !== '' && old('is_active') !== null
    ? !empty(old('is_active'))
    : (bool) ($customer['is_active'] ?? true);
?>
<form method="post" action="<?= $isEdit ? url('/customers/' . $customer['id']) : url('/customers') ?>">
  <?= csrf_field() ?>
  <div class="modal-form-grid">
    <div class="sm:col-span-2">
      <label class="label">Nombre *</label>
      <input name="name" value="<?= e($field('name')) ?>" required class="input-field">
    </div>
    <div>
      <label class="label">Empresa</label>
      <input name="company" value="<?= e($field('company')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">CUIT / DNI</label>
      <input name="tax_id" value="<?= e($field('tax_id')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Email</label>
      <input type="email" name="email" value="<?= e($field('email')) ?>" class="input-field">
    </div>
    <div>
      <label class="label">Teléfono</label>
      <input name="phone" value="<?= e($field('phone')) ?>" class="input-field">
    </div>
    <div class="sm:col-span-2">
      <label class="label">Dirección</label>
      <textarea name="address" rows="2" class="input-field"><?= e($field('address')) ?></textarea>
    </div>
    <div class="sm:col-span-2">
      <label class="label">Observaciones</label>
      <textarea name="notes" rows="2" class="input-field"><?= e($field('notes')) ?></textarea>
    </div>
    <?php if ($isEdit): ?>
    <div class="sm:col-span-2">
      <label class="form-check">
        <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
        Cliente activo
      </label>
    </div>
    <?php endif; ?>
  </div>
  <div class="modal-form-footer">
    <a href="<?= url($isEdit ? '/customers/' . $customer['id'] : '/customers') ?>" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
  </div>
</form>
