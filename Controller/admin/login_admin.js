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

document.getElementById('formLogin').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = document.getElementById('btnIngresar');
  const usuario = document.getElementById('usuario').value.trim();
  const contrasena = document.getElementById('contrasena').value;

  if (!usuario || !contrasena) {
    showModal('Datos incompletos', 'Por favor, complete todos los campos.');
    return;
  }

  const fd = new FormData();
  fd.append('usuario', usuario);
  fd.append('contrasena', contrasena);

  btn.disabled = true;
  try {
    const r = await fetch('../../Php/admin/login_admin.php', { method:'POST', body: fd, credentials: 'same-origin' });
    const t = (await r.text()).trim();
    if (t === 'success') {
      showModal('Ingreso correcto', 'Bienvenido al panel de administración.', () => {
        window.location.href = '../../Php/admin/index_admin.php';
      });
    } else {
      showModal('Error de inicio de sesión', 'Usuario o contraseña incorrectos.');
    }
  } catch (err) {
    showModal('Error de red', 'No se pudo conectar con el servidor.\n' + err.message);
  } finally {
    btn.disabled = false;
  }
});
