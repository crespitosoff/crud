<?php

require_once __DIR__ . '/../models/PacienteModel.php';

class PacienteController
{
    public function registrar(array $post): array
    {
        $datos      = $this->sanitizar($post);
        $validacion = $this->validar($datos);

        if ($validacion['valido'] === false) {
            return [
                'exito'   => false,
                'mensaje' => $validacion['mensaje'],
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
            if ($e->getCode() === '23000') {
                return [
                    'exito'   => false,
                    'mensaje' => 'Este documento ya se encuentra registrado en el sistema.',
                ];
            }

            return [
                'exito'   => false,
                'mensaje' => 'Error al registrar: ' . $e->getMessage(),
            ];
        }
    }

    public function listar(array $post): array
    {
        try {
            $pacientes = PacienteModel::obtenerPacientes();

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
        $datos      = $this->sanitizar($post);
        $validacion = $this->validar($datos);

        if ($validacion['valido'] === false) {
            return [
                'exito'   => false,
                'mensaje' => $validacion['mensaje'],
            ];
        }

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

    private function limpiar(string $valor): string
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    private function validar(array $datos): array
    {
        if ($datos['primer_nombre'] === '' || $datos['primer_apellido'] === '' || $datos['documento_identidad'] === ''
            || $datos['fecha_nacimiento'] === '' || $datos['genero'] === '' || $datos['sede_id'] === '') {
            return [
                'valido'  => false,
                'mensaje' => 'Todos los campos obligatorios deben estar completos',
            ];
        }

        if (preg_match('/^[0-9]{10}$/', $datos['documento_identidad']) !== 1) {
            return [
                'valido'  => false,
                'mensaje' => 'El documento de identidad debe contener exactamente 10 números',
            ];
        }

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