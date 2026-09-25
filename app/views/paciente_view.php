<!DOCTYPE html>
<!--
    Vista principal del módulo de Pacientes
    Archivo: app/views/paciente_view.php

    Es la única responsable de la presentación (MVC puro). No contiene
    lógica de negocio: solo muestra el formulario y el listado, y conecta
    los scripts (SweetAlert2 y main.js) que orquestan la interactividad.

    NOTA DE SEGURIDAD (XSS): si en esta vista se llegara a imprimir algún
    dato proveniente de PHP usando la etiqueta corta de eco ("?" + "="),
    esa salida debe envolverse SIEMPRE en htmlspecialchars(), por ejemplo:
        echo htmlspecialchars($dato, ENT_QUOTES, 'UTF-8')
    para impedir la inyección de HTML/JavaScript.
-->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pacientes - Clínica</title>
</head>
<body>
    <main>
        <h1>Gestión de Pacientes</h1>

        <!-- =====================================================
             SECCIÓN: FORMULARIO DE REGISTRO/EDICIÓN
             El atributo novalidate apaga las alertas nativas del
             navegador; la validación la asume main.js (con Swal.fire).
             El input oculto "id" se usa para distinguir "registrar"
             de "actualizar" según tenga valor o no.
             ===================================================== -->
        <form id="formulario-paciente" method="POST" novalidate>
            <input type="hidden" id="paciente-id" name="id">

            <!-- Datos personales del paciente -->
            <fieldset>
                <legend>Datos personales</legend>

                <label for="primer-nombre">Primer nombre <span>*</span></label>
                <input type="text" id="primer-nombre" name="primer_nombre" required>

                <label for="segundo-nombre">Segundo nombre</label>
                <input type="text" id="segundo-nombre" name="segundo_nombre">

                <label for="primer-apellido">Primer apellido <span>*</span></label>
                <input type="text" id="primer-apellido" name="primer_apellido" required>

                <label for="segundo-apellido">Segundo apellido</label>
                <input type="text" id="segundo-apellido" name="segundo_apellido">
            </fieldset>

            <!-- Documento de identidad y fecha de nacimiento -->
            <fieldset>
                <legend>Datos del documento</legend>

                <label for="documento-identidad">Documento de identidad <span>*</span></label>
                <!-- oninput filtra las letras físicamente: solo números y
                     máximo 10 dígitos. La validación final la hace main.js. -->
                <input type="text" id="documento-identidad" name="documento_identidad"
                       minlength="10" maxlength="10" pattern="\d{10}" required
                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">

                <label for="fecha-nacimiento">Fecha de nacimiento <span>*</span></label>
                <!-- El atributo max se asigna dinámicamente desde main.js
                     para bloquear fechas futuras en el calendario. -->
                <input type="date" id="fecha-nacimiento" name="fecha_nacimiento" required>
            </fieldset>

            <!-- Género y sede (sede quemada en el select por ahora) -->
            <fieldset>
                <legend>Información adicional</legend>

                <label for="genero">Género <span>*</span></label>
                <select id="genero" name="genero" required>
                    <option value="">Seleccione una opción</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>

                <label for="sede">Sede <span>*</span></label>
                <select id="sede" name="sede_id" required>
                    <option value="">Seleccione una opción</option>
                    <option value="1">Sede 1</option>
                    <option value="2">Sede 2</option>
                </select>
            </fieldset>

            <!-- Botones de acción del formulario -->
            <button type="submit" id="boton-enviar">Registrar paciente</button>
            <button type="button" id="boton-cancelar" hidden>Cancelar edición</button>
        </form>

        <!-- =====================================================
             SECCIÓN: LISTADO DE PACIENTES
             El <tbody> es llenado dinámicamente por main.js mediante
             la acción "listar"; cada fila incluye los botones Editar
             y Eliminar en la columna de Acciones.
             ===================================================== -->
        <section>
            <h2>Listado de pacientes</h2>

            <table id="tabla-pacientes">
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Documento</th>
                        <th>Fecha de nacimiento</th>
                        <th>Género</th>
                        <th>Sede</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-pacientes"></tbody>
            </table>
        </section>
    </main>

    <!-- =====================================================
         SECCIÓN: SCRIPTS
         SweetAlert2 (CDN) genera los modales estilizados; main.js
         contiene toda la lógica de peticiones fetch y validación.
         ===================================================== -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="app/assets/js/main.js"></script>
</body>
</html>