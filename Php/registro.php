<?php
// Php/registro.php
declare(strict_types=1);
session_start();

// Conexión
require_once __DIR__ . '/conexion.php'; 

// PHPMailer (rutas relativas dentro de Php/libraries)
require_once __DIR__ . '/libraries/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/libraries/PHPMailer/SMTP.php';
require_once __DIR__ . '/libraries/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Helper para redirigir con status
function redirect_status($status) {
    header("Location: ../Templates/registro.html?status={$status}");
    exit;
}

// POST
$nombre   = $_POST['nombre']   ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono1'] ?? ''; 
$correo   = $_POST['correo']   ?? '';
$cedula   = $_POST['cedula']   ?? '';

// datos
$nombre   = trim($nombre);
$apellido = trim($apellido);
$telefono = trim($telefono);
$correo   = trim($correo);
$cedula   = trim($cedula);

// Validaciones
$errors = [];
// Nombre/apellido: solo letras (mayúsculas, minúsculas), espacios y acentos (1-50)
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,50}$/u', $nombre)) {
    $errors[] = "Nombre inválido.";
}
if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]{1,50}$/u', $apellido)) {
    $errors[] = "Apellido inválido.";
}

// Teléfono: exactamente 10 dígitos
if (!preg_match('/^[0-9]{10}$/', $telefono)) {
    $errors[] = "Teléfono inválido.";
}

// Cédula: entre 6 y 15 dígitos
if (!preg_match('/^[0-9]{6,15}$/', $cedula)) {
    $errors[] = "Cédula inválida.";
}

// Correo: formato y dominio permitido
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Correo con formato inválido.";
} else {
    $allowed_domains = [
        "gmail.com","hotmail.com","hotmail.es","outlook.com","outlook.es",
        "live.com","live.com.mx","msn.com","yahoo.com","icloud.com","me.com",
        "mac.com","protonmail.com","pm.me","zoho.com","gmx.com","yandex.com",
        "mail.com","unitropico.edu.co"
    ];
    $parts = explode('@', $correo);
    $domain = strtolower(array_pop($parts));
    if (!in_array($domain, $allowed_domains, true)) {
        $errors[] = "Dominio de correo no permitido.";
    }
}

if (!empty($errors)) {
    $_SESSION['registro_errors'] = $errors;
    redirect_status('invalid_input');
}

// Verificar si el correo ya existe
$sql_check = "SELECT correo FROM inscripciones WHERE correo = ?";
$stmt_check = $conexion->prepare($sql_check);
if (!$stmt_check) {
    redirect_status('db_prepare_error');
}
$stmt_check->bind_param('s', $correo);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    $stmt_check->close();
    redirect_status('already_registered');
}
$stmt_check->close();


// Insertar en base de datos
$sql = "INSERT INTO inscripciones (nombre, apellido, telefono, correo, cedula, fecha)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    redirect_status('db_prepare_error');
}
$stmt->bind_param('sssss', $nombre, $apellido, $telefono, $correo, $cedula);

if (!$stmt->execute()) {
    redirect_status('db_execute_error');
}

$inserted_id = $stmt->insert_id;
$stmt->close();

// Enviar correo de confirmación con PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'seminariosistemas.unitropico@gmail.com';          
    $mail->Password   = 'yyxl obbb gfhu sbeq';   
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('seminariosistemas.unitropico@gmail.com', 'Seminario IA Unitrópico'); 
    $mail->addAddress($correo, $nombre . ' ' . $apellido);

    $mail->isHTML(true);
    $mail->Subject = 'Confirmación de inscripción - Seminario IA Unitrópico';
    $mail->Body = "
        <p>¡Hola <strong>" . htmlspecialchars($nombre . ' ' . $apellido) . "</strong>!</p>
        <p>Hemos recibido tu inscripción al <strong>1er Seminario de Inteligencia Artificial Unitrópico</strong>.</p>
        <ul>
          <li><strong>Cédula:</strong> " . htmlspecialchars($cedula) . "</li>
          <li><strong>Teléfono:</strong> " . htmlspecialchars($telefono) . "</li>
          <li><strong>Correo:</strong> " . htmlspecialchars($correo) . "</li>
        </ul>
        <p>📅 Fecha del evento: 06 de octubre 2025</p>
        <p>📍 Lugar: Universidad Unitrópico</p>
        <br>
        <p>Saludos,<br><strong>Grupo Tic-Trópico</strong></p>
    ";

    $mail->send();
    redirect_status('success');

} catch (Exception $e) {
    redirect_status('mail_error');
}