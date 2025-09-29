<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
    header('Location: index.php');
    exit;
}

function redirect_with_error(string $message, array $oldValues): void {
    $_SESSION['login_error'] = $message;
    $_SESSION['old_login'] = $oldValues;
    header('Location: index.php');
    exit;
}

$usuario = isset($_POST['usuario']) ? trim((string)$_POST['usuario']) : '';
$clave = isset($_POST['clave']) ? trim((string)$_POST['clave']) : '';
$rol = isset($_POST['rol']) ? (string)$_POST['rol'] : '';
$materia = isset($_POST['materia']) ? (string)$_POST['materia'] : '';
$captchaInput = isset($_POST['captcha']) ? strtoupper(trim((string)$_POST['captcha'])) : '';

$oldValues = [
    'usuario' => $usuario,
    'rol' => $rol,
    'materia' => $materia,
];

if ($usuario === '' || $clave === '') {
    redirect_with_error('Debe completar usuario y contraseña.', $oldValues);
}

$rolesPermitidos = ['alumno', 'docente'];
if (!in_array($rol, $rolesPermitidos, true)) {
    redirect_with_error('Debe seleccionar un rol válido.', $oldValues);
}

$materiasList = [
    'Ingenieria en Software 2',
    'Bases de Datos',
    'Programacion Avanzada',
    'Probabilidad y Estadistica',
    'Paradigma y Lenguajes',
    'Sistemas Operativos',
];
if (!in_array($materia, $materiasList, true)) {
    redirect_with_error('Seleccione una materia válida.', $oldValues);
}

$captchaSession = isset($_SESSION['captcha']) ? strtoupper((string)$_SESSION['captcha']) : '';
if ($captchaInput === '' || $captchaSession === '' || !hash_equals($captchaSession, $captchaInput)) {
    redirect_with_error('El valor del captcha no es correcto.', $oldValues);
}

$credencialesValidas = $usuario === 'fcytuader' && $clave === 'programacionavanzada';
if (!$credencialesValidas) {
    redirect_with_error('Usuario o contraseña incorrectos.', $oldValues);
}

session_regenerate_id(true);
$_SESSION['autenticado'] = true;
$_SESSION['usuario_nombre'] = $usuario;
unset($_SESSION['captcha']);

header('Location: inicio.php');
exit;
