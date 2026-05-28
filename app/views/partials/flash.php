<?php if ($msg = \App\Core\Session::flash('success')): ?>
<div class="flash-success"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($msg = \App\Core\Session::flash('error')): ?>
<div class="flash-error"><?= e($msg) ?></div>
<?php endif; ?>
