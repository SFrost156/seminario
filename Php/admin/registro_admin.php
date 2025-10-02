<?php
require_once __DIR__ . '/../conexion.php';

$usuario    = trim($_POST['usuario']    ?? '');
$nombre     = trim($_POST['nombre']     ?? '');
$apellido   = trim($_POST['apellido']   ?? '');
$correo     = trim($_POST['correo']     ?? '');
$contrasena = (string)($_POST['contrasena'] ?? '');
$codigo     = trim($_POST['codigo']     ?? '');

if ($codigo !== 'SeminarioIATicTropico2025B') { echo 'Código inválido.'; exit; }
if ($usuario==='' || $nombre==='' || $apellido==='' || $correo==='' || $contrasena==='') { echo 'Faltan campos obligatorios.'; exit; }
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) { echo 'Correo inválido.'; exit; }
if (!preg_match('/@unitropico\.edu\.co$/i', $correo)) { echo 'El correo debe ser institucional (@unitropico.edu.co).'; exit; }

$len = strlen($contrasena);
if ($len < 8 || $len > 20 || !preg_match('/[A-Z]/', $contrasena) || !preg_match('/[a-z]/', $contrasena) || !preg_match('/[^A-Za-z0-9]/', $contrasena)) {
  echo 'Contraseña insegura.'; exit;
}

$sqlCheck = "SELECT 1 FROM administradores WHERE usuario = ? OR correo = ? LIMIT 1";
if (!$stc = $conexion->prepare($sqlCheck)) { echo 'Error interno.'; exit; }
$stc->bind_param("ss", $usuario, $correo);
$stc->execute();
$stc->store_result();
if ($stc->num_rows > 0) { echo 'Ya existe un usuario o correo registrado.'; exit; }

$hash = password_hash($contrasena, PASSWORD_DEFAULT);

$sql = "INSERT INTO administradores (usuario, nombre, apellido, correo, contrasena) VALUES (?, ?, ?, ?, ?)";
if (!$stmt = $conexion->prepare($sql)) { echo 'Error interno.'; exit; }
$stmt->bind_param("sssss", $usuario, $nombre, $apellido, $correo, $hash);

if ($stmt->execute()) { echo 'success'; }
else { echo 'No se pudo crear el administrador.'; }
