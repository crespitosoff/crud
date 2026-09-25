<?php

// =========================================================
// CONTROLADOR FRONTAL (Front Controller)
// Único punto de entrada de la aplicación: el servidor siempre
// llega aquí y desde aquí se delega a la vista correspondiente.
// =========================================================

// Paso 1: cargamos el Controlador para tener disponible su clase.
require_once __DIR__ . '/app/controllers/PacienteController.php';

// Paso 2: instanciamos el controlador; toda petición pasa por este objeto,
// que orquesta la lógica de la aplicación (patrón MVC).
$controlador = new PacienteController();

// Paso 3: el método mostrarVista() incluye la vista que renderiza el HTML.
$controlador->mostrarVista();