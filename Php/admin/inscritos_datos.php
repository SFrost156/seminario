<?php
require __DIR__ . '/proteger_admin.php';
require_once __DIR__ . '/../conexion.php';

$page   = max(1, (int)($_GET['page'] ?? 1));
$pp     = max(1, min(100, (int)($_GET['perPage'] ?? 25)));
$q      = trim($_GET['q'] ?? '');
$sort   = $_GET['sort'] ?? 'fecha';
$dir    = strtoupper($_GET['dir'] ?? 'DESC'); $dir = ($dir==='ASC'?'ASC':'DESC');

$map = [
  'id' => 'id',
  'nombre' => 'nombre',
  'apellido' => 'apellido',
  'correo' => 'correo',
  'fecha' => 'fecha'
];
$orden = $map[$sort] ?? 'fecha';

$where = '1';
$params = []; $types = '';
if ($q !== '') {
  $where = "(id LIKE CONCAT('%',?,'%') OR nombre LIKE CONCAT('%',?,'%') OR apellido LIKE CONCAT('%',?,'%') OR telefono LIKE CONCAT('%',?,'%') OR correo LIKE CONCAT('%',?,'%') OR cedula LIKE CONCAT('%',?,'%'))";
  $params = [$q,$q,$q,$q,$q,$q]; $types = 'ssssss';
}

$total = 0;
if ($where==='1') {
  $res = $conexion->query("SELECT COUNT(*) FROM inscripciones");
  $total = (int)$res->fetch_row()[0];
} else {
  $st = $conexion->prepare("SELECT COUNT(*) FROM inscripciones WHERE $where");
  $st->bind_param($types, ...$params); $st->execute(); $r=$st->get_result();
  $total = (int)$r->fetch_row()[0];
}

$off = ($page-1)*$pp;
$sql = "SELECT id,nombre,apellido,telefono,correo,cedula,fecha
        FROM inscripciones ".($where==='1'?'':"WHERE $where")."
        ORDER BY $orden $dir
        LIMIT ? OFFSET ?";
if ($where==='1'){
  $st = $conexion->prepare("SELECT id,nombre,apellido,telefono,correo,cedula,fecha FROM inscripciones ORDER BY $orden $dir LIMIT ? OFFSET ?");
  $st->bind_param('ii', $pp, $off);
} else {
  $st = $conexion->prepare("SELECT id,nombre,apellido,telefono,correo,cedula,fecha FROM inscripciones WHERE $where ORDER BY $orden $dir LIMIT ? OFFSET ?");
  $types2 = $types.'ii';
  $st->bind_param($types2, ...array_merge($params, [$pp,$off]));
}
$st->execute(); $rs=$st->get_result();
$rows=[];
while($row=$rs->fetch_assoc()){
  $rows[]=[
    'id'=>$row['id'],
    'nombre'=>$row['nombre'],
    'apellido'=>$row['apellido'],
    'telefono'=>$row['telefono'],
    'correo'=>$row['correo'],
    'cedula'=>$row['cedula'],
    'fecha'=>date('Y-m-d H:i:s', strtotime($row['fecha']))
  ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['total'=>$total, 'rows'=>$rows], JSON_UNESCAPED_UNICODE);
