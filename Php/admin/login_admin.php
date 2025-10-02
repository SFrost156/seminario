<?php
session_start();
require_once __DIR__ . '/../conexion.php';

$usuario    = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

if ($usuario === '' || $contrasena === '') { echo 'error'; exit; }

$sql = "SELECT id, contrasena, nombre FROM administradores WHERE usuario = ? LIMIT 1";
if (!$stmt = $conexion->prepare($sql)) { echo 'error'; exit; }
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
  $stmt->bind_result($id_admin, $hash, $nombre);
  $stmt->fetch();
  if (password_verify($contrasena, $hash)) {
    session_regenerate_id(true);
    $_SESSION['id_admin'] = (int)$id_admin;
    $_SESSION['usuario']  = $usuario;
    $_SESSION['nombre']   = $nombre;
    echo 'success'; exit;
  }
}

echo 'error';
