<?php
/** @var array<string, mixed>|null $brand */
$isEdit = !empty($brand);
$field = static function (string $key, mixed $default = '') use ($brand): string {
    $v = old($key);
    if ($v !== '' && $v !== null) {
        return (string) $v;
    }
    if ($brand !== null && array_key_exists($key, $brand)) {
        return (string) $brand[$key];
    }

    return (string) $default;
};
?>
<form method="post" action="<?= $isEdit ? url('/brands/' . $brand['id']) : url('/brands') ?>">
  <?= csrf_field() ?>
  <div class="modal-form-grid">
    <div class="sm:col-span-2">
      <label class="label">Nombre</label>
      <input name="name" value="<?= e($field('name')) ?>" required class="input-field" placeholder="Ej: Samsung, Arcor…">
    </div>
  </div>
  <div class="modal-form-footer">
    <a href="<?= url('/brands') ?>" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
  </div>
</form>
