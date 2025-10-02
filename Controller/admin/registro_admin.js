const modal = document.getElementById('modal');
const modalTitle = document.getElementById('modalTitle');
const modalContent = document.getElementById('modalContent');
const modalClose = document.getElementById('modalClose');

function showModal(titulo, contenido, onClose) {
  modalTitle.textContent = titulo || 'Mensaje';
  modalContent.innerHTML = contenido || '';
  modal.setAttribute('aria-hidden', 'false');
  function handler() {
    modal.setAttribute('aria-hidden', 'true');
    modalClose.removeEventListener('click', handler);
    modal.removeEventListener('click', backdropHandler);
    document.removeEventListener('keydown', escHandler);
    if (typeof onClose === 'function') onClose();
  }
  function escHandler(e){ if (e.key === 'Escape') handler(); }
  function backdropHandler(e){ if (e.target === modal) handler(); }
  modalClose.addEventListener('click', handler);
  modal.addEventListener('click', backdropHandler);
  document.addEventListener('keydown', escHandler);
}

document.getElementById('mostrarContrasena').addEventListener('change', function () {
  const pass = document.getElementById('contrasena');
  pass.type = this.checked ? 'text' : 'password';
});

document.getElementById('formRegistro').addEventListener('submit', async function (e) {
  e.preventDefault();

  const btn = document.getElementById('btnRegistrar');
  const usuario = document.getElementById('usuario').value.trim();
  const nombre = document.getElementById('nombre').value.trim();
  const apellido = document.getElementById('apellido').value.trim();
  const correo = document.getElementById('correo').value.trim();
  const contrasena = document.getElementById('contrasena').value;
  const codigo = document.getElementById('codigo').value.trim();

  const correoRegex = /^[a-zA-Z]+(?:\.[a-zA-Z]+)?@unitropico\.edu\.co$/;
  const contrasenaValida = contrasena.length >= 8 && contrasena.length <= 20 &&
    /[A-Z]/.test(contrasena) && /[a-z]/.test(contrasena) && /[^A-Za-z0-9]/.test(contrasena);

  if (!usuario || !nombre || !apellido || !correo || !contrasena || !codigo) {
    showModal('Datos incompletos','Por favor, complete todos los campos.');
    return;
  }
  if (!correoRegex.test(correo)) {
    showModal('Correo inválido','El correo debe ser institucional (@unitropico.edu.co).');
    return;
  }
  if (!contrasenaValida) {
    showModal('Contraseña insegura','Debe tener 8–20 caracteres, al menos una mayúscula, una minúscula y un símbolo.');
    return;
  }

  const fd = new FormData();
  fd.append('usuario', usuario);
  fd.append('nombre', nombre);
  fd.append('apellido', apellido);
  fd.append('correo', correo);
  fd.append('contrasena', contrasena);
  fd.append('codigo', codigo);

  btn.disabled = true;
  try {
    const r = await fetch('../../Php/admin/registro_admin.php', { method: 'POST', body: fd, credentials: 'same-origin' });
    const t = (await r.text()).trim();
    if (t === 'success') {
      showModal('Administrador creado', 'La cuenta se ha creado correctamente.', () => {
        window.location.href = '../../Templates/admin/login_admin.html';
      });
    } else {
      showModal('No se pudo registrar', t);
    }
  } catch (err) {
    showModal('Error de red', 'No se pudo conectar con el servidor.\n' + err.message);
  } finally {
    btn.disabled = false;
  }
});
