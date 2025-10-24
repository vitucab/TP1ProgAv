<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['autenticado']) || empty($_SESSION['usuario_nombre'])) {
    $_SESSION['login_error'] = 'Debe iniciar sesión para acceder al panel.';
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/src/inicio.php';

$usuario = htmlspecialchars((string)$_SESSION['usuario_nombre'], ENT_QUOTES, 'UTF-8');

$main = <<<HTML
<main class="flex-1 flex items-center justify-center bg-slate-100 py-12">
  <div class="bg-white shadow-lg rounded-lg p-8 text-center space-y-4 max-w-xl w-full mx-4">
    <h2 class="text-2xl font-bold text-gray-800">Bienvenido/a al sitio</h2>
    <p class="text-gray-600">Hola <span class="font-semibold">{$usuario}</span>, gracias por iniciar sesión.</p>
    <a class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded" href="index.php">Volver al inicio</a>
  </div>
</main>
HTML;

echo setDom([
    'title' => 'Inicio',
    'brand' => 'Buscar Particular',
    'main'  => $main,
    'showUser' => true,
]);
