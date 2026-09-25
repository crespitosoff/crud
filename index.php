<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pacientes - Clínica</title>
</head>
<body>
    <main>
        <h1>Gestión de Pacientes</h1>

        <form id="formulario-paciente" method="POST">
            <input type="hidden" id="paciente-id" name="id">

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

            <fieldset>
                <legend>Datos del documento</legend>

                <label for="documento-identidad">Documento de identidad <span>*</span></label>
                <input type="text" id="documento-identidad" name="documento_identidad"
                       minlength="10" maxlength="10" pattern="\d{10}" required>

                <label for="fecha-nacimiento">Fecha de nacimiento <span>*</span></label>
                <input type="date" id="fecha-nacimiento" name="fecha_nacimiento" required>
            </fieldset>

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

            <button type="submit" id="boton-enviar">Registrar paciente</button>
            <button type="button" id="boton-cancelar" hidden>Cancelar edición</button>
        </form>

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

    <script src="app/assets/js/main.js"></script>
</body>
</html>