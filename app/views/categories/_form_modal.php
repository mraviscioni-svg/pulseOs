<?php
/** @var array<string, mixed>|null $category */
$isEdit = !empty($category);
$field = static function (string $key, mixed $default = '') use ($category): string {
    $v = old($key);
    if ($v !== '' && $v !== null) {
        return (string) $v;
    }
    if ($category !== null && array_key_exists($key, $category)) {
        return (string) $category[$key];
    }

    return (string) $default;
};
?>
<form method="post" action="<?= $isEdit ? url('/categories/' . $category['id']) : url('/categories') ?>">
  <?= csrf_field() ?>
  <div class="modal-form-grid">
    <div class="sm:col-span-2">
      <label class="label">Nombre</label>
      <input name="name" value="<?= e($field('name')) ?>" required class="input-field" placeholder="Ej: Bebidas, Repuestos…">
    </div>
    <div class="sm:col-span-2">
      <label class="label">Descripción <span class="text-slate-400 font-normal">(opcional)</span></label>
      <textarea name="description" rows="2" class="input-field" placeholder="Detalle breve para tu equipo"><?= e($field('description')) ?></textarea>
    </div>
  </div>
  <div class="modal-form-footer">
    <a href="<?= url('/categories') ?>" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar</button>
  </div>
</form>
