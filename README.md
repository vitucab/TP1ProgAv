# Proyecto de Gestión de Usuarios

Esta aplicación PHP valida el inicio de sesión contra una base de datos SQLite creada automáticamente.
Utiliza sesiones para mantener el estado del usuario autenticado y un captcha para evitar accesos automatizados.
El proyecto crea automáticamente la base, la tabla y un usuario por defecto si todavía no existen.

## Requisitos previos

- PHP 8.1 o superior con las extensiones `pdo_sqlite` y `session` habilitadas.
- Permisos de escritura en el directorio donde se aloja el proyecto para generar la base de datos.
- Un navegador web moderno.

## Configuración rápida

El sistema utiliza las siguientes variables de entorno (todas opcionales) para personalizar la base de datos y el usuario inicial:

| Variable | Descripción | Valor por defecto |
| --- | --- | --- |
| `LOGIN_DB_PATH` | Ruta absoluta al archivo SQLite donde se guardarán los datos | `<directorio del proyecto>/data/login.sqlite` |
| `LOGIN_SEED_USER` | Nombre de usuario sembrado | `fcytuader` |
| `LOGIN_SEED_PASSWORD` | Contraseña en texto plano utilizada para generar el hash | `programacionavanzada` |
| `LOGIN_SEED_ROLE` | Rol asociado al usuario sembrado | `docente` |
| `LOGIN_SEED_SUBJECT` | Materia asociada al usuario sembrado | `Programacion Avanzada` |

Al cargar el sitio o recibir el primer intento de inicio de sesión, la capa de base de datos hará lo siguiente:

1. Creará el directorio y el archivo SQLite indicados por `LOGIN_DB_PATH` si no existen.
2. Verificará que la tabla `usuarios` exista y la creará en caso contrario.
3. Insertará (con `ON CONFLICT DO NOTHING`) un usuario por defecto (`fcytuader` / `programacionavanzada`, rol `docente`, materia `Programacion Avanzada`).

## Puesta en marcha para pruebas manuales

1. Exporta las variables necesarias si vas a usar valores distintos a los predeterminados. Ejemplo:
   ```bash
   export LOGIN_DB_PATH=/ruta/personalizada/login.sqlite
   export LOGIN_SEED_USER=fcytuader
   export LOGIN_SEED_PASSWORD=programacionavanzada
   ```
2. Arranca el servidor de desarrollo de PHP dentro del directorio del proyecto:
   ```bash
   php -S localhost:8000
   ```
3. Abre `http://localhost:8000/index.php` en el navegador.
4. Completa el formulario de inicio de sesión:
   - **Usuario**: `fcytuader` (usuario creado automáticamente si no existe).
   - **Contraseña**: `programacionavanzada`.
   - **Rol y materia**: deben coincidir con los almacenados para el usuario (por defecto: `docente` y `Programacion Avanzada`).
   - **Captcha**: transcribe el texto mostrado en la imagen.
5. Al enviar el formulario (o incluso antes, al cargar la portada):
   - Si las credenciales son válidas, serás redirigido a `inicio.php` y se almacenarán en sesión el rol y la materia.
   - Si hay algún error, volverás a `index.php` con un mensaje y los campos completados (excepto la contraseña).

## Usuarios iniciales y personalización

- El usuario por defecto (`fcytuader`) queda creado con una contraseña Bcrypt generada desde PHP en el primer arranque.
- Para agregar más cuentas manualmente puedes utilizar `password_hash` en un script PHP y ejecutar un `INSERT` sobre la tabla `usuarios` con cualquier cliente SQLite.
- Para restablecer el acceso a los valores iniciales borra la fila correspondiente en la tabla y vuelve a cargar el formulario de inicio de sesión.

## Comprobaciones adicionales

- Desde PHP puedes ejecutar un lint rápido:
  ```bash
  php -l procesoLogin.php
  ```

## Limpieza de sesión

Cuando cierres la sesión en el navegador, borra las cookies correspondientes o usa la funcionalidad de cierre de sesión de la aplicación si está disponible. Esto garantiza que los datos de rol y materia almacenados en `$_SESSION` se eliminen correctamente.
