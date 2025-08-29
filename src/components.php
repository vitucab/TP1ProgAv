<?php
    /**
     * ==========================================
     * components.php — Versión documentada
     * ==========================================
     *
     * ¿Qué hace este archivo?
     * -----------------------
     * Define funciones que generan "componentes" de la página
     * (head, header, navbar, main con formulario, footer y scripts)
     * y los devuelve como STRINGS de HTML.
     *
     * ¿Por qué como strings?
     * ----------------------
     * Porque en este proyecto NO se permite escribir HTML "suelto"
     * fuera de PHP. Entonces armamos el HTML dentro de funciones y
     * lo retornamos como texto para que otro archivo (layout.php) lo
     * pegue en su lugar.
     *
     * ¿Qué es HEREDOC?
     * ----------------
     * Es una sintaxis de PHP para escribir bloques largos de texto
     * sin tener que escapar comillas ni concatenar línea por línea.
     *
     *   $html = <<<HTML
     *   <h1>Título</h1>
     *   <p>Texto</p>
     *   HTML;
     *
     * Todo lo entre <<<HTML y HTML; es un string literal.
     * Dentro de HEREDOC podés interpolar variables como {$variable}.
     *
     * Sobre declare(strict_types=1):
     * ------------------------------
     * Activa "tipado estricto". Si una función pide un string y le
     * pasás un número, PHP lanzará error. Esto nos obliga a ser más
     * prolijos y evita errores silenciosos.
     */

    declare(strict_types=1);

    /**
     * component_head
     * --------------
     * Genera las etiquetas típicas del <head> del documento:
     * - charset
     * - viewport (responsivo)
     * - <title> (dinámico por parámetro)
     * - CSS de Bootstrap por CDN
     * - favicon mínimo para evitar 404 en algunos navegadores
     *
     * @param string $title  Título de la pestaña/navegador (opcional)
     * @return string        Bloque de HTML para insertar dentro de <head>
     */
    function component_head(string $title = 'Mi Formulario'): string {
        // Usamos HEREDOC: el bloque empieza con <<<HTML y termina con HTML;
        // Dentro se puede interpolar {$title}
        return <<<HTML
        <!-- HEAD del documento -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{$title}</title>

        <!-- Bootstrap 5: estilos por CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Favicon mínimo en data URL para evitar request 404 -->
        <link rel="icon" href="data:,">
        HTML;
    }

    /**
     * component_header
     * ----------------
     * Renderiza la franja superior del sitio (<header>) con el nombre
     * de la “marca” (brand). Es una sección estática arriba del todo.
     *
     * @param string $brand  Texto que aparece como título/logotipo
     * @return string        HTML del <header>
     */
    function component_header(string $brand = 'Mi Sitio'): string {
        return <<<HTML
        <!-- HEADER superior del sitio -->
        <header class="py-3 border-bottom bg-white">
          <div class="container d-flex align-items-center justify-content-between">
            <h1 class="h4 m-0">{$brand}</h1>
            <span class="text-muted small">Demo PHP-only DOM</span>
          </div>
        </header>
        HTML;
    }

    /**
     * component_navbar
     * ----------------
     * Barra de navegación (menú) hecha con Bootstrap.
     * Incluye botón “hamburguesa” para móviles y un par de links
     * de ejemplo (“Formulario” y “Ayuda”).
     *
     * @return string  HTML de la <nav> de Bootstrap
     */
    function component_navbar(): string {
        return <<<HTML
        <!-- NAVBAR / Menú principal -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
          <div class="container">
            <a class="navbar-brand" href="#">Inicio</a>

            <!-- Botón de colapso (móviles) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido colapsable -->
            <div id="navMain" class="collapse navbar-collapse">
              <ul class="navbar-nav me-auto">
                <li class="nav-item">
                  <a class="nav-link active" href="#">Formulario</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Ayuda</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        HTML;
    }

    /**
     * component_main_form
     * -------------------
     * Sección principal (<main>) con una tarjeta (card) de Bootstrap
     * que contiene un formulario mínimo de contacto.
     *
     * IMPORTANTE:
     * - method="POST": el form se envía como POST (seguro para datos)
     * - action="procesar.php": archivo que recibirá y validará la info
     * - novalidate: desactiva validación HTML nativa; usaremos JS + servidor
     * - Atributos "required", "minlength", "maxlength" ayudan a validar en front
     *
     * @return string  HTML del <main> con el formulario
     */
    function component_main_form(): string {
        // Este formulario solo cubre la parte "front" (interfaz).
        // La validación real y persistencia se hace del lado servidor (procesar.php).
        return <<<HTML
        <!-- CONTENIDO PRINCIPAL -->
        <main class="container my-4">
          <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
              <div class="card shadow-sm">
                <div class="card-body">

                  <h2 class="h4 mb-3">Formulario de contacto</h2>

                  <!-- Formulario: se envía a procesar.php por POST -->
                  <form id="form-contacto" method="POST" action="procesar.php" novalidate>
                    <!-- Campo: Nombre -->
                    <div class="mb-3">
                      <label class="form-label" for="nombre">Nombre</label>
                      <input
                        class="form-control"
                        type="text"
                        id="nombre"
                        name="nombre"
                        required
                        minlength="2"
                        maxlength="80"
                      >
                      <div class="invalid-feedback">Ingresá un nombre válido (mín. 2).</div>
                    </div>

                    <!-- Campo: Email -->
                    <div class="mb-3">
                      <label class="form-label" for="email">Email</label>
                      <input
                        class="form-control"
                        type="email"
                        id="email"
                        name="email"
                        required
                      >
                      <div class="invalid-feedback">Ingresá un email válido.</div>
                    </div>

                    <!-- Campo: Mensaje -->
                    <div class="mb-3">
                      <label class="form-label" for="mensaje">Mensaje</label>
                      <textarea
                        class="form-control"
                        id="mensaje"
                        name="mensaje"
                        rows="4"
                        required
                        maxlength="500"
                      ></textarea>
                      <div class="invalid-feedback">El mensaje es obligatorio (máx. 500).</div>
                    </div>

                    <!-- Botón enviar -->
                    <button class="btn btn-primary w-100" type="submit">Enviar</button>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </main>
        HTML;
    }

    /**
     * component_footer
     * ----------------
     * Pie de página (<footer>) con el año actual generado dinámicamente.
     *
     * @return string  HTML del <footer>
     */
    function component_footer(): string {
        // Obtenemos el año actual para el copyright
        $year = date('Y');

        return <<<HTML
        <!-- FOOTER / Pie de página -->
        <footer class="bg-light py-4 mt-auto border-top">
          <div class="container text-center text-muted small">
            &copy; {$year} Equipo de Formulario. Todos los derechos reservados.
          </div>
        </footer>
        HTML;
    }

    /**
     * component_body_end_scripts
     * --------------------------
     * Scripts que van al final del <body>:
     * - Un pequeño IIFE en JS que agrega la clase "was-validated" si hay
     *   campos inválidos, siguiendo el patrón de validación de Bootstrap.
     * - El bundle JS de Bootstrap (incluye Popper) por CDN.
     *
     * ¿Por qué al final del body?
     * ---------------------------
     * Para que el HTML se cargue primero y el JS no bloquee el render.
     *
     * @return string  Bloque <script> + JS de Bootstrap
     */
    function component_body_end_scripts(): string {
        return <<<HTML
        <!-- SCRIPTS al final del <body> -->
        <script>
          // IIFE: Immediately Invoked Function Expression (función que se ejecuta sola)
          (() => {
            const form = document.getElementById('form-contacto');
            if (!form) return; // Si no existe el form en esta página, no hace nada

            form.addEventListener('submit', (e) => {
              // checkValidity() usa reglas HTML5 (required, minlength, type="email", etc.)
              if (!form.checkValidity()) {
                // Si hay errores, prevenimos el envío y mostramos estilos de error
                e.preventDefault();
                e.stopPropagation();
              }
              // Bootstrap muestra los mensajes de error al tener esta clase
              form.classList.add('was-validated');
            });
          })();
        </script>

        <!-- JavaScript de Bootstrap 5 (con Popper) por CDN -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        HTML;
    }
?>
