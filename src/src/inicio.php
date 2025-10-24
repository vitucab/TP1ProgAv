<?php
    /**
     * ==========================================
     * layout.php — Versión documentada
     * ==========================================
     *
     * ¿Qué hace este archivo?
     * -----------------------
     * Define la función setDom(), que arma la PÁGINA COMPLETA en memoria
     * como un único string de HTML (doctype, html, head, body y todas las
     * secciones internas).
     *
     * No imprime nada directamente. Quien decide cuándo mostrarlo es
     * index.php (u otra puerta de entrada) mediante: echo setDom([...]).
     */

    // Tipado estricto: si una función pide string y le mandás un int, lanza error.
    declare(strict_types=1);

    // Importamos las funciones de componentes (head, header, navbar, main, footer, scripts)
    // __DIR__ es la carpeta actual de este archivo; components.php está al lado (en /src).
    require_once __DIR__ . '/components.php';

    /**
     * setDom
     * ------
     * Une todos los componentes en el orden correcto y devuelve el HTML final
     * como TEXT0 (string). NO imprime.
     *
     * @param array $opts Opciones para personalizar la vista:
     *   - 'title' (string): Título de la pestaña del navegador.
     *   - 'brand' (string): Texto/marca que aparece en el header.
     *   - 'main'  (string): Contenido principal (<main>...</main>) ya renderizado.
     *                       Si no lo pasás, se usa por defecto component_main_form().
     *   - 'showUser' (bool): Si es true y hay sesión, muestra "Logueado como" en el header.
     *
     * @return string HTML completo del documento.
     */
    function setDom(array $opts = []): string {
        // Valores por defecto si no se pasan en $opts:
        $title  = $opts['title']  ?? 'Formulario';   // Título de la <title>
        $brand  = $opts['brand']  ?? 'Mi Sitio';     // Texto que se ve en el <header>
        $main   = $opts['main']   ?? component_main_form(); // <main> por defecto: el formulario
        $showUser = (bool)($opts['showUser'] ?? false);     // Mostrar usuario logueado en el header

        // Invocamos cada componente para obtener su HTML como string:
        $head   = component_head($title);
        $header = component_header($brand, $showUser);
        $nav    = component_navbar();
        $footer = component_footer();
        $scripts= component_body_end_scripts();

        // Armamos el documento completo usando HEREDOC (bloque largo de texto).
        // Interpolamos las variables {$head}, {$header}, etc. dentro del bloque.
        $html = <<<HTML
        <!doctype html>
        <html lang="es">
        <head>
        {$head}
        </head>
        <body class="min-h-screen flex flex-col">
        {$header}
        {$nav}
        {$main}
        {$footer}
        {$scripts}
        </body>
        </html>
        HTML;

        // Devolvemos el HTML armado; NO imprimimos acá.
        return $html;
    }
?>
