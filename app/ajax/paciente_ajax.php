<?php

// La respuesta siempre será JSON; se define antes de cualquier otra salida
// para que el navegador la interprete correctamente y con acentos (utf-8).
header('Content-Type: application/json; charset=utf-8');

// Cargamos el controlador que contiene la lógica de las cuatro acciones.
require_once __DIR__ . '/../controllers/PacienteController.php';

// Se instancia el controlador una única vez en este endpoint.
$controlador = new PacienteController();

// "accion" indica qué operación solicita el JavaScript.
$accion = $_POST['accion'] ?? '';

// match traduce la acción recibida a la llamada del método correspondiente.
// La rama default cubre acciones desconocidas con un error controlado.
$respuesta = match ($accion) {
    // Cada rama delega en el controlador, que devuelve 'exito' y 'mensaje'.
    'registrar'  => $controlador->registrar($_POST),
    'listar'     => $controlador->listar($_POST),
    'actualizar' => $controlador->actualizar($_POST),
    'eliminar'   => $controlador->eliminar($_POST),

    default      => [
        'exito'   => false,
        'mensaje' => 'Acción no válida',
    ],
};

// Se serializa el arreglo resultado a JSON y se envía al frontend.
// JSON_UNESCAPED_UNICODE evita escapes ("\u00e1") y mantiene los acentos
// legibles en la respuesta.
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);