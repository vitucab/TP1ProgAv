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
     * - TailwindCSS por CDN
     * - favicon mínimo para evitar 404 en algunos navegadores
     *
     * @param string $title  Título de la pestaña/navegador (opcional)
     * @return string        Bloque de HTML para insertar dentro de <head>
     */
    function component_head(string $title = 'Mi Formulario'): string {
        return <<<HTML
        <!-- HEAD del documento -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{$title}</title>

        <!-- Tailwind CSS por CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
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
    <header class="py-3 bg-gradient-to-r from-blue-600 to-purple-700 text-white">
      <div class="container mx-auto flex items-center h-full">
        <img src="src/img/lentes.png" alt="Logo" class="h-9 w-auto mr-2">
        <h1 class="text-lg font-semibold">{$brand}</h1>
      </div>
    </header>
    HTML;
    }

    /**
     * component_navbar
     * ----------------
     * Barra de navegación (menú). 
     * Con Tailwind podemos armar un nav responsive con links simples.
     *
     * @return string  HTML de la <nav>
     */
    function component_navbar(): string {
        return <<<HTML
        <!-- NAVBAR -->
        <nav class="bg-gray-100 border-b">
          <div class="container mx-auto flex justify-between items-center p-3">
            <a href="#" class="text-blue-700 font-semibold">Formulario</a>
            <a href="#" class="text-gray-600 hover:text-blue-600">Ayuda</a>
          </div>
        </nav>
        HTML;
    }

    /**
     * component_main_form
     * -------------------
     * Sección principal (<main>) con un formulario estilizado con Tailwind.
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
      <main class="flex-1 flex items-center justify-center bg-cover bg-center" 
            style="background-image: linear-gradient(rgba(0,0,0,.35), rgba(0,0,0,.35)), url('{$bgPath}')">
        <div class="max-w-lg w-full bg-white/80 backdrop-blur-md shadow-lg rounded-lg p-6">
          <h2 class="text-xl font-bold text-center mb-4">Iniciar Sesión</h2>

          <form id="form-contacto" method="POST" action="{$action}" novalidate class="space-y-4">
            
            <div>
              <label for="usuario" class="block font-medium mb-1">Usuario</label>
              <input type="text" id="usuario" name="usuario"
                     value="{$usuarioPrefill}" required minlength="4" maxlength="50"
                     class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
              <p class="text-sm text-red-600 hidden">Ingresá un usuario válido (mín. 4 caracteres).</p>
            </div>

            <div>
              <label for="clave" class="block font-medium mb-1">Contraseña</label>
              <div class="flex">
                <input type="password" id="clave" name="clave" required minlength="6" autocomplete="current-password"
                       class="flex-1 border rounded-l px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <button type="button" id="toggle-pass" 
                        class="px-3 bg-gray-200 border border-l-0 rounded-r">Ver</button>
              </div>
              <p id="caps-hint" class="text-yellow-600 text-sm hidden">Bloq Mayús activado</p>
              <p class="text-sm text-red-600 hidden">Ingresá tu contraseña (mín. 6 caracteres).</p>
            </div>

            <div>
              <span class="block font-medium mb-1">Rol</span>
              <label class="mr-4">
                <input type="radio" name="rol" id="rol-alumno" value="alumno" required {$rolAlumnoChk}>
                Alumno
              </label>
              <label>
                <input type="radio" name="rol" id="rol-docente" value="docente" required {$rolDocenteChk}>
                Docente
              </label>
              <p class="text-sm text-gray-500">Seleccioná tu rol.</p>
            </div>

            <hr>
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700">
              ENVIAR
            </button>
          </form>
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
      <footer class="py-4 mt-auto bg-gradient-to-r from-blue-600 to-purple-700 text-white">
        <div class="container mx-auto text-center text-sm">
          &copy; {$year} Equipo de MiParticular. Todos los derechos reservados.
        </div>
      </footer>
      HTML;
    }

    /**
     * component_body_end_scripts
     * --------------------------
     * Scripts que van al final del <body>.
     * Incluye validación básica y toggle de password.
     *
     * @return string  Bloque <script>
     */
    function component_body_end_scripts(): string {
        return <<<HTML
        <!-- SCRIPTS al final del <body> -->
        <script>
          (() => {
            const form = document.getElementById('form-contacto');
            if (!form) return;
            form.addEventListener('submit', (e) => {
              if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
              }
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
                  hint.classList.remove('hidden');
                } else {
                  hint.classList.add('hidden');
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
            refreshMateriaLabel();
          })();
        </script>
        HTML;
    }
?>
