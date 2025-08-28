<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Mi Aplicación</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Mi Sistema de Administración</h1>
  </header>

  <main>
    <div class="contenedor-login">
      <h2>Iniciar Sesión</h2>
      <form action="procesoLogin.php" method="POST">
        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Ingresar</button>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> - Mi Aplicación</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>