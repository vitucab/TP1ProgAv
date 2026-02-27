# Proyecto de Gestión de Usuarios

Esta aplicación PHP valida el inicio de sesión contra PostgreSQL. Utiliza sesiones para mantener el estado del usuario autenticado y un captcha para evitar accesos automatizados. El proceso de inicio crea automáticamente la base de datos, la tabla y un usuario por defecto si todavía no existen.

## Requisitos previos

- PHP 8.1 o superior con las extensiones `pdo_pgsql` y `session` habilitadas.
- Un servidor PostgreSQL accesible (local o remoto) y un rol con permisos de inicio de sesión. Para que la creación automática de la base funcione, el rol debe contar con privilegio `CREATEDB` en ese servidor.
- Un navegador web moderno.

## Configuración rápida

El sistema utiliza las siguientes variables de entorno (todas opcionales) para conectarse a PostgreSQL:

| Variable | Descripción | Valor por defecto |
| --- | --- | --- |
| `LOGIN_DB_HOST` | Host o IP del servidor PostgreSQL | `localhost` |
| `LOGIN_DB_PORT` | Puerto del servidor | `5432` |
| `LOGIN_DB_NAME` | Base de datos usada por la app | `gestion_usuarios` |
| `LOGIN_DB_MAINTENANCE` | Base de mantenimiento usada para crear la anterior | `postgres` |
| `LOGIN_DB_USER` | Rol de conexión | `fcytuader` |
| `LOGIN_DB_PASSWORD` | Contraseña del rol | `programacionavanzada` |

Además, puedes modificar los datos del usuario inicial que se crea automáticamente definiendo estas variables (opcionales). Si dejas `LOGIN_SEED_USER` o `LOGIN_SEED_PASSWORD` vacíos, la aplicación omitirá la inserción automática:

| Variable | Descripción | Valor por defecto |
| --- | --- | --- |
| `LOGIN_SEED_USER` | Nombre de usuario sembrado | `fcytuader` |
| `LOGIN_SEED_PASSWORD` | Contraseña en texto plano utilizada para generar el hash | `programacionavanzada` |
| `LOGIN_SEED_ROLE` | Rol asociado al usuario sembrado | `docente` |
| `LOGIN_SEED_SUBJECT` | Materia asociada al usuario sembrado | `Programacion Avanzada` |

Al recibir el primer intento de inicio de sesión, `procesoLogin.php` hará lo siguiente:

1. Intentará conectarse a `LOGIN_DB_NAME`.
2. Si la base no existe y el rol tiene `CREATEDB`, la generará automáticamente usando `LOGIN_DB_MAINTENANCE` como base de mantenimiento.
3. Verificará que la tabla `usuarios` exista y la creará en caso contrario.
4. Insertará (con `ON CONFLICT DO NOTHING`) un usuario por defecto (`fcytuader` / `programacionavanzada`, rol `docente`, materia `Programacion Avanzada`).

Si el rol no tiene privilegios para crear la base, verás un mensaje de error de conexión. En ese caso deberás crear la base y la tabla manualmente (ver más abajo) y volver a probar.

## Puesta en marcha para pruebas manuales

1. Exporta las variables necesarias si vas a usar valores distintos a los predeterminados. Ejemplo:
   ```bash
   export LOGIN_DB_HOST=localhost
   export LOGIN_DB_USER=fcytuader
   export LOGIN_DB_PASSWORD=programacionavanzada
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
5. Al enviar el formulario:
   - Si las credenciales son válidas, serás redirigido a `inicio.php` y se almacenarán en sesión el rol y la materia.
   - Si hay algún error, volverás a `index.php` con un mensaje y los campos completados (excepto la contraseña).

## Creación manual de la base de datos (alternativa)

Si prefieres preparar todo previamente o tu rol no puede crear bases de datos, ejecuta estas instrucciones una sola vez desde `psql` con un usuario con los privilegios necesarios:

```sql
CREATE DATABASE gestion_usuarios OWNER fcytuader;
\c gestion_usuarios

CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(100) UNIQUE NOT NULL,
    clave_hash TEXT NOT NULL,
    rol VARCHAR(50) NOT NULL,
    materia VARCHAR(100) NOT NULL
);

INSERT INTO usuarios (usuario, clave_hash, rol, materia)
VALUES (
    'fcytuader',
    crypt('programacionavanzada', gen_salt('bf')),
    'docente',
    'Programacion Avanzada'
)
ON CONFLICT (usuario) DO NOTHING;
```

> Si usas el `INSERT` manual anterior necesitarás tener habilitada la extensión `pgcrypto`. Alternativamente, puedes dejar que la aplicación genere el hash al primer inicio de sesión exitoso.

También puedes importar el script [`db/init.sql`](db/init.sql) una vez que la base `gestion_usuarios` exista y hayas ejecutado `CREATE EXTENSION pgcrypto` (si cuentas con privilegios):

```bash
psql -U fcytuader -h localhost -d gestion_usuarios -f db/init.sql
```

El script crea la tabla `usuarios` (si no existe) e inserta el usuario por defecto usando `crypt`.

## Usuarios iniciales y personalización

- El usuario por defecto (`fcytuader`) queda creado con una contraseña Bcrypt generada desde PHP en el primer arranque.
- Para agregar más cuentas manualmente puedes utilizar `password_hash` en un script PHP o la función `crypt` de PostgreSQL y ejecutar `INSERT ... ON CONFLICT DO NOTHING`.
- Para restablecer el acceso a los valores iniciales borra la fila correspondiente en la tabla y vuelve a cargar el formulario de inicio de sesión.

## Comprobaciones adicionales

- Desde PHP puedes ejecutar un lint rápido:
  ```bash
  php -l procesoLogin.php
  ```

## Limpieza de sesión

Cuando cierres la sesión en el navegador, borra las cookies correspondientes o usa la funcionalidad de cierre de sesión de la aplicación si está disponible. Esto garantiza que los datos de rol y materia almacenados en `$_SESSION` se eliminen correctamente.
