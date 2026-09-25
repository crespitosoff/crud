<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../controllers/PacienteController.php';

$controlador = new PacienteController();
$accion      = $_POST['accion'] ?? '';

$respuesta = match ($accion) {
    'registrar'  => $controlador->registrar($_POST),
    'listar'     => $controlador->listar($_POST),
    'actualizar' => $controlador->actualizar($_POST),
    'eliminar'   => $controlador->eliminar($_POST),
    default      => [
        'exito'   => false,
        'mensaje' => 'Acción no válida',
    ],
};

echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
