# Instrucciones de Desarrollo - Proyecto CRUD PHP MVC

## 🛠️ Stack Tecnológico Obligatorio
- **Backend:** PHP 8.x+ Nativo (Programación Orientada a Objetos - POO).
- **Frontend:** HTML5, CSS3, JavaScript Nativo (ES6+) para peticiones asíncronas (Fetch API).
- **Base de Datos:** MySQL/MariaDB (Entorno XAMPP local).
- **Librería de Alertas:** SweetAlert2 mediante CDN (Fase 2 de UI).

## 📐 Estándar de Código (PSR-12 Modificado)
Debes adherirte estrictamente a la guía de estilo **PSR-12** con estas reglas irrompibles:
- Usa **4 espacios** para la indentación (nunca tabuladores).
- Nombres de clases en `StudlyCaps` (ej. `PacienteModel`).
- Nombres de métodos y propiedades en `camelCase` (ej. `obtenerPacientes`).
- Palabras clave de PHP (`true`, `false`, `null`, `if`) siempre en minúsculas.
- **PROHIBIDO EL CIERRE PHP:** Omite siempre la etiqueta de cierre `?>` al final de los archivos que contengan solo código PHP. No dejes líneas en blanco al final.
- **Alineación Vertical:** Tabula las asignaciones múltiples (`=`) y los arreglos para que queden alineados verticalmente si mejora la legibilidad.
- **Trailing Comma:** Aplica SIEMPRE la coma final en arreglos multilínea y listas de argumentos.

## 🔒 Validación y Seguridad
Toda entrada de datos debe validarse en **dos capas**:
1. **Frontend (JS Nativo):** Validación previa antes del envío (campos vacíos, formatos). 
2. **Backend (PHP POO):** Nunca confíes en el frontend. Sanitiza las entradas con `trim()`, valida tipos de datos y usa EXCLUSIVAMENTE **Sentencias Preparadas (PDO)** (`prepare`, `bindParam`, `execute`) en todas las consultas para prevenir inyección SQL.

## 📁 Arquitectura del Proyecto (MVC Conservador sin Autoload/Htaccess)
- **`app/config/Conexion.php`**: Conexión PDO con manejo de excepciones.
- **`app/models/`**: Lógica de base de datos.
- **`app/controllers/`**: Reciben datos, los limpian y llaman al modelo.
- **`app/ajax/`**: Endpoints puros (`.php`) que reciben el `POST` de JS, instancian el controlador y retornan `echo json_encode()`.
- **`app/assets/js/main.js`**: Lógica `fetch()`.
- **`index.php`**: Única vista HTML en la raíz.