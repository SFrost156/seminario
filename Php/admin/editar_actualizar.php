<?php
require __DIR__ . '/proteger_admin.php';
require_once __DIR__ . '/../conexion.php';

$id = (int)($_POST['id'] ?? 0);
$nombre   = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$correo   = trim($_POST['correo'] ?? '');
$cedula   = trim($_POST['cedula'] ?? '');

if ($id<=0 || $nombre==='' || $apellido==='' || $telefono==='' || $correo==='' || $cedula==='') {
  echo 'Datos incompletos'; exit;
}

$st = $conexion->prepare("UPDATE inscripciones SET nombre=?, apellido=?, telefono=?, correo=?, cedula=? WHERE id=?");
$st->bind_param('sssssi', $nombre,$apellido,$telefono,$correo,$cedula,$id);
echo $st->execute() ? 'success' : 'error';
