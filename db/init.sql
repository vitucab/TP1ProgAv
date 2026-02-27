-- Esquema base para la aplicación de gestión de usuarios.
-- Ejecutar conectado a la base `gestion_usuarios`.
-- Si pgcrypto no está disponible omite la línea CREATE EXTENSION
-- y deja que PHP genere los hashes al primer inicio de sesión.
-- El flujo automático usa las variables LOGIN_SEED_* descritas en el README
-- para personalizar usuario, contraseña, rol y materia iniciales.

CREATE EXTENSION IF NOT EXISTS pgcrypto;

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
