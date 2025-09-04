<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

// ⚠️ Nada de espacios antes de <?php (para no romper setcookie)
require_once __DIR__ . '/src/layout.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
  $main = <<<HTML
  <main class="container my-4">
    <div class="alert alert-warning" role="alert">Acceso inválido.</div>
    <a class="btn btn-secondary" href="index.php">Volver</a>
  </main>
  HTML;

  echo setDom(['title' => 'Resultado de login', 'brand' => 'Proyecto del Grupo', 'main' => $main]);
  exit;
}

// Datos del form
$usuario    = isset($_POST['usuario']) ? trim((string)$_POST['usuario']) : '';
$clave      = isset($_POST['clave'])   ? trim((string)$_POST['clave'])   : '';
$recordarme = isset($_POST['recordarme']) && $_POST['recordarme'] === '1';
$rol   = isset($_POST['rol']) ? (string)$_POST['rol'] : '';
$rolOk = in_array($rol, ['alumno','docente'], true);
$materia = isset($_POST['materia']) ? (string)$_POST['materia'] : '';
$materiasList = [
  'Ingenieria en Software 2',
  'Bases de Datos',
  'Programacion Avanzada',
  'Probabilidad y Estadistica',
  'Paradigma y Lenguajes',
  'Sistemas Operativos',
];
$materiaOk = in_array($materia, $materiasList, true);

if ($usuario === 'fcytuader' && $clave === 'programacionavanzada' && $rolOk && $materiaOk) {
  if ($recordarme) {
    setcookie('usuario', $usuario, time() + 7*24*60*60, '/');
    setcookie('rol', $rol, time() + 7*24*60*60, '/');
    setcookie('materia', $materia, time() + 7*24*60*60, '/');
  } else {
    setcookie('usuario', '', time() - 3600, '/');
    setcookie('rol', '', time() - 3600, '/');
    setcookie('materia', '', time() - 3600, '/');
  }

  $rolTxt = htmlspecialchars($rol, ENT_QUOTES, 'UTF-8');

  $main = <<<HTML
  <main class="container my-4">
    <div class="alert alert-success text-center" role="alert">
      ingreso correctamente
    </div>
    <a class="btn btn-primary" href="index.php">Volver</a>
  </main>
  HTML;

} else {
  $main = <<<HTML
  <main class="container my-4">
    <div class="alert alert-danger" role="alert">
      Usuario o contraseña incorrectos.
    </div>
    <a class="btn btn-secondary" href="index.php">Volver al formulario</a>
  </main>
  HTML;
}

echo setDom([
  'title' => 'Resultado de login',
  'brand' => 'MiParticular',
  'main'  => $main,
]);