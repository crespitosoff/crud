<?php

require_once __DIR__ . '/../models/PacienteModel.php';

class PacienteController
{
    public function registrar(array $post): array
    {
        $datos = $this->sanitizar($post);

        if ($this->validar($datos) === false) {
            return [
                'exito'   => false,
                'mensaje' => 'Todos los campos obligatorios deben estar completos',
            ];
        }

        $datos['segundo_nombre']   = $datos['segundo_nombre']   !== '' ? $datos['segundo_nombre']   : null;
        $datos['segundo_apellido'] = $datos['segundo_apellido'] !== '' ? $datos['segundo_apellido'] : null;
        $datos['sede_id']          = (int) $datos['sede_id'];

        try {
            $exito = PacienteModel::insertar($datos);

            return [
                'exito'   => $exito,
                'mensaje' => $exito ? 'Paciente registrado correctamente' : 'No se pudo registrar al paciente',
            ];
        } catch (PDOException $e) {
            return [
                'exito'   => false,
                'mensaje' => 'Error al registrar: ' . $e->getMessage(),
            ];
        }
    }

    public function listar(array $post): array
    {
        try {
            $pacientes = PacienteModel::obtenerTodos();

            return [
                'exito'     => true,
                'mensaje'   => count($pacientes) . ' paciente(s) encontrado(s)',
                'pacientes' => $pacientes,
            ];
        } catch (PDOException $e) {
            return [
                'exito'   => false,
                'mensaje' => 'Error al listar: ' . $e->getMessage(),
            ];
        }
    }

    public function actualizar(array $post): array
    {
        $datos = $this->sanitizar($post);

        if ($this->validar($datos) === false || $datos['id'] === '') {
            return [
                'exito'   => false,
                'mensaje' => 'Todos los campos obligatorios deben estar completos',
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

    public function eliminar(array $post): array
    {
        $id = (int) trim($post['id'] ?? '');

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

    private function sanitizar(array $post): array
    {
        return [
            'id'                     => trim($post['id'] ?? ''),
            'primer_nombre'          => trim($post['primer_nombre'] ?? ''),
            'segundo_nombre'         => trim($post['segundo_nombre'] ?? ''),
            'primer_apellido'        => trim($post['primer_apellido'] ?? ''),
            'segundo_apellido'       => trim($post['segundo_apellido'] ?? ''),
            'documento_identidad'    => trim($post['documento_identidad'] ?? ''),
            'fecha_nacimiento'       => trim($post['fecha_nacimiento'] ?? ''),
            'genero'                 => trim($post['genero'] ?? ''),
            'sede_id'                => trim($post['sede_id'] ?? ''),
        ];
    }

    private function validar(array $datos): bool
    {
        return $datos['primer_nombre']       !== ''
            && $datos['primer_apellido']     !== ''
            && $datos['documento_identidad'] !== ''
            && $datos['fecha_nacimiento']    !== ''
            && $datos['genero']              !== ''
            && $datos['sede_id']             !== '';
    }
}
