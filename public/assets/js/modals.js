(function () {
  function closeModal(el) {
    if (!el) {
      return;
    }
    if (el.id) {
      el.classList.add('hidden');
      return;
    }
    el.remove();
  }

  function bindDismiss(root) {
    root.querySelectorAll('[data-dismiss-modal]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        closeModal(btn.closest('.modal-backdrop'));
      });
    });
    root.addEventListener('click', function (e) {
      if (e.target === root) {
        closeModal(root);
      }
    });
  }

  document.querySelectorAll('.flash-modal').forEach(bindDismiss);

  document.querySelectorAll('[data-open-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-open-modal');
      var modal = id ? document.getElementById(id) : null;
      if (modal) {
        modal.classList.remove('hidden');
      }
    });
  });

  function showConfirm(opts) {
    var root = document.createElement('div');
    root.className = 'modal-backdrop';
    root.setAttribute('role', 'alertdialog');
    root.setAttribute('aria-modal', 'true');
    root.innerHTML =
      '<div class="modal-panel">' +
      '<div class="modal-header">' +
      '<div class="modal-header-text"><h3>' + escapeHtml(opts.title || 'Confirmar') + '</h3>' +
      (opts.message ? '<p>' + escapeHtml(opts.message) + '</p>' : '') +
      '</div></div>' +
      '<div class="modal-body">' +
      '<div class="modal-form-footer !mt-0 !border-0 !pt-0">' +
      '<button type="button" class="btn-secondary" data-dismiss-modal>Cancelar</button>' +
      '<button type="button" class="' + (opts.danger ? 'btn-danger' : 'btn-primary') + '" data-confirm-ok>' +
      escapeHtml(opts.confirmLabel || 'Confirmar') +
      '</button></div></div></div>';
    document.body.appendChild(root);
    bindDismiss(root);
    root.querySelector('[data-confirm-ok]').addEventListener('click', function () {
      closeModal(root);
      if (typeof opts.onConfirm === 'function') {
        opts.onConfirm();
      }
    });
  }

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  document.querySelectorAll('form[data-confirm-message]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (form.dataset.confirmed === '1') {
        delete form.dataset.confirmed;
        return;
      }
      e.preventDefault();
      showConfirm({
        title: form.getAttribute('data-confirm-title') || 'Confirmar',
        message: form.getAttribute('data-confirm-message') || '',
        confirmLabel: form.getAttribute('data-confirm-label') || 'Confirmar',
        danger: form.getAttribute('data-confirm-danger') === '1',
        onConfirm: function () {
          form.dataset.confirmed = '1';
          if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
          } else {
            form.submit();
          }
        },
      });
    });
  });

  window.PulseOSConfirm = showConfirm;
})();
