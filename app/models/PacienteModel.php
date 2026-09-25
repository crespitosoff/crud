<?php

require_once __DIR__ . '/../config/Conexion.php';

class PacienteModel
{
    public static function insertar(array $datos): bool
    {
        $sql = 'INSERT INTO pacientes (
                    primer_nombre,
                    segundo_nombre,
                    primer_apellido,
                    segundo_apellido,
                    documento_identidad,
                    fecha_nacimiento,
                    genero,
                    sede_id
                ) VALUES (
                    :primer_nombre,
                    :segundo_nombre,
                    :primer_apellido,
                    :segundo_apellido,
                    :documento_identidad,
                    :fecha_nacimiento,
                    :genero,
                    :sede_id
                )';

        $stmt = Conexion::conectar()->prepare($sql);

        $stmt->bindParam(':primer_nombre',       $datos['primer_nombre']);
        $stmt->bindParam(':segundo_nombre',      $datos['segundo_nombre']);
        $stmt->bindParam(':primer_apellido',     $datos['primer_apellido']);
        $stmt->bindParam(':segundo_apellido',    $datos['segundo_apellido']);
        $stmt->bindParam(':documento_identidad', $datos['documento_identidad']);
        $stmt->bindParam(':fecha_nacimiento',    $datos['fecha_nacimiento']);
        $stmt->bindParam(':genero',              $datos['genero']);
        $stmt->bindParam(':sede_id',             $datos['sede_id']);

        return $stmt->execute();
    }

    public static function obtenerPacientes(): array
    {
        $sql = 'SELECT
                    p.id,
                    p.primer_nombre,
                    p.segundo_nombre,
                    p.primer_apellido,
                    p.segundo_apellido,
                    p.documento_identidad,
                    p.fecha_nacimiento,
                    p.genero,
                    s.nombre AS sede
                FROM pacientes AS p
                INNER JOIN sedes AS s ON s.id = p.sede_id
                ORDER BY p.id DESC';

        $stmt = Conexion::conectar()->prepare($sql);

        $stmt->execute();

        return array_map(function (array $paciente): array {
            $paciente['segundo_nombre']   = $paciente['segundo_nombre'] ?? '';
            $paciente['segundo_apellido'] = $paciente['segundo_apellido'] ?? '';

            return $paciente;
        }, $stmt->fetchAll());
    }

    public static function actualizar(array $datos): bool
    {
        $sql = 'UPDATE pacientes SET
                    primer_nombre       = :primer_nombre,
                    segundo_nombre      = :segundo_nombre,
                    primer_apellido     = :primer_apellido,
                    segundo_apellido    = :segundo_apellido,
                    documento_identidad = :documento_identidad,
                    fecha_nacimiento    = :fecha_nacimiento,
                    genero              = :genero,
                    sede_id             = :sede_id
                WHERE id = :id';

        $stmt = Conexion::conectar()->prepare($sql);

        $stmt->bindParam(':primer_nombre',       $datos['primer_nombre']);
        $stmt->bindParam(':segundo_nombre',      $datos['segundo_nombre']);
        $stmt->bindParam(':primer_apellido',     $datos['primer_apellido']);
        $stmt->bindParam(':segundo_apellido',    $datos['segundo_apellido']);
        $stmt->bindParam(':documento_identidad', $datos['documento_identidad']);
        $stmt->bindParam(':fecha_nacimiento',    $datos['fecha_nacimiento']);
        $stmt->bindParam(':genero',              $datos['genero']);
        $stmt->bindParam(':sede_id',             $datos['sede_id']);
        $stmt->bindParam(':id',                  $datos['id'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function eliminar(int $id): bool
    {
        $sql = 'DELETE FROM pacientes WHERE id = :id';

        $stmt = Conexion::conectar()->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}