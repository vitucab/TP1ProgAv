<?php
    /**
     * ==========================================
     * index.php — Versión documentada
     * ==========================================
     *
     * ¿Qué hace este archivo?
     * -----------------------
     * Es la “puerta de entrada” del sitio.
     * - Importa el layout (estructura de la página).
     * - Llama a setDom() para construir la página completa.
     * - La imprime en el navegador con echo.
     *
     * IMPORTANTE: Por la condición del proyecto, NO hay HTML fuera de PHP.
     */

    // Tipado estricto activado (si una función pide string y pasamos int, error).
    declare(strict_types=1);

    // Traemos el archivo layout.php, que contiene la función setDom()
    // __DIR__ significa “la carpeta donde está este archivo”.
    // OJO: faltaba una barra en tu require_once. Debe ser '/src/layout.php'
    require_once __DIR__ . '/src/layout.php';

    /**
     * Acá armamos la página usando setDom().
     *
     * Pasamos un array de opciones:
     * - title: lo que aparece en la pestaña del navegador (<title>)
     * - brand: el nombre que aparece en el header
     * - main: (opcional) bloque <main>... personalizado
     *
     * Si no se pasa 'main', se usa por defecto component_main_form()
     * definido en components.php.
     */
    echo setDom([
        'title' => 'Formulario de Contacto',
        'brand' => 'Buscar Particular',

        // 'main' => component_main_form(), // Ejemplo: podés cambiar el contenido principal
    ]);
?>
