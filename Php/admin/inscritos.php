<?php
require __DIR__ . '/proteger_admin.php';
require_once __DIR__ . '/../conexion.php';

$adminUsuario = 'Administrador';
$idAdmin = (int)($_SESSION['id_admin'] ?? 0);
if ($idAdmin > 0) {
  if ($st = $conexion->prepare("SELECT usuario FROM administradores WHERE id=?")) {
    $st->bind_param('i', $idAdmin);
    $st->execute();
    if ($res = $st->get_result()) {
      if ($row = $res->fetch_assoc()) {
        $adminUsuario = $row['usuario'] ?? 'Administrador';
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inscritos - Seminario</title>
  <link rel="icon" href="../../Resources/icons/escudo-unitropico_1.png" />
  <link rel="stylesheet" href="../../Css/admin/inscritos.css" />
</head>
<body>
<div class="container">
  <div class="content">

    <header class="header">
      <img src="../../Resources/icons/logo-unitropico-01.png" class="logo-universidad" alt="Logo Unitrópico" />
      <h1>Primer Seminario Internacional de Inteligencia Artificial Unitrópico</h1>
      <img src="../../Resources/icons/escudo-unitropico_1.png" class="logo-universidad" alt="Escudo Unitrópico" />
    </header>

    <div class="admin-info">
      <?= htmlspecialchars($adminUsuario, ENT_QUOTES, 'UTF-8') ?>
      <a href="./cerrar_sesion.php" class="btn-logout">🚪 Cerrar sesión</a>
    </div>

    <nav class="menu-top">
      <a href="./index_admin.php" class="btn-nav">🏠 Inicio</a>
    </nav>

    <section class="panel-control">
      <input id="q" class="inpt" placeholder="Buscar por cualquier campo (Enter)..." />
      <button id="btnBuscar" class="btn btn-prim">Buscar</button>
      <button id="btnLimpiar" class="btn btn-sec">Limpiar</button>

      <span class="label-inline">Ordenar por:</span>
      <select id="sort" class="sel">
        <option value="fecha">Fecha inscripción</option>
        <option value="id">ID</option>
        <option value="nombre">Nombre</option>
        <option value="apellido">Apellido</option>
        <option value="correo">Correo</option>
      </select>

      <select id="dir" class="sel">
        <option value="DESC">Descendente</option>
        <option value="ASC">Ascendente</option>
      </select>

      <button id="btnAplicar" class="btn btn-prim">Aplicar</button>

      <form id="frmExport" action="./inscritos_exportar_excel.php" method="get" target="_blank" class="push-right">
        <input type="hidden" name="q" id="ex_q">
        <input type="hidden" name="sort" id="ex_sort">
        <input type="hidden" name="dir" id="ex_dir">
        <button type="submit" class="btn btn-prim">📥 Exportar Excel</button>
      </form>
    </section>

    <div class="pagin" id="pagin-top"></div>

    <section class="tabla-wrap">
      <table id="tabla" class="tabla">
        <thead>
          <tr>
            <th class="col-id">ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Cédula</th>
            <th class="col-fecha">Fecha inscripción</th>
            <th class="col-acciones">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </section>

    <div class="pagin" id="pagin-bottom"></div>

  </div>

  <footer class="footer">
    <img src="../../Resources/icons/logo-unitropico-04.png" class="logo-universidad" alt="Logo Unitrópico" />
    <div class="footer-center">
      <div class="footer-container">
        <p>Universidad Internacional del Trópico Americano: <b>Unitrópico</b></p>
      </div>
      <div class="footer-content">
        <p>Programa de Ingeniería de Sistemas</p>
        <p>Grupo Tic-Trópico</p>
        <p>© 2025 Unitrópico. Todos los derechos reservados.</p>
      </div>
    </div>
    <img src="../../Resources/icons/escudo-unitropico_4.png" class="logo-seminario" alt="Escudo Unitrópico" />
  </footer>
</div>

<div class="modal" id="modalEdit" aria-hidden="true">
  <div class="modal-dialog">
    <h3 class="modal-title">Editar inscrito</h3>
    <form id="formEdit" class="form-grid">
      <input type="hidden" id="e_id" name="id">
      <label>Nombre<input id="e_nombre" name="nombre" required></label>
      <label>Apellido<input id="e_apellido" name="apellido" required></label>
      <label>Teléfono<input id="e_telefono" name="telefono" required></label>
      <label>Correo<input id="e_correo" name="correo" required></label>
      <label>Cédula<input id="e_cedula" name="cedula" required></label>
    </form>
    <div class="modal-actions">
      <button class="btn btn-sec" data-close="#modalEdit">Cancelar</button>
      <button id="btnSave" class="btn btn-prim">Guardar</button>
    </div>
  </div>
</div>

<div class="modal" id="modalDel" aria-hidden="true">
  <div class="modal-dialog">
    <h3 class="modal-title">Eliminar inscrito</h3>
    <p>¿Seguro que deseas eliminar este registro?</p>
    <div class="modal-actions">
      <button class="btn btn-sec" data-close="#modalDel">Cancelar</button>
      <button id="btnDelOk" class="btn btn-warn">Eliminar</button>
    </div>
  </div>
</div>

<script src="../../Controller/admin/inscritos.js"></script>
</body>
</html>
