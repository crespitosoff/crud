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

ok