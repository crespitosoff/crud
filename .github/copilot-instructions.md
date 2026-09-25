# Instrucciones de Desarrollo - Proyecto CRUD PHP MVC

## 🛠️ Stack Tecnológico Obligatorio
- **Backend:** PHP 8.x+ Nativo (Programación Orientada a Objetos - POO).
- **Frontend:** HTML5, CSS3, JavaScript Nativo (ES6+) para peticiones asíncronas (Fetch API).
- **Base de Datos:** MariaDB/MySQL (Corriendo bajo el entorno XAMPP).
- **Librería de Alertas:** SweetAlert2 mediante CDN (Fase 2 de UI).

## 📐 Estándar de Código (PSR-12 Estricto)
Debes adherirte estrictamente a la guía de estilo **PSR-12**:
- Usa **4 espacios** para la indentación (nunca tabuladores).
- Nombres de clases en `StudlyCaps` (ej. `PacienteModel`).
- Nombres de métodos y propiedades en `camelCase` (ej. `obtenerPacientes`).
- Palabras clave de PHP (`true`, `false`, `null`, `if`) siempre en minúsculas.
- **PROHIBIDO EL CIERRE PHP:** Omite SIEMPRE la etiqueta de cierre `?>` al final de los archivos que contengan solo código PHP.
- **Alineación Vertical:** Tabula las asignaciones múltiples (`=`) y arreglos para que queden alineados verticalmente si mejora la legibilidad.
- **Trailing Comma:** Aplica SIEMPRE la coma final en arreglos multilínea y listas de argumentos.

## 🔒 Validación y Seguridad
Toda entrada de datos debe validarse en **dos capas**:
1. **Frontend (JS Nativo):** Validación previa antes del envío (campos vacíos). Alertas renderizadas con SweetAlert2.
2. **Backend (PHP POO):** Revalida todo en el Controlador de forma estricta. Sanitiza con `htmlspecialchars()` o `trim()`. 
3. **Seguridad SQL:** Usa EXCLUSIVAMENTE **Sentencias Preparadas (PDO)** (`prepare`, `bindParam`, `execute`) en todas las consultas para prevenir Inyección SQL. 

## 📁 Arquitectura del Proyecto (MVC sin Htaccess)
- **`app/config/Conexion.php`**: Clase PDO usando constantes (`const`) para las credenciales y patrón Singleton estático.
- **`app/models/`**: Lógica de base de datos directa.
- **`app/controllers/`**: Reciben, limpian datos y llaman al modelo.
- **`app/ajax/`**: Endpoints puros (`.php`) que reciben el POST, instancian el controlador y retornan `echo json_encode()`.
- **`app/assets/js/main.js`**: Lógica asíncrona con `fetch()`.
- **`index.php`**: Única vista HTML.



El entorno base ya funciona, pero tenemos graves brechas de validación, errores en el JSON por valores nulos, excepciones PDO rompiendo la vista, y faltan las operaciones finales. Iniciaremos la Fase 3: Seguridad Estricta, Control de Excepciones y Finalización del CRUD (Actualizar y Eliminar).

Requerimientos para la Fase 3:

    Reparación del Modelo (app/models/PacienteModel.php):

    En obtenerPacientes(), si segundo_nombre o segundo_apellido son null en la BD, asegúrate de que el mapeo devuelva un string vacío ("") para no romper el JSON ni el renderizado del Frontend.

    Crea los métodos actualizar(array $datos) y eliminar(int $id) usando Sentencias Preparadas PDO.

    Blindaje del Controlador (app/controllers/PacienteController.php):

    Sanitización Total: Modifica sanitizar() para que absolutamente toda cadena pase por htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8').

    Validación Estricta: En validar(), usa preg_match('/^[0-9]{10}$/', $doc) para exigir exactamente 10 números en el documento de identidad. Usa strtotime para validar que fecha_nacimiento no sea mayor a la fecha actual.

    Control de Duplicados: Modifica el bloque try-catch del método registrar(). Si PDOException lanza el código 23000 (Duplicate entry), atrapa la excepción y retorna un array: ['exito' => false, 'mensaje' => 'Este documento ya está registrado en el sistema.'].

    Implementa los métodos actualizar() y eliminar() conectando con el modelo.

    Mejoras UI/UX en index.php:

    En el input del documento, agrega el atributo: oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)" para borrar letras físicamente.

    Agrega el atributo max="<?php echo date('Y-m-d'); ?" al input de fecha para bloquear el calendario en días futuros.

    Asegúrate de que la tabla dinámica tenga una columna 'Acciones' esperando los botones de Editar y Eliminar.

    Completar AJAX y JS (app/ajax/paciente_ajax.php y main.js):

    En paciente_ajax.php, añade las ramas para recibir la acción 'actualizar' y 'eliminar'.

    En main.js, implementa SweetAlert2 (Swal.fire) en lugar de alert() para mostrar el mensaje de éxito o el error amigable del documento duplicado.

    Inyecta los botones Editar y Eliminar en las filas generadas por cargarPacientes() y asigna sus respectivas peticiones fetch.

Entrégame los archivos actualizados. NO incluyas la etiqueta de cierre php al final de los archivos backend.