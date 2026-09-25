const formularioPaciente = document.querySelector('#formulario-paciente');
const cuerpoTabla        = document.querySelector('#cuerpo-pacientes');
const campoId            = document.querySelector('#paciente-id');
const fechaNacimiento    = document.querySelector('#fecha-nacimiento');
const documentoIdentidad = document.querySelector('#documento-identidad');
const botonEnviar        = document.querySelector('#boton-enviar');
const botonCancelar      = document.querySelector('#boton-cancelar');

function establecerFechaMaxima() {
    const hoy  = new Date();
    const anio = hoy.getFullYear();
    const mes  = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia  = String(hoy.getDate()).padStart(2, '0');

    fechaNacimiento.max = `${anio}-${mes}-${dia}`;
}

function validarFormulario() {
    const campos = formularioPaciente.querySelectorAll('[required]');

    for (const campo of campos) {
        if (campo.value.trim() === '') {
            alert('Todos los campos obligatorios deben estar completos');

            return false;
        }
    }

    if (/^\d{10}$/.test(documentoIdentidad.value) === false) {
        alert('El documento de identidad debe contener exactamente 10 números');

        return false;
    }

    if (fechaNacimiento.value !== '' && fechaNacimiento.value > fechaNacimiento.max) {
        alert('La fecha de nacimiento no puede ser posterior a la fecha actual');

        return false;
    }

    return true;
}

function actualizarEstadoFormulario() {
    const esEdicion = campoId.value !== '';

    botonEnviar.textContent   = esEdicion ? 'Actualizar paciente' : 'Registrar paciente';
    botonCancelar.hidden      = esEdicion === false;
}

function cancelarEdicion() {
    formularioPaciente.reset();
    actualizarEstadoFormulario();
}

function enviarFormulario(evento) {
    evento.preventDefault();

    if (validarFormulario() === false) {
        return;
    }

    const datos     = new FormData(formularioPaciente);
    const esEdicion = campoId.value !== '';

    datos.set('accion', esEdicion ? 'actualizar' : 'registrar');

    fetch('app/ajax/paciente_ajax.php', {
        method : 'POST',
        body   : datos,
    })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
            alert(respuesta.mensaje);

            if (respuesta.exito) {
                campoId.value = '';
                formularioPaciente.reset();
                actualizarEstadoFormulario();
                cargarPacientes();
            }
        })
        .catch(() => {
            alert('Ocurrió un error al guardar el paciente');
        });
}

function editarPaciente(paciente) {
    campoId.value                          = paciente.id;
    formularioPaciente.primer_nombre.value = paciente.primer_nombre;
    formularioPaciente.segundo_nombre.value = paciente.segundo_nombre || '';
    formularioPaciente.primer_apellido.value = paciente.primer_apellido;
    formularioPaciente.segundo_apellido.value = paciente.segundo_apellido || '';
    documentoIdentidad.value                = paciente.documento_identidad;
    fechaNacimiento.value                   = paciente.fecha_nacimiento;
    formularioPaciente.genero.value         = paciente.genero;
    formularioPaciente.sede_id.value        = paciente.sede_id;

    actualizarEstadoFormulario();
    formularioPaciente.scrollIntoView({ behavior : 'smooth' });
}

function eliminarPaciente(id) {
    if (confirm('¿Está seguro de eliminar este paciente?') === false) {
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
            alert(respuesta.mensaje);

            if (respuesta.exito) {
                cargarPacientes();
            }
        })
        .catch(() => {
            alert('Ocurrió un error al eliminar el paciente');
        });
}

function crearFila(paciente) {
    const fila = document.createElement('tr');

    fila.innerHTML = `
        <td>${paciente.primer_nombre} ${paciente.segundo_nombre || ''}</td>
        <td>${paciente.primer_apellido} ${paciente.segundo_apellido || ''}</td>
        <td>${paciente.documento_identidad}</td>
        <td>${paciente.fecha_nacimiento}</td>
        <td>${paciente.genero}</td>
        <td>${paciente.sede}</td>
        <td>
            <button type="button" class="accion-editar" data-id="${paciente.id}">Editar</button>
            <button type="button" class="accion-eliminar" data-id="${paciente.id}">Eliminar</button>
        </td>
    `;

    fila.querySelector('.accion-editar').addEventListener('click', () => editarPaciente(paciente));
    fila.querySelector('.accion-eliminar').addEventListener('click', () => eliminarPaciente(paciente.id));

    return fila;
}

function cargarPacientes() {
    const datos = new FormData();

    datos.append('accion', 'listar');

    fetch('app/ajax/paciente_ajax.php', {
        method : 'POST',
        body   : datos,
    })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
            if (respuesta.exito === false) {
                alert(respuesta.mensaje);

                return;
            }

            cuerpoTabla.innerHTML = '';

            respuesta.pacientes.forEach((paciente) => {
                cuerpoTabla.appendChild(crearFila(paciente));
            });
        })
        .catch(() => {
            alert('Ocurrió un error al cargar los pacientes');
        });
}

document.addEventListener('DOMContentLoaded', () => {
    establecerFechaMaxima();
    formularioPaciente.addEventListener('submit', enviarFormulario);
    botonCancelar.addEventListener('click', cancelarEdicion);
    cargarPacientes();
});