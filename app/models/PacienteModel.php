<?php

require_once __DIR__ . '/../config/Conexion.php';

/**
 * Clase PacienteModel
 *
 * Capa de datos del MVC: única responsable de interactuar con la tabla
 * `pacientes`. Todos los métodos son estáticos y usan SIEMPRE sentencias
 * preparadas (PDO) para prevenir inyecciones SQL.
 */
class PacienteModel
{
    /**
     * Inserta un nuevo paciente en la base de datos.
     *
     * @param array $datos Arreglo asociativo con los datos ya sanitizados.
     * @return bool true si la inserción fue exitosa.
     */
    public static function insertar(array $datos): bool
    {
        // Consulta parametrizada: los marcadores (:nombre) se sustituyen
        // luego con bindParam; los valores jamás se concatenan en el SQL.
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

        // Se prepara la consulta una sola vez a nivel del driver.
        $stmt = Conexion::conectar()->prepare($sql);

        // Cada marcador se vincula a su valor por referencia.
        // bindParam mantiene los datos fuera del texto del SQL (seguridad).
        $stmt->bindParam(':primer_nombre',       $datos['primer_nombre']);
        $stmt->bindParam(':segundo_nombre',      $datos['segundo_nombre']);
        $stmt->bindParam(':primer_apellido',     $datos['primer_apellido']);
        $stmt->bindParam(':segundo_apellido',    $datos['segundo_apellido']);
        $stmt->bindParam(':documento_identidad', $datos['documento_identidad']);
        $stmt->bindParam(':fecha_nacimiento',    $datos['fecha_nacimiento']);
        $stmt->bindParam(':genero',              $datos['genero']);
        $stmt->bindParam(':sede_id',             $datos['sede_id']);

        // execute() devuelve true/false según el resultado de la operación.
        return $stmt->execute();
    }

    /**
     * Obtiene todos los pacientes junto con el nombre de su sede.
     *
     * Además, convierte los campos opcionales NULL en "" para que el JSON
     * nunca devuelva null y el Frontend no se rompa al renderizar la fila.
     *
     * @return array Lista de pacientes como arreglos asociativos.
     */
    public static function obtenerPacientes(): array
    {
        // JOIN con `sedes` para mostrar el nombre de la sede (relación por
        // sede_id). ORDER BY id DESC muestra primero los más recientes.
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

        // array_map recorre cada fila y, con el operador ??, reemplaza los
        // valores null por cadena vacía "" antes de devolver el arreglo.
        return array_map(function (array $paciente): array {
            $paciente['segundo_nombre']   = $paciente['segundo_nombre'] ?? '';
            $paciente['segundo_apellido'] = $paciente['segundo_apellido'] ?? '';

            return $paciente;
        }, $stmt->fetchAll());
    }

    /**
     * Actualiza los datos de un paciente existente, identificado por su id.
     *
     * @param array $datos Incluye el id y todos los campos a actualizar.
     * @return bool true si la actualización se ejecutó correctamente.
     */
    public static function actualizar(array $datos): bool
    {
        // UPDATE parametrizado con WHERE por id: solo se toca ese registro.
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

        // PDO::PARAM_INT fuerza a que el id se trate como entero.
        $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Elimina un paciente de la base de datos por su id.
     *
     * @param int $id Identificador del paciente a eliminar.
     * @return bool true si el DELETE se ejecutó correctamente.
     */
    public static function eliminar(int $id): bool
    {
        $sql = 'DELETE FROM pacientes WHERE id = :id';

        $stmt = Conexion::conectar()->prepare($sql);

        // Se vincula el id como entero para evitar interpretaciones raras.
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}