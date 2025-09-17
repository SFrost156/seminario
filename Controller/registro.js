document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registro");

  // Inputs
  const nombreInput   = document.getElementById("nombre");
  const apellidoInput = document.getElementById("apellido");
  const telefonoInput = document.getElementById("telefono1");
  const correoInput   = document.getElementById("correo");
  const cedulaInput   = document.getElementById("cedula");

  // Dominios permitidos
  const dominiosValidos = [
    "gmail.com","hotmail.com","hotmail.es","outlook.com","outlook.es",
    "live.com","live.com.mx","msn.com","yahoo.com","icloud.com","me.com",
    "mac.com","protonmail.com","pm.me","zoho.com","gmx.com","yandex.com",
    "mail.com","unitropico.edu.co"
  ];

  // Funciones para sanitizar la entrada
  const regexSoloLetras = /[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g; 
  const regexDigits = /[^0-9]/g;                    

  function allowOnlyLetters(e) {
    const cursor = e.target.selectionStart;
    const clean = e.target.value.replace(regexSoloLetras, '');
    if (clean !== e.target.value) {
      e.target.value = clean;
      try { e.target.setSelectionRange(cursor - 1, cursor - 1); } catch (err) {}
    }
  }

  function allowOnlyDigits(e) {
    const cursor = e.target.selectionStart;
    const clean = e.target.value.replace(regexDigits, '');
    if (clean !== e.target.value) {
      e.target.value = clean;
      try { e.target.setSelectionRange(cursor - 1, cursor - 1); } catch (err) {}
    }
  }

  function sanitizePaste(e, regex) {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    const clean = pasted.replace(regex, '');
    document.execCommand('insertText', false, clean);
  }

  // Listeners para evitar caracteres no permitidos
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

  // =======================================================
  // LÓGICA PARA MODALES
  // =======================================================
  function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = 'flex';
      // Reiniciar la página al cerrar si es el modal de éxito
      if (modalId === 'successModal') {
        const closeBtn = modal.querySelector('.btn-close-modal');
        closeBtn.addEventListener('click', () => {
          window.location.href = window.location.origin + window.location.pathname;
        });
      } else {
        // Para otros modales, solo cerrar
        const closeBtn = modal.querySelector('.btn-close-modal');
        closeBtn.addEventListener('click', () => {
          modal.style.display = 'none';
        });
      }
    }
  }

  // Chequear el parámetro 'status' en la URL
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get('status');
  if (status) {
    let modalId = '';
    switch(status) {
      case 'success':
        modalId = 'successModal';
        break;
      case 'already_registered':
        modalId = 'alreadyRegisteredModal';
        break;
      case 'invalid_input':
        modalId = 'invalidInputModal';
        break;
      default:
        modalId = 'errorModal';
        break;
    }
    showModal(modalId);
  }
});