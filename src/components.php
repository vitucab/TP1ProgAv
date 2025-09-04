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

        <link rel="stylesheet" href="src/CSS/style.css">

        <!-- Favicon mínimo en data URL para evitar request 404 -->
        <link rel="icon" href="data:,">

        <!-- Ajustes para 0 scroll -->
        <style>
          /* Alturas fijas del header y footer (podés ajustar si cambiás paddings o tamaños) */
          :root { --hdr: 64px; --ftr: 64px; }

          html, body { height: 100%; overflow: hidden; } /* sin scroll */
          header.site-header { height: var(--hdr); }
          footer.site-footer { height: var(--ftr); }

          /* El main ocupa exactamente el espacio restante de la pantalla */
          main.fullscreen-center {
            height: calc(100dvh - var(--hdr) - var(--ftr));
          }
        </style>
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
    <header class="py-3 site-header"
            style="background: linear-gradient(90deg, #007BFF 0%, #6f42c1 100%); color: #fff;">
      <div class="container d-flex align-items-center justify-content-start h-100">
        <img src="src/img/lentes.png" alt="Logo" style="height: 35px; width: auto; margin-right: 10px;">
        <h1 class="h4 m-0">{$brand}</h1>
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
  $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
  $action = htmlspecialchars($base . '/procesoLogin.php', ENT_QUOTES, 'UTF-8');
  $usuarioPrefill = htmlspecialchars($_COOKIE['usuario'] ?? '', ENT_QUOTES, 'UTF-8');
  $checked = isset($_COOKIE['usuario']) ? 'checked' : '';
  $rolCookie     = $_COOKIE['rol'] ?? '';
  $rolAlumnoChk  = $rolCookie === 'alumno'  ? 'checked' : '';
  $rolDocenteChk = $rolCookie === 'docente' ? 'checked' : '';
  $rolCookie     = $_COOKIE['rol'] ?? ($rolCookie ?? '');
  $materias = [
    'Ingenieria en Software 2',
    'Bases de Datos',
    'Programacion Avanzada',
    'Probabilidad y Estadistica',
    'Paradigma y Lenguajes',
    'Sistemas Operativos',
  ];
  $materiaCookie = $_COOKIE['materia'] ?? '';
  if (!in_array($materiaCookie, $materias, true)) { $materiaCookie = ''; }
  $optionsMateria = '';
  foreach ($materias as $m) {
    $safe = htmlspecialchars($m, ENT_QUOTES, 'UTF-8');
    $sel  = ($m === $materiaCookie) ? ' selected' : '';
    $optionsMateria .= "<option value=\"{$safe}\"{$sel}>{$safe}</option>";
  }
  $labelMateria = ($rolCookie === 'docente')
    ? 'Materia a dictar clase particular'
    : 'Materia a solicitar clase particular';

  $bgPath = 'src/img/educacion.png';

  return <<<HTML
  <!-- CONTENIDO PRINCIPAL -->
  <main class="fullscreen-center d-flex align-items-center justify-content-center"
        style="
          background:
            linear-gradient(rgba(0,0,0,.35), rgba(0,0,0,.35)),
            url('{$bgPath}') center / cover no-repeat;
        ">
    <div class="container" style="max-width: 640px;">
      <div class="card shadow-lg border-0 bg-white bg-opacity-75" style="backdrop-filter: blur(4px); margin: 0;">
        <div class="card-body p-4">
          <h2 class="h4 mb-3 text-center">Iniciar Sesión</h2>

          <form id="form-contacto" method="POST" action="{$action}" novalidate>
            <div class="mb-3">
              <label class="form-label" for="usuario">Usuario</label>
              <input class="form-control" type="text" id="usuario" name="usuario"
                     value="{$usuarioPrefill}" required minlength="4" maxlength="50">
              <div class="invalid-feedback">Ingresá un usuario válido (mín. 4 caracteres).</div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="clave">Contraseña</label>
              <div class="input-group">
                <input class="form-control" type="password" id="clave" name="clave"
                       required minlength="6" autocomplete="current-password" aria-describedby="caps-hint">
                <button class="btn btn-outline-secondary" type="button" id="toggle-pass">Ver</button>
              </div>
              <div id="caps-hint" class="form-text text-warning d-none">Bloq Mayús activado</div>
              <div class="invalid-feedback">Ingresá tu contraseña (mín. 6 caracteres).</div>
            </div>

            <div class="mb-3">
              <label class="form-label d-block">Rol</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="rol" id="rol-alumno"
                       value="alumno" required {$rolAlumnoChk}>
                <label class="form-check-label" for="rol-alumno">Alumno</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="rol" id="rol-docente"
                       value="docente" required {$rolDocenteChk}>
                <label class="form-check-label" for="rol-docente">Docente</label>
              </div>
              <div class="form-text">Seleccioná tu rol.</div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="materia" id="label-materia">{$labelMateria}</label>
              <select class="form-select" id="materia" name="materia" required>
                {$optionsMateria}
              </select>
              <div class="form-text">Elegí una materia.</div>
            </div>

            <div class="form-check my-2">
              <input class="form-check-input" type="checkbox" id="recordarme" name="recordarme" value="1" {$checked}>
              <label class="form-check-label" for="recordarme">Recordarme</label>
            </div>

            <hr class="my-4">
            <button class="btn btn-primary w-100" type="submit">ENVIAR</button>
          </form>
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
    $year = date('Y');
    return <<<HTML
    <!-- FOOTER / Pie de página -->
    <footer class="py-4 mt-auto site-footer"
            style="background: linear-gradient(90deg, #007BFF 0%, #6f42c1 100%); color: #fff;">
      <div class="container text-center small h-100 d-flex align-items-center justify-content-center">
        &copy; {$year} Equipo de MiParticular. Todos los derechos reservados.
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
        <script>
          (() => {
            const pass = document.getElementById('clave');
            const btn  = document.getElementById('toggle-pass');
            const hint = document.getElementById('caps-hint');
            if (pass && btn) {
              btn.addEventListener('click', () => {
                const isPwd = pass.type === 'password';
                pass.type = isPwd ? 'text' : 'password';
                btn.textContent = isPwd ? 'Ocultar' : 'Ver';
              });
              pass.addEventListener('keydown', (e) => {
                if (!hint) return;
                if (e.getModifierState && e.getModifierState('CapsLock')) {
                  hint.classList.remove('d-none');
                } else {
                  hint.classList.add('d-none');
                }
              });
            }
          })();
          </script>
          <script>
          (() => {
            const label = document.getElementById('label-materia');
            function refreshMateriaLabel() {
              const r = document.querySelector('input[name="rol"]:checked');
              if (!label) return;
              label.textContent = (r && r.value === 'docente')
                ? 'Materia a dictar clase particular'
                : 'Materia a solicitar clase particular';
            }
            document.querySelectorAll('input[name="rol"]').forEach(el => {
              el.addEventListener('change', refreshMateriaLabel);
            });
            // set inicial
            refreshMateriaLabel();
          })();
          </script>
        <!-- JavaScript de Bootstrap 5 (con Popper) por CDN -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        HTML;
    }
?>