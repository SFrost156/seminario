<?php
require __DIR__ . '/proteger_admin.php';
require_once __DIR__ . '/../conexion.php';

/* ====== Filtro / orden ====== */
$q    = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'fecha';
$dir  = strtoupper($_GET['dir'] ?? 'DESC'); $dir = ($dir==='ASC'?'ASC':'DESC');

$map   = ['id'=>'id','nombre'=>'nombre','apellido'=>'apellido','correo'=>'correo','fecha'=>'fecha'];
$orden = $map[$sort] ?? 'fecha';

$where='1'; $params=[]; $types='';
if ($q!==''){
  $where="(id LIKE CONCAT('%',?,'%') OR nombre LIKE CONCAT('%',?,'%') OR apellido LIKE CONCAT('%',?,'%')
           OR telefono LIKE CONCAT('%',?,'%') OR correo LIKE CONCAT('%',?,'%') OR cedula LIKE CONCAT('%',?,'%'))";
  $params=[$q,$q,$q,$q,$q,$q]; $types='ssssss';
}

/* ====== Datos ====== */
if ($where==='1'){
  $st = $conexion->prepare("SELECT id,nombre,apellido,telefono,correo,cedula,fecha FROM inscripciones ORDER BY $orden $dir");
} else {
  $st = $conexion->prepare("SELECT id,nombre,apellido,telefono,correo,cedula,fecha FROM inscripciones WHERE $where ORDER BY $orden $dir");
  $st->bind_param($types, ...$params);
}
$st->execute(); $rs=$st->get_result();

$headers = ['ID','Nombre','Apellido','Teléfono','Correo','Cédula','Fecha inscripción'];
$rows=[];
while($r=$rs->fetch_assoc()){
  $rows[]=[
    (string)$r['id'],
    (string)$r['nombre'],
    (string)$r['apellido'],
    (string)$r['telefono'],
    (string)$r['correo'],
    (string)$r['cedula'],
    date('Y-m-d H:i:s', strtotime($r['fecha']))
  ];
}

/* ====== Carpetas temp ====== */
$tmp = sys_get_temp_dir().'/xlsx_'.bin2hex(random_bytes(8));
@mkdir($tmp.'/_rels', 0777, true);
@mkdir($tmp.'/xl/_rels', 0777, true);
@mkdir($tmp.'/xl/worksheets', 0777, true);

/* ====== [Content_Types].xml ====== */
file_put_contents($tmp.'/[Content_Types].xml','<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml"  ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml"            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml"   ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml"              ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/sharedStrings.xml"       ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>');

/* ====== _rels/.rels ====== */
file_put_contents($tmp.'/_rels/.rels','<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

/* ====== xl/_rels/workbook.xml.rels ====== */
file_put_contents($tmp.'/xl/_rels/workbook.xml.rels','<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

/* ====== xl/workbook.xml ====== */
file_put_contents($tmp.'/xl/workbook.xml','<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Inscritos" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

/* ====== xl/styles.xml ====== */
/* Cambios: color de encabezado fijo (verde) y cuerpo como texto con bordes finos */
$styles = '<?xml version="1.0" encoding="UTF-8"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><name val="Times New Roman"/><sz val="12"/></font>
    <font><name val="Times New Roman"/><sz val="12"/><b/><color rgb="FFFFFFFF"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill>
      <patternFill patternType="solid">
        <!-- Verde similar a la captura: #548235 -->
        <fgColor rgb="FF548235"/>
        <bgColor indexed="64"/>
      </patternFill>
    </fill>
  </fills>
  <borders count="2">
    <border/>
    <border>
      <left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/><diagonal/>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="3">
    <!-- Normal -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- Encabezado: negrita blanca, fondo verde, centrado y borde -->
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
      <alignment horizontal="center" vertical="center"/>
    </xf>
    <!-- Cuerpo: todo texto con borde fino -->
    <xf numFmtId="49" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/>
  </cellXfs>
  <cellStyles count="1">
    <cellStyle name="Normal" xfId="0" builtinId="0"/>
  </cellStyles>
</styleSheet>';
file_put_contents($tmp.'/xl/styles.xml', $styles);

/* ====== sharedStrings.xml ====== */
function esc($s){ return htmlspecialchars((string)$s, ENT_XML1|ENT_QUOTES, 'UTF-8'); }
$allStrings = array_merge($headers, ...$rows);
$unique = array_values(array_unique($allStrings));
$idx = array_flip($unique);

$sst = ['<?xml version="1.0" encoding="UTF-8"?>',
        '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($allStrings).'" uniqueCount="'.count($unique).'">'];
foreach($unique as $s){ $sst[] = '<si><t>'.esc($s).'</t></si>'; }
$sst[]='</sst>';
file_put_contents($tmp.'/xl/sharedStrings.xml', implode('', $sst));

/* ====== xl/worksheets/sheet1.xml ====== */
$cols = ['A','B','C','D','E','F','G'];
$maxRow = 1 + count($rows);

$colsXml = '
  <cols>
    <col min="1" max="1" width="6"  customWidth="1"/>
    <col min="2" max="2" width="18" customWidth="1"/>
    <col min="3" max="3" width="24" customWidth="1"/>
    <col min="4" max="4" width="19" customWidth="1"/>
    <col min="5" max="5" width="42" customWidth="1"/>
    <col min="6" max="6" width="19" customWidth="1"/>
    <col min="7" max="7" width="22" customWidth="1"/>
  </cols>';

$cells = [];
foreach($headers as $i=>$h){
  $cells[] = '<c r="'.$cols[$i].'1" t="s" s="1"><v>'.$idx[$h].'</v></c>';
}
$rowsXml = ['<row r="1" spans="1:7" ht="20" customHeight="1">'.implode('', $cells).'</row>'];

$r = 2;
foreach($rows as $row){
  $cells = [];
  foreach($row as $i=>$val){
    $cells[] = '<c r="'.$cols[$i].$r.'" t="s" s="2"><v>'.$idx[$val].'</v></c>';
  }
  $rowsXml[] = '<row r="'.$r.'" spans="1:7">'.implode('', $cells).'</row>';
  $r++;
}

$sheet = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:G'.$maxRow.'"/>
  <sheetViews>
    <!-- Congelar fila de encabezado -->
    <sheetView workbookViewId="0">
      <pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>
    </sheetView>
  </sheetViews>
  <sheetFormatPr defaultRowHeight="15"/>
  '.$colsXml.'
  <sheetData>'.implode('', $rowsXml).'</sheetData>
  <!-- Autofiltro sobre todo el rango -->
  <autoFilter ref="A1:G'.$maxRow.'"/>
</worksheet>';
file_put_contents($tmp.'/xl/worksheets/sheet1.xml', $sheet);

/* ====== Zip ====== */
$zipPath = $tmp.'/inscritos.xlsx';
$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE|ZipArchive::OVERWRITE);
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmp, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
foreach($it as $file){
  if (is_dir($file)) continue;
  $local = substr($file, strlen($tmp)+1);
  if (substr($local,-4)==='.zip') continue;
  $zip->addFile($file, $local);
}
$zip->close();

/* ====== Descargar ====== */
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="inscritos_seminario.xlsx"');
header('Content-Length: '.filesize($zipPath));
readfile($zipPath);

/* ====== Limpiar ====== */
function rrmdir($dir){
  if(!is_dir($dir))return;
  $it=new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
  $ri=new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
  foreach($ri as $f){ $f->isDir()?rmdir($f):@unlink($f); }
  @rmdir($dir);
}
rrmdir($tmp);
