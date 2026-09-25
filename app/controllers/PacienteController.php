<?php

require_once __DIR__ . '/../models/PacienteModel.php';

/**
 * Clase PacienteController
 *
 * Capa intermedia del MVC: recibe los datos del frontend (POST), los
 * sanitiza, los valida y delega la persistencia al PacienteModel.
 * Cada método devuelve un arreglo con 'exito' y 'mensaje' para que el
 * AJAX lo convierta en JSON.
 */
class PacienteController
{
    /**
     * Registra un nuevo paciente.
     *
     * @param array $post Datos crudos llegados por $_POST.
     * @return array Respuesta con 'exito' y 'mensaje'.
     */
    public function registrar(array $post): array
    {
        $datos      = $this->sanitizar($post);
        $validacion = $this->validar($datos);

        // Si la validación falla, se responde sin tocar la base de datos.
        if ($validacion['valido'] === false) {
            return [
                'exito'   => false,
                'mensaje' => $validacion['mensaje'],
            ];
        }

        // Los nombres opcionales vacíos se guardan como null en la BD.
        $datos['segundo_nombre']   = $datos['segundo_nombre']   !== '' ? $datos['segundo_nombre']   : null;
        $datos['segundo_apellido'] = $datos['segundo_apellido'] !== '' ? $datos['segundo_apellido'] : null;

        // sede_id es una FK numérica: se convierte a entero antes de grabar.
        $datos['sede_id'] = (int) $datos['sede_id'];

        try {
            $exito = PacienteModel::insertar($datos);

            return [
                'exito'   => $exito,
                'mensaje' => $exito ? 'Paciente registrado correctamente' : 'No se pudo registrar al paciente',
            ];
        } catch (PDOException $e) {
            // SQLSTATE 23000 = violación de restricción UNIQUE: el documento
            // de identidad ya existe, así que respondemos un mensaje amigable.
            if ($e->getCode() === '23000') {
                return [
                    'exito'   => false,
                    'mensaje' => 'Este documento ya se encuentra registrado en el sistema.',
                ];
            }

            // Cualquier otra excepción se devuelve como error genérico.
            return [
                'exito'   => false,
                'mensaje' => 'Error al registrar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Lista todos los pacientes existentes.
     *
     * @param array $post No se usa, pero se recibe para homogeneidad.
     * @return array Respuesta con 'exito', 'mensaje' y los 'pacientes'.
     */
    public function listar(array $post): array
    {
        try {
            $pacientes = PacienteModel::obtenerPacientes();

            return [
                'exito'     => true,
                'mensaje'   => count($pacientes) . ' paciente(s) encontrado(s)',
                // Los pacientes se incluyen para que el JS llene la tabla.
                'pacientes' => $pacientes,
            ];
        } catch (PDOException $e) {
            return [
                'exito'   => false,
                'mensaje' => 'Error al listar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Actualiza los datos de un paciente existente.
     *
     * @param array $post Datos crudos incluyendo el campo id.
     * @return array Respuesta con 'exito' y 'mensaje'.
     */
    public function actualizar(array $post): array
    {
        $datos      = $this->sanitizar($post);
        $validacion = $this->validar($datos);

        if ($validacion['valido'] === false) {
            return [
                'exito'   => false,
                'mensaje' => $validacion['mensaje'],
            ];
        }

        // Un UPDATE sin id es imposible: se valida antes de llamar al modelo.
        if ($datos['id'] === '') {
            return [
                'exito'   => false,
                'mensaje' => 'Identificador de paciente no válido',
            ];
        }

        $datos['segundo_nombre']   = $datos['segundo_nombre']   !== '' ? $datos['segundo_nombre']   : null;
        $datos['segundo_apellido'] = $datos['segundo_apellido'] !== '' ? $datos['segundo_apellido'] : null;
        $datos['id']               = (int) $datos['id'];
        $datos['sede_id']          = (int) $datos['sede_id'];

        try {
            $exito = PacienteModel::actualizar($datos);

            return [
                'exito'   => $exito,
                'mensaje' => $exito ? 'Paciente actualizado correctamente' : 'No se pudo actualizar el paciente',
            ];
        } catch (PDOException $e) {
            return [
                'exito'   => false,
                'mensaje' => 'Error al actualizar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Elimina un paciente existente por su id.
     *
     * @param array $post Debe incluir el campo id.
     * @return array Respuesta con 'exito' y 'mensaje'.
     */
    public function eliminar(array $post): array
    {
        $id = (int) trim($post['id'] ?? '');

        // Con un id inválido (0 o negativo) no hay nada que eliminar.
        if ($id <= 0) {
            return [
                'exito'   => false,
                'mensaje' => 'Identificador de paciente no válido',
            ];
        }

        try {
            $exito = PacienteModel::eliminar($id);

            return [
                'exito'   => $exito,
                'mensaje' => $exito ? 'Paciente eliminado correctamente' : 'No se pudo eliminar el paciente',
            ];
        } catch (PDOException $e) {
            return [
                'exito'   => false,
                'mensaje' => 'Error al eliminar: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Puente hacia la vista: incluye el HTML que ve el usuario.
     *
     * Separa la presentación del controlador (MVC puro). El controlador
     * solo "decide" qué vista mostrar; la vista se encarga del render.
     */
    public function mostrarVista()
    {
        require_once __DIR__ . '/../views/paciente_view.php';
    }

    /**
     * Sanitiza TODAS las entradas de texto del POST.
     *
     * Cada campo se limpia con la combinación estricta:
     * htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8')
     * - trim() elimina espacios en blanco iniciales y finales.
     * - htmlspecialchars convierte caracteres peligrosos (HTML/JavaScript)
     *   en entidades seguras, neutralizando XSS.
     *
     * @param array $post Datos crudos.
     * @return array Datos ya sanitizados.
     */
    private function sanitizar(array $post): array
    {
        return [
            'id'                     => $this->limpiar($post['id'] ?? ''),
            'primer_nombre'          => $this->limpiar($post['primer_nombre'] ?? ''),
            'segundo_nombre'         => $this->limpiar($post['segundo_nombre'] ?? ''),
            'primer_apellido'        => $this->limpiar($post['primer_apellido'] ?? ''),
            'segundo_apellido'       => $this->limpiar($post['segundo_apellido'] ?? ''),
            'documento_identidad'    => $this->limpiar($post['documento_identidad'] ?? ''),
            'fecha_nacimiento'       => $this->limpiar($post['fecha_nacimiento'] ?? ''),
            'genero'                 => $this->limpiar($post['genero'] ?? ''),
            'sede_id'                => $this->limpiar($post['sede_id'] ?? ''),
        ];
    }

    /**
     * Aplica la limpieza combinada a un único valor de texto.
     *
     * ENT_QUOTES también escapa las comillas simples, y 'UTF-8' garantiza
     * que los acentos no se rompan durante el escapado.
     *
     * @param string $valor Valor crudo a limpiar.
     * @return string Valor limpio.
     */
    private function limpiar(string $valor): string
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida estrictamente los datos ya sanitizados.
     *
     * @param array $datos Datos sanitizados.
     * @return array Arreglo con clave 'valido' (bool) y 'mensaje' (string).
     */
    private function validar(array $datos): array
    {
        // Ningún campo obligatorio puede llegar vacío.
        if ($datos['primer_nombre'] === '' || $datos['primer_apellido'] === '' || $datos['documento_identidad'] === ''
            || $datos['fecha_nacimiento'] === '' || $datos['genero'] === '' || $datos['sede_id'] === '') {
            return [
                'valido'  => false,
                'mensaje' => 'Todos los campos obligatorios deben estar completos',
            ];
        }

        // El documento debe ser exactamente 10 dígitos, sin letras.
        if (preg_match('/^[0-9]{10}$/', $datos['documento_identidad']) !== 1) {
            return [
                'valido'  => false,
                'mensaje' => 'El documento de identidad debe contener exactamente 10 números',
            ];
        }

        // Comparación de fechas ISO (Y-m-d): si la nacimiento es mayor que
        // la de hoy, es una fecha futura y se rechaza ("no viajeros del tiempo").
        if ($datos['fecha_nacimiento'] > date('Y-m-d')) {
            return [
                'valido'  => false,
                'mensaje' => 'La fecha de nacimiento no puede ser posterior a la fecha actual',
            ];
        }

        return [
            'valido'  => true,
            'mensaje' => '',
        ];
    }
}