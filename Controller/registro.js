document.addEventListener("DOMContentLoaded", () => {
  const nombreInput   = document.getElementById("nombre");
  const apellidoInput = document.getElementById("apellido");
  const telefonoInput = document.getElementById("telefono1");
  const correoInput   = document.getElementById("correo");
  const cedulaInput   = document.getElementById("cedula");

  // ===== Sanitización =====
  const regexSoloLetras = /[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g;
  const regexDigits = /[^0-9]/g;

  function allowOnlyLetters(e) {
    const cursor = e.target.selectionStart;
    const clean = e.target.value.replace(regexSoloLetras, '');
    if (clean !== e.target.value) {
      e.target.value = clean;
      try { e.target.setSelectionRange(cursor - 1, cursor - 1); } catch {}
    }
  }
  function allowOnlyDigits(e) {
    const cursor = e.target.selectionStart;
    const clean = e.target.value.replace(regexDigits, '');
    if (clean !== e.target.value) {
      e.target.value = clean;
      try { e.target.setSelectionRange(cursor - 1, cursor - 1); } catch {}
    }
  }
  function sanitizePaste(e, regex) {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    const clean = pasted.replace(regex, '');
    document.execCommand('insertText', false, clean);
  }

  nombreInput.addEventListener('input', allowOnlyLetters);
  apellidoInput.addEventListener('input', allowOnlyLetters);
  nombreInput.addEventListener('paste', e => sanitizePaste(e, regexSoloLetras));
  apellidoInput.addEventListener('paste', e => sanitizePaste(e, regexSoloLetras));

  telefonoInput.addEventListener('input', allowOnlyDigits);
  cedulaInput.addEventListener('input', allowOnlyDigits);
  telefonoInput.addEventListener('paste', e => sanitizePaste(e, regexDigits));
  cedulaInput.addEventListener('paste', e => sanitizePaste(e, regexDigits));

  [nombreInput, apellidoInput, correoInput, cedulaInput, telefonoInput].forEach(inp => {
    inp.addEventListener('blur', () => inp.value = inp.value.trim());
  });

  // ===== Modales =====
  function showModal(modalId, noticeType) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.style.display = 'flex';
    const closeBtn = modal.querySelector('.btn-close-modal');
    if (!closeBtn) return;

    // SIEMPRE reinicia la página y deja un mensaje persistente arriba
    closeBtn.addEventListener('click', () => {
      const base = window.location.origin + window.location.pathname;
      window.location.href = `${base}?notice=${encodeURIComponent(noticeType)}`;
    }, { once: true });
  }

  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get('status');

  if (status) {
    // status -> (modal, notice)
    switch(status) {
      case 'success':
        showModal('successModal', 'success');
        break;
      case 'duplicate_email':
      case 'duplicate_cedula':
      case 'duplicate_both':
      case 'already_registered':
        showModal('alreadyRegisteredModal', 'warning');
        break;
      case 'invalid_input':
      case 'mail_error':
      case 'db_prepare_error':
      case 'db_execute_error':
        showModal('invalidInputModal', 'error'); // puedes cambiar a errorModal si prefieres
        break;
      default:
        showModal('errorModal', 'error');
        break;
    }
  }

  // ===== Mensaje persistente tras el reinicio =====
  const notice = urlParams.get('notice');
  if (notice) {
    const bar = document.getElementById('statusMessage');
    if (bar) {
      bar.style.display = 'block';
      if (notice === 'success') {
        bar.textContent = '✅ Registro exitoso.';
        bar.classList.add('success');
      } else if (notice === 'warning') {
        bar.textContent = '⚠️ El correo o la cédula ya están registrados.';
        bar.classList.add('warning');
      } else {
        bar.textContent = '❌ Hubo un error en el registro.';
        bar.classList.add('error');
      }
      // Limpia la URL para evitar re-mostrar si recarga otra vez
      window.history.replaceState(null, '', window.location.pathname);
    }
  }
});
