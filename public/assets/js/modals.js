(function () {
  function closeModal(el) {
    if (!el) {
      return;
    }
    if (el.id) {
      el.classList.add('hidden');
      el.setAttribute('aria-hidden', 'true');
      return;
    }
    el.remove();
  }

  function openModal(modal, trigger) {
    if (trigger) {
      var slug = trigger.getAttribute('data-tenant-slug');
      var action = trigger.getAttribute('data-delete-url');
      if (slug) {
        var slugEl = modal.querySelector('[data-tenant-slug-display]');
        if (slugEl) {
          slugEl.textContent = slug;
        }
        var input = modal.querySelector('[name="confirm_slug"]');
        if (input) {
          input.value = '';
          input.placeholder = slug;
        }
      }
      if (action) {
        var form = modal.querySelector('[data-tenant-delete-form]');
        if (form) {
          form.action = action;
        }
      }
    }
    modal.classList.remove('hidden');
    modal.removeAttribute('aria-hidden');
  }

  document.addEventListener('click', function (e) {
    var dismissBtn = e.target.closest('[data-dismiss-modal]');
    if (dismissBtn) {
      closeModal(dismissBtn.closest('.modal-backdrop'));
      return;
    }

    if (e.target.classList.contains('modal-backdrop') && !e.target.classList.contains('hidden')) {
      closeModal(e.target);
      return;
    }

    var openBtn = e.target.closest('[data-open-modal]');
    if (openBtn) {
      var id = openBtn.getAttribute('data-open-modal');
      var modal = id ? document.getElementById(id) : null;
      if (modal) {
        openModal(modal, openBtn);
      }
    }
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
