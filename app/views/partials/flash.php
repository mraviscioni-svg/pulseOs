<?php
$flashSuccess = \App\Core\Session::flash('success');
$flashError = \App\Core\Session::flash('error');
?>
<?php if ($flashSuccess): ?>
<div class="modal-backdrop flash-modal" role="alertdialog" aria-modal="true" aria-labelledby="flash-success-title">
  <div class="modal-panel modal-panel-alert modal-panel-alert-success">
    <div class="modal-header">
      <div class="modal-header-text">
        <h3 id="flash-success-title">Listo</h3>
        <p><?= e($flashSuccess) ?></p>
      </div>
    </div>
    <div class="modal-body">
      <div class="modal-form-footer !mt-0 !border-0 !pt-0">
        <button type="button" class="btn-primary" data-dismiss-modal>Aceptar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="modal-backdrop flash-modal" role="alertdialog" aria-modal="true" aria-labelledby="flash-error-title">
  <div class="modal-panel modal-panel-alert modal-panel-alert-error">
    <div class="modal-header">
      <div class="modal-header-text">
        <h3 id="flash-error-title">Atención</h3>
        <p><?= e($flashError) ?></p>
      </div>
    </div>
    <div class="modal-body">
      <div class="modal-form-footer !mt-0 !border-0 !pt-0">
        <button type="button" class="btn-primary" data-dismiss-modal>Aceptar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
