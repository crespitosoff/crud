// =========================================================
// Script principal: lógica de peticiones fetch y validación
// de la vista de pacientes. Todas las alertas usan SweetAlert2.
// =========================================================

// Referencias a los elementos del DOM que se usan en todo el flujo.
const formularioPaciente = document.querySelector('#formulario-paciente');
const cuerpoTabla        = document.querySelector('#cuerpo-pacientes');
const campoId            = document.querySelector('#paciente-id');
const fechaNacimiento    = document.querySelector('#fecha-nacimiento');
const documentoIdentidad = document.querySelector('#documento-identidad');
const botonEnviar        = document.querySelector('#boton-enviar');
const botonCancelar      = document.querySelector('#boton-cancelar');

/**
 * Fija el límite máximo de la fecha de nacimiento al día de hoy.
 * Esto bloquea en el calendario la selección de fechas futuras
 * ("no se admiten viajeros del tiempo").
 */
function establecerFechaMaxima() {
    const hoy  = new Date();
    const anio = hoy.getFullYear();
    const mes  = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia  = String(hoy.getDate()).padStart(2, '0');

    // El formato Y-m-d es el que exige el input type="date".
    fechaNacimiento.max = `${anio}-${mes}-${dia}`;
}

/**
 * Helper que centraliza TODOS los modales de SweetAlert2.
 * Evita repetir el objeto de configuración en cada respuesta.
 *
 * @param {string} icono  'success' | 'error' | 'warning'
 * @param {string} titulo Título del modal.
 * @param {string} texto  Cuerpo del mensaje.
 */
function mostrarSwal(icono, titulo, texto) {
    Swal.fire({
        icon  : icono,
        title : titulo,
        text  : texto,
    });
}

/**
 * Validación en JavaScript: es LA ÚNICA responsabilidad de validación
 * porque el formulario usa novalidate (alertas nativas apagadas).
 *
 * @returns {boolean} true si todos los datos son correctos; si no,
 *                    muestra una alerta estilizada y devuelve false.
 */
function validarFormulario() {
    // 1) Ningún campo marcado como "required" puede estar vacío.
    const campos = formularioPaciente.querySelectorAll('[required]');

    for (const campo of campos) {
        if (campo.value.trim() === '') {
            mostrarSwal('warning', 'Campos incompletos', 'Todos los campos obligatorios deben estar completos');

            return false;
        }
    }

    // 2) El documento debe ser exactamente 10 dígitos.
    if (/^\d{10}$/.test(documentoIdentidad.value) === false) {
        mostrarSwal('warning', 'Documento inválido', 'El documento de identidad debe contener exactamente 10 números');

        return false;
    }

    // 3) La fecha de nacimiento no puede ser posterior a hoy.
    if (fechaNacimiento.value !== '' && fechaNacimiento.value > fechaNacimiento.max) {
        mostrarSwal('warning', 'Fecha inválida', 'La fecha de nacimiento no puede ser posterior a la fecha actual');

        return false;
    }

    return true;
}

/**
 * Ajusta el texto/botones del formulario según el modo actual:
 * "registrar" (formulario vacío) o "actualizar" (campo id con valor).
 */
function actualizarEstadoFormulario() {
    const esEdicion = campoId.value !== '';

    botonEnviar.textContent = esEdicion ? 'Actualizar paciente' : 'Registrar paciente';
    botonCancelar.hidden    = esEdicion === false;
}

/**
 * Cancela la edición: limpia el formulario y el id oculto,
 * devolviendo el formulario al modo "registrar".
 */
function cancelarEdicion() {
    formularioPaciente.reset();
    actualizarEstadoFormulario();
}

/**
 * Manejador del envío del formulario (submit).
 * Valida primero; si es válido decide entre registrar o actualizar
 * según exista un id oculto, y envía la petición por fetch (POST).
 */
function enviarFormulario(evento) {
    // evento.preventDefault() evita el envío tradicional y el recargo
    // de la página, dejando que la petición se haga por AJAX.
    evento.preventDefault();

    if (validarFormulario() === false) {
        return;
    }

    const datos     = new FormData(formularioPaciente);
    const esEdicion = campoId.value !== '';

    // "accion" le dice al backend AJAX qué método debe invocar.
    datos.set('accion', esEdicion ? 'actualizar' : 'registrar');

    fetch('app/ajax/paciente_ajax.php', {
        method : 'POST',
        body   : datos,
    })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
            mostrarSwal(respuesta.exito ? 'success' : 'error', respuesta.exito ? 'Correcto' : 'Error', respuesta.mensaje);

            // Solo si la operación fue exitosa se limpia y recarga.
            if (respuesta.exito) {
                campoId.value = '';
                formularioPaciente.reset();
                actualizarEstadoFormulario();
                cargarPacientes();
            }
        })
        .catch(() => {
            mostrarSwal('error', 'Error', 'Ocurrió un error al guardar el paciente');
        });
}

/**
 * Carga los datos del paciente seleccionado en el formulario
 * para editar. Al fijar el id oculto, el siguiente envío será un
 * UPDATE (acción "actualizar" en lugar de "registrar").
 */
function cargarFormularioEdicion(paciente) {
    campoId.value                            = paciente.id;
    formularioPaciente.primer_nombre.value   = paciente.primer_nombre;
    formularioPaciente.segundo_nombre.value  = paciente.segundo_nombre;
    formularioPaciente.primer_apellido.value = paciente.primer_apellido;
    formularioPaciente.segundo_apellido      = paciente.segundo_apellido;

    documentoIdentidad.value         = paciente.documento_identidad;
    fechaNacimiento.value            = paciente.fecha_nacimiento;
    formularioPaciente.genero.value  = paciente.genero;
    formularioPaciente.sede_id.value = paciente.sede_id;

    actualizarEstadoFormulario();
    formularioPaciente.scrollIntoView({ behavior : 'smooth' });
}

/**
 * Elimina un paciente, pero antes pide confirmación con un modal
 * estilizado. Solo ejecuta el fetch si el usuario confirma.
 */
function eliminarPaciente(id) {
    Swal.fire({
        title              : '¿Eliminar paciente?',
        text               : 'Esta acción no se puede deshacer',
        icon               : 'warning',
        showCancelButton   : true,
        confirmButtonColor : '#d33',
        confirmButtonText  : 'Sí, eliminar',
        cancelButtonText   : 'Cancelar',
    }).then((resultado) => {
        // isConfirmed es true solo si el usuario pulsó el botón "Sí".
        if (resultado.isConfirmed === false) {
            return;
        }

        const datos = new FormData();

        datos.append('accion', 'eliminar');
        datos.append('id', id);

        fetch('app/ajax/paciente_ajax.php', {
            method : 'POST',
            body   : datos,
        })
            .then((respuesta) => respuesta.json())
            .then((respuesta) => {
                mostrarSwal(respuesta.exito ? 'success' : 'error', respuesta.exito ? 'Correcto' : 'Error', respuesta.mensaje);

                if (respuesta.exito) {
                    cargarPacientes();
                }
            })
            .catch(() => {
                mostrarSwal('error', 'Error', 'Ocurrió un error al eliminar el paciente');
            });
    });
}

/**
 * Construye una fila (<tr>) de la tabla con los datos de un paciente
 * y le adjunta los manejadores de los botones Editar y Eliminar.
 *
 * @param {object} paciente Datos ya sanos (sin null) provenientes del backend.
 * @returns {HTMLTableRowElement} Fila lista para insertar en el tbody.
 */
function crearFila(paciente) {
    const fila = document.createElement('tr');

    // Se usa innerHTML porque los datos vienen escapados del backend
    // (htmlspecialchars) y los campos opcionales ya son "" (nunca null).
    fila.innerHTML = `
        <td>${paciente.primer_nombre} ${paciente.segundo_nombre}</td>
        <td>${paciente.primer_apellido} ${paciente.segundo_apellido}</td>
        <td>${paciente.documento_identidad}</td>
        <td>${paciente.fecha_nacimiento}</td>
        <td>${paciente.genero}</td>
        <td>${paciente.sede}</td>
        <td>
            <button type="button" class="accion-editar" data-id="${paciente.id}">Editar</button>
            <button type="button" class="accion-eliminar" data-id="${paciente.id}">Eliminar</button>
        </td>
    `;

    // Se enlaza el evento de cada botón con su función correspondiente.
    fila.querySelector('.accion-editar').addEventListener('click', () => cargarFormularioEdicion(paciente));
    fila.querySelector('.accion-eliminar').addEventListener('click', () => eliminarPaciente(paciente.id));

    return fila;
}

/**
 * Obtiene todos los pacientes (acción "listar") y vuelca su resultado
 * en el tbody de la tabla, regenerando todas las filas.
 */
function cargarPacientes() {
    const datos = new FormData();

    datos.append('accion', 'listar');

    fetch('app/ajax/paciente_ajax.php', {
        method : 'POST',
        body   : datos,
    })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
            // Si el backend reportó fallo, se muestra y no se toca la tabla.
            if (respuesta.exito === false) {
                mostrarSwal('error', 'Error', respuesta.mensaje);

                return;
            }

            // Se vacía el tbody antes de reconstruirlo (evita filas duplicadas).
            cuerpoTabla.innerHTML = '';

            respuesta.pacientes.forEach((paciente) => {
                cuerpoTabla.appendChild(crearFila(paciente));
            });
        })
        .catch(() => {
            mostrarSwal('error', 'Error', 'Ocurrió un error al cargar los pacientes');
        });
}

// Al terminar de cargar el DOM se prepara todo lo necesario:
// límite de fecha, envío del formulario, cancelar edición y listado.
document.addEventListener('DOMContentLoaded', () => {
    establecerFechaMaxima();
    formularioPaciente.addEventListener('submit', enviarFormulario);
    botonCancelar.addEventListener('click', cancelarEdicion);
    cargarPacientes();
});