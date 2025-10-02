<?php
require __DIR__ . '/proteger_admin.php';
require_once __DIR__ . '/../conexion.php';

$id = (int)($_POST['id'] ?? 0);
if ($id<=0){ echo 'error'; exit; }

$st = $conexion->prepare("DELETE FROM inscripciones WHERE id=?");
$st->bind_param('i', $id);
echo $st->execute() ? 'success' : 'error';
