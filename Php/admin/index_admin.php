<?php
require __DIR__ . '/proteger_admin.php';
require_once __DIR__ . '/../conexion.php';

/* ===========================
   Usuario administrador
=========================== */
$adminUsuario = 'Administrador';
$idAdmin = (int)($_SESSION['id_admin'] ?? 0);
if ($idAdmin > 0) {
  $st = $conexion->prepare("SELECT usuario FROM administradores WHERE id=?");
  $st->bind_param('i', $idAdmin);
  $st->execute();
  if ($res = $st->get_result()) {
    if ($row = $res->fetch_assoc()) {
      $adminUsuario = $row['usuario'] ?? 'Administrador';
    }
  }
}

/* ===========================
   Fecha del seminario
=========================== */
$fechaSeminario = '2025-10-06 07:00:00';

/* ===========================
   Métricas
=========================== */
$totalIns = 0;
if ($q = $conexion->query("SELECT COUNT(*) FROM inscripciones")) {
  $totalIns = (int)$q->fetch_row()[0];
}

$insHoy = 0;
$hoy = date('Y-m-d');
$st = $conexion->prepare("SELECT COUNT(*) FROM inscripciones WHERE DATE(fecha)=?");
$st->bind_param('s', $hoy);
$st->execute();
$r = $st->get_result();
if ($r) $insHoy = (int)$r->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de administración - Seminario</title>

  <!-- Estilos base del sitio -->
  <link rel="stylesheet" href="../../Css/index.css" />
  <!-- Estilos puntuales de este panel -->
  <link rel="stylesheet" href="../../Css/admin/index_admin.css" />

  <link rel="icon" href="../../Resources/icons/escudo-unitropico_1.png" />
</head>
<body>
  <div class="container">
    <div class="content">

      <!-- Encabezado -->
      <header class="header">
        <img src="../../Resources/icons/logo-unitropico-01.png" alt="Logo Unitrópico" class="logo-universidad" />
        <h1>Primer Seminario Internacional de Inteligencia Artificial Unitrópico</h1>
        <img src="../../Resources/icons/escudo-unitropico_1.png" alt="Escudo Unitrópico" class="logo-universidad" />
      </header>

      <!-- Usuario y cierre de sesión -->
      <div class="admin-info">
      <?= htmlspecialchars($adminUsuario) ?>
        <a href="./cerrar_sesion.php" class="btn-logout">🚪 Cerrar sesión</a>
      </div>

      <!-- Menú principal -->
      <nav class="menu">
        <a href="./inscritos.php" class="btn-nav">📋 Ir a inscripciones</a>
      </nav>

      <!-- Contador -->
      <section id="reloj" class="reloj">
        <h1 style="text-align:center;">¡Sumérgete en la innovación!</h1>
        <div id="contadorTexto" class="contador"></div>

        <div class="mensaje-motivacional">
          Ven y participa en este seminario sobre inteligencia artificial. Vive una experiencia enriquecedora donde ampliarás tu conocimiento en una temática innovadora y esencial para el futuro.
        </div>

        <h2 style="text-align:center;">📅 El seminario se realizará el 6 de octubre de 2025 📅</h2>
        <div class="mensaje-motivacional">¡Te invitamos a que asistas a esta gran actividad!</div>
      </section>

      <!-- Métricas -->
      <section class="rejilla2">
        <div class="tarjeta metr">
          <h3>Total inscritos</h3>
          <div class="valor"><?= $totalIns ?></div>
        </div>
        <div class="tarjeta metr">
          <h3>Inscritos hoy</h3>
          <div class="valor"><?= $insHoy ?></div>
        </div>
      </section>

    </div>

    <!-- Footer -->
    <footer class="footer">
      <img src="../../Resources/icons/logo-unitropico-04.png" alt="Logo Unitrópico" class="logo-universidad" />
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
      <img src="../../Resources/icons/escudo-unitropico_4.png" alt="Escudo Unitrópico" class="logo-seminario" />
    </footer>
  </div>

  <script>window.__FECHA_SEMINARIO__ = "<?= addslashes($fechaSeminario) ?>";</script>
  <script src="../../Controller/admin/index_admin.js"></script>
</body>
</html>
