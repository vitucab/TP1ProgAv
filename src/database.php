<?php
declare(strict_types=1);

/**
 * Garantiza que la base de datos exista y tenga la estructura mínima.
 *
 * @throws RuntimeException si no se puede crear el archivo o la tabla.
 */
function initializeDatabase(): void
{
    static $initialized = false;

    if ($initialized) {
        return;
    }

    $initialized = true;
    $pdo = createOrOpenDatabase();
    $pdo = null;
}

function createOrOpenDatabase(): PDO
{
    $config = databaseConfig();
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $path = $config['path'];
    $directory = dirname($path);

    if (!is_dir($directory)) {
        if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('No se pudo crear el directorio para la base de datos.');
        }
    }

    try {
        $pdo = new PDO('sqlite:' . $path, null, null, $options);
    } catch (PDOException $exception) {
        throw new RuntimeException('No se pudo abrir la base de datos.', 0, $exception);
    }

    $pdo->exec('PRAGMA foreign_keys = ON');

    ensureSchema($pdo);

    return $pdo;
}

/**
 * @return array{path: string}
 */
function databaseConfig(): array
{
    $envPath = getenv('LOGIN_DB_PATH');
    $path = ($envPath !== false && $envPath !== '') ? $envPath : __DIR__ . '/../data/login.sqlite';

    return [
        'path' => $path,
    ];
}

function ensureSchema(PDO $pdo): void
{
    $inTransaction = false;

    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $inTransaction = true;
    }

    try {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario TEXT UNIQUE NOT NULL,
                clave_hash TEXT NOT NULL,
                rol TEXT NOT NULL,
                materia TEXT NOT NULL
            )'
        );

        $seed = seedConfig();

        if ($seed['usuario'] !== '' && $seed['password'] !== '') {
            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (usuario, clave_hash, rol, materia)
                VALUES (:usuario, :clave_hash, :rol, :materia)
                ON CONFLICT(usuario) DO NOTHING'
            );

            $stmt->execute([
                'usuario' => $seed['usuario'],
                'clave_hash' => password_hash($seed['password'], PASSWORD_BCRYPT),
                'rol' => $seed['rol'],
                'materia' => $seed['materia'],
            ]);
        }

        if ($inTransaction) {
            $pdo->commit();
        }
    } catch (Throwable $exception) {
        if ($inTransaction && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}

/**
 * @return array{usuario: string, password: string, rol: string, materia: string}
 */
function seedConfig(): array
{
    return [
        'usuario' => getenv('LOGIN_SEED_USER') ?: 'fcytuader',
        'password' => getenv('LOGIN_SEED_PASSWORD') ?: 'programacionavanzada',
        'rol' => getenv('LOGIN_SEED_ROLE') ?: 'docente',
        'materia' => getenv('LOGIN_SEED_SUBJECT') ?: 'Programacion Avanzada',
    ];
}
