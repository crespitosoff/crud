const formularioPaciente = document.querySelector('#formulario-paciente');

function validarFormulario() {
    const camposObligatorios = formularioPaciente.querySelectorAll('[required]');

    return Array.from(camposObligatorios).every((campo) => campo.value.trim() !== '');
}

function enviarFormulario(evento) {
    evento.preventDefault();

    if (validarFormulario() === false) {
        alert('Todos los campos obligatorios deben estar completos');

        return;
    }

    const datos = new FormData(formularioPaciente);

    datos.append('accion', 'registrar');

    fetch('app/ajax/paciente_ajax.php', {
        method : 'POST',
        body   : datos,
    })
        .then((respuesta) => respuesta.json())
        .then((respuesta) => {
            console.log(respuesta);
            alert(respuesta.mensaje);
        })
        .catch(() => {
            alert('Ocurrió un error al intentar registrar el paciente');
        });
}

document.addEventListener('DOMContentLoaded', () => {
    formularioPaciente.addEventListener('submit', enviarFormulario);
});