<?php
/**
 * @var string $name
 * @var string $label
 * @var string $type input|email|password|number|textarea|select
 * @var mixed $value
 * @var string|null $hint
 * @var bool $required
 * @var string|null $placeholder
 * @var string|null $id
 * @var list<array{value: string, label: string}>|null $options for select
 */
$id = $id ?? $name;
$type = $type ?? 'input';
$required = $required ?? false;
?>
<div class="form-group">
  <label for="<?= e($id) ?>" class="label">
    <?= e($label) ?>
    <?php if ($required): ?><span class="text-rose-400" aria-hidden="true">*</span><?php endif; ?>
  </label>
  <?php if ($type === 'textarea'): ?>
  <textarea id="<?= e($id) ?>" name="<?= e($name) ?>" class="input-field" rows="3"
    <?= $required ? 'required' : '' ?>
    placeholder="<?= e($placeholder ?? '') ?>"><?= e((string) ($value ?? '')) ?></textarea>
  <?php elseif ($type === 'select'): ?>
  <select id="<?= e($id) ?>" name="<?= e($name) ?>" class="input-field" <?= $required ? 'required' : '' ?>>
    <?php foreach ($options ?? [] as $opt): ?>
    <option value="<?= e($opt['value']) ?>" <?= (string) ($value ?? '') === (string) $opt['value'] ? 'selected' : '' ?>><?= e($opt['label']) ?></option>
    <?php endforeach; ?>
  </select>
  <?php else: ?>
  <input id="<?= e($id) ?>" type="<?= e($type === 'input' ? 'text' : $type) ?>" name="<?= e($name) ?>"
    value="<?= e((string) ($value ?? '')) ?>" class="input-field"
    <?= $required ? 'required' : '' ?>
    placeholder="<?= e($placeholder ?? '') ?>">
  <?php endif; ?>
  <?php if (!empty($hint)): ?>
  <p class="form-hint"><?= e($hint) ?></p>
  <?php endif; ?>
</div>
