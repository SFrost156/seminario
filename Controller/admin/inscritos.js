console.log('Controller/admin/inscritos.js cargado');

// ===== Estado =====
let page = 1, perPage = 25, q = '', sort = 'fecha', dir = 'DESC', total = 0, selId = null;

// ===== Helpers de modal =====
function openModal(sel){ const el=document.querySelector(sel); if(el) el.setAttribute('aria-hidden','false'); }
function closeModal(sel){ const el=document.querySelector(sel); if(el) el.setAttribute('aria-hidden','true'); }
// Cerrar por botones con data-close
document.addEventListener('click', e=>{
  const sel = e.target.getAttribute?.('data-close');
  if(sel){ e.preventDefault(); closeModal(sel); }
});

// ===== Paginación =====
function renderPagination(containerId, totalPages, currentPage){
  const cont = document.getElementById(containerId);
  if(!cont) return;
  cont.innerHTML = '';

  const addBtn = (text, pageNum, opts={})=>{
    const b = document.createElement('button');
    b.textContent = text;
    b.className = 'page-btn' + (opts.icon ? ' icon' : '') + (opts.active ? ' active' : '');
    if (opts.disabled) b.disabled = true;
    b.setAttribute('aria-label', opts.aria || text);
    b.onclick = ()=>{ if(!opts.disabled && pageNum!==currentPage){ page = pageNum; load(); }};
    cont.appendChild(b);
  };

  addBtn('«', 1,            {icon:true, disabled: currentPage===1, aria:'Primera página'});
  addBtn('‹', currentPage-1, {icon:true, disabled: currentPage===1, aria:'Página anterior'});

  for(let p=1;p<=totalPages;p++){
    addBtn(String(p), p, {active: p===currentPage});
  }

  addBtn('›', currentPage+1, {icon:true, disabled: currentPage===totalPages, aria:'Página siguiente'});
  addBtn('»', totalPages,     {icon:true, disabled: currentPage===totalPages, aria:'Última página'});
}

// ===== Cargar tabla =====
function load(){
  const params = new URLSearchParams({page, perPage, q, sort, dir});
  const url = './inscritos_datos.php?' + params.toString();

  fetch(url, { headers:{ 'X-Requested-With':'XMLHttpRequest' } })
    .then(async resp=>{
      if(!resp.ok){
        const txt = await resp.text();
        throw new Error(`HTTP ${resp.status} ${resp.statusText} – ${txt.slice(0,300)}`);
      }
      try{
        return await resp.json();
      }catch(e){
        const raw = await resp.clone().text();
        console.error('Respuesta NO JSON:', raw);
        throw new Error('La respuesta no es JSON válido. Revisa inscritos_datos.php y los headers.');
      }
    })
    .then(d=>{
      total = d.total || 0;
      const tb = document.getElementById('tbody');
      if(!tb) return;
      tb.innerHTML = '';

      (d.rows || []).forEach(r=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="mono">${r.id}</td>
          <td>${r.nombre}</td>
          <td>${r.apellido}</td>
          <td class="mono">${r.telefono}</td>
          <td><small class="mono">${r.correo}</small></td>
          <td class="mono">${r.cedula}</td>
          <td class="mono">${r.fecha}</td>
          <td>
            <div class="acciones">
              <button class="btn btn-prim" data-edit="${r.id}">Editar</button>
              <button class="btn btn-warn" data-del="${r.id}">Eliminar</button>
            </div>
          </td>`;
        tb.appendChild(tr);
      });

      const totalPages = Math.max(1, Math.ceil(total/perPage));
      renderPagination('pagin-top', totalPages, page);
      renderPagination('pagin-bottom', totalPages, page);

      // Sincroniza exportación
      const exq=document.getElementById('ex_q'), exs=document.getElementById('ex_sort'), exd=document.getElementById('ex_dir');
      if (exq) exq.value = q;
      if (exs) exs.value = sort;
      if (exd) exd.value = dir;
    })
    .catch(err=>{
      console.error('Error cargando tabla:', err);
      alert('No se pudo cargar la tabla.\n\n' + err.message);
    });
}

// ===== Búsqueda / filtros =====
document.getElementById('btnBuscar')?.addEventListener('click', ()=>{ q=document.getElementById('q').value.trim(); page=1; load(); });
document.getElementById('q')?.addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); document.getElementById('btnBuscar').click(); }});
document.getElementById('btnLimpiar')?.addEventListener('click', ()=>{ document.getElementById('q').value=''; q=''; page=1; load(); });
document.getElementById('btnAplicar')?.addEventListener('click', ()=>{ sort=document.getElementById('sort').value; dir=document.getElementById('dir').value; page=1; load(); });

// ===== Acciones (delegación en tbody) =====
document.getElementById('tbody')?.addEventListener('click', e=>{
  const t = e.target;
  const idEdit = t.getAttribute('data-edit');
  const idDel  = t.getAttribute('data-del');

  if (idEdit){
    selId = idEdit;
    const tr = t.closest('tr'); const tds = tr.querySelectorAll('td');
    document.getElementById('e_id').value       = tds[0].textContent.trim();
    document.getElementById('e_nombre').value   = tds[1].textContent.trim();
    document.getElementById('e_apellido').value = tds[2].textContent.trim();
    document.getElementById('e_telefono').value = tds[3].textContent.trim();
    document.getElementById('e_correo').value   = tds[4].innerText.trim();
    document.getElementById('e_cedula').value   = tds[5].textContent.trim();
    openModal('#modalEdit');
  }

  if (idDel){
    selId = idDel;
    openModal('#modalDel');
  }
});

// ===== Guardar / Eliminar =====
document.getElementById('btnSave')?.addEventListener('click', async ()=>{
  const fd = new FormData(document.getElementById('formEdit'));
  const r  = await fetch('./editar_actualizar.php',{method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
  const t  = (await r.text()).trim();
  if(t==='success'){ closeModal('#modalEdit'); load(); } else { alert('Error: '+t); }
});

document.getElementById('btnDelOk')?.addEventListener('click', async ()=>{
  const fd = new FormData(); fd.append('id', selId);
  const r  = await fetch('./inscritos_eliminar.php',{method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
  const t  = (await r.text()).trim();
  if(t==='success'){ closeModal('#modalDel'); load(); } else { alert('Error: '+t); }
});

// ===== Inicial =====
load();
