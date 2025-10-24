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

function verify_password(string $password, string $hash): bool
{
    if ($hash === '') {
        return false;
    }

    $info = password_get_info($hash);
    if ($info['algo'] !== 0) {
        return password_verify($password, $hash);
    }

    $crypt = @crypt($password, $hash);
    if (!is_string($crypt) || strlen($crypt) !== strlen($hash)) {
        return false;
    }

    return hash_equals($crypt, $hash);
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

try {
    $pdo = createOrOpenDatabase();
} catch (Throwable $e) {
    redirect_with_error('No se pudo conectar a la base de datos.', $oldValues);
}

$stmt = $pdo->prepare('SELECT clave_hash, rol, materia FROM usuarios WHERE usuario = :usuario LIMIT 1');
$stmt->execute(['usuario' => $usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user === false) {
    redirect_with_error('Usuario o contraseña incorrectos.', $oldValues);
}

if (!verify_password($clave, (string)$user['clave_hash'])) {
    redirect_with_error('Usuario o contraseña incorrectos.', $oldValues);
}

if ((string)$user['rol'] !== $rol || (string)$user['materia'] !== $materia) {
    redirect_with_error('Los datos seleccionados no corresponden al usuario.', $oldValues);
}

$_SESSION['usuario_rol'] = $user['rol'];
$_SESSION['usuario_materia'] = $user['materia'];

session_regenerate_id(true);
$_SESSION['autenticado'] = true;
$_SESSION['usuario_nombre'] = $usuario;
unset($_SESSION['captcha']);

header('Location: inicio.php');
exit;

function createOrOpenDatabase(): PDO
{
    $config = databaseConfig();
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $dsn = buildDsn($config['host'], $config['port'], $config['dbname']);

    try {
        $pdo = new PDO($dsn, $config['user'], $config['password'], $options);
    } catch (PDOException $exception) {
        if ($exception->getCode() !== '3D000') {
            throw $exception;
        }

        bootstrapDatabase($config, $options);
        $pdo = new PDO($dsn, $config['user'], $config['password'], $options);
    }

    ensureSchema($pdo);

    return $pdo;
}

/**
 * @return array{host: string, port: string, dbname: string, maintenance_db: string, user: string, password: string}
 */
function databaseConfig(): array
{
    return [
        'host' => getenv('LOGIN_DB_HOST') ?: 'localhost',
        'port' => getenv('LOGIN_DB_PORT') ?: '5432',
        'dbname' => getenv('LOGIN_DB_NAME') ?: 'gestion_usuarios',
        'maintenance_db' => getenv('LOGIN_DB_MAINTENANCE') ?: 'postgres',
        'user' => getenv('LOGIN_DB_USER') ?: 'fcytuader',
        'password' => getenv('LOGIN_DB_PASSWORD') ?: 'programacionavanzada',
    ];
}

function buildDsn(string $host, string $port, string $dbname): string
{
    $safePort = preg_match('/^\\d+$/', $port) === 1 ? $port : '5432';

    return sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $safePort, $dbname);
}

/**
 * @param array{host: string, port: string, dbname: string, maintenance_db: string, user: string, password: string} $config
 * @param array<int, mixed> $options
 */
function bootstrapDatabase(array $config, array $options): void
{
    validateIdentifier($config['dbname']);
    validateIdentifier($config['maintenance_db']);

    $maintenanceDsn = buildDsn($config['host'], $config['port'], $config['maintenance_db']);

    try {
        $maintenancePdo = new PDO($maintenanceDsn, $config['user'], $config['password'], $options);
    } catch (PDOException $exception) {
        throw new RuntimeException('No se pudo crear la base de datos automáticamente.', 0, $exception);
    }

    $databaseName = quoteIdentifier($config['dbname']);

    try {
        $maintenancePdo->exec('CREATE DATABASE ' . $databaseName);
    } catch (PDOException $exception) {
        if ($exception->getCode() !== '42P04') {
            throw new RuntimeException('No se pudo crear la base de datos automáticamente.', 0, $exception);
        }
    }
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
                id SERIAL PRIMARY KEY,
                usuario VARCHAR(100) UNIQUE NOT NULL,
                clave_hash TEXT NOT NULL,
                rol VARCHAR(50) NOT NULL,
                materia VARCHAR(100) NOT NULL
            )'
        );

        $seed = seedConfig();

        if ($seed['usuario'] !== '' && $seed['password'] !== '') {
            $stmt = $pdo->prepare(
                'INSERT INTO usuarios (usuario, clave_hash, rol, materia)
                VALUES (:usuario, :clave_hash, :rol, :materia)
                ON CONFLICT (usuario) DO NOTHING'
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

function validateIdentifier(string $identifier): void
{
    if ($identifier === '' || str_contains($identifier, "\0")) {
        throw new RuntimeException('Nombre de base de datos inválido.');
    }
}

function quoteIdentifier(string $identifier): string
{
    return '"' . str_replace('"', '""', $identifier) . '"';
}
