<?php
// Credenciales válidas
$usuarioValido = "fcytuader";
$passwordValido = "programacionavanzada";

// Recibimos datos del formulario
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

// Validación
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultado Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Resultado del Login</h1>
  </header>
  <main>
    <div class="contenedor-login">
      <?php if ($usuario === $usuarioValido && $password === $passwordValido): ?>
        <h2>Ingreso correctamente ✅</h2>
        <p>Bienvenido, <strong><?php echo htmlspecialchars($usuario); ?></strong></p>
      <?php else: ?>
        <h2>Usuario o contraseña incorrectos ❌</h2>
        <a href="index.php">Intentar nuevamente</a>
      <?php endif; ?>
    </div>
  </main>
  <footer>
    <p>&copy; <?php echo date("Y"); ?> - Mi Aplicación</p>
  </footer>
  <script src="js/script.js"></script>
</body>
</html>
