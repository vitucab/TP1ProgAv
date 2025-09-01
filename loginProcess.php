<?php
declare(strict_types=1);

// Mostrar errores en desarrollo (podés comentar en prod)
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Almenos por ahora este es el unico usuario que debe reconocer
const USUARIO_VALIDO = 'fcytuader';
const CLAVE_VALIDA   = 'programacionavanzada';

// Tomamos los datos del POST 
$usuario = trim($_POST['usuario'] ?? '');
$clave   = $_POST['clave'] ?? '';

// Validar login
$loginOK = ($usuario === USUARIO_VALIDO && $clave === CLAVE_VALIDA);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Resultado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="data:,">
</head>
<body class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

      <?php if ($loginOK): ?>
        <div class="alert alert-success shadow-sm">
          <h4 class="alert-heading">✅ Login correcto</h4>
          <p>Bienvenido, <strong><?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?></strong>.</p>
        </div>

        <a class="btn btn-primary mt-3" href="index.php">Volver</a>

      <?php else: ?>
        <div class="alert alert-danger shadow-sm">
          <h4 class="alert-heading">❌ Error de login</h4>
          <p>Usuario o contraseña incorrectos.</p>
        </div>
        <a class="btn btn-secondary mt-3" href="index.php">Volver al formulario</a>
      <?php endif; ?>

    </div>
  </div>
</body>
</html>