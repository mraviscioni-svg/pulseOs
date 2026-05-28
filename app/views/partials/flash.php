<?php if ($msg = \App\Core\Session::flash('success')): ?>
<div class="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($msg = \App\Core\Session::flash('error')): ?>
<div class="mb-4 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300"><?= e($msg) ?></div>
<?php endif; ?>
