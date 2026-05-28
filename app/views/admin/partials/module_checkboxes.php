<?php
/** @var list<string> $activeModules */
/** @var array<string, string> $moduleLabels */
?>
<div class="grid gap-2 sm:grid-cols-2">
  <?php foreach ($moduleLabels as $key => $label): ?>
  <label class="inline-flex gap-2 rounded-lg border border-slate-800 px-3 py-2 text-sm">
    <input type="checkbox" name="modules[]" value="<?= e($key) ?>" <?= in_array($key, $activeModules, true) ? 'checked' : '' ?>>
    <?= e($label) ?>
  </label>
  <?php endforeach; ?>
</div>
