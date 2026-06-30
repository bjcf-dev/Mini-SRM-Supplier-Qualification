# Testing de la API con Bruno

## ¿Qué es Bruno?

[Bruno](https://www.usebruno.com/) es un cliente API de código abierto para probar endpoints REST y GraphQL. Es una alternativa a Postman o Insomnia. Las colecciones de Bruno se almacenan como **archivos de texto plano** (`.bru`), lo que permite versionarlas con Git.

## Instalación

1. Descargar Bruno desde https://www.usebruno.com/downloads
2. Seleccionar la versión correspondiente al sistema operativo (Windows, macOS o Linux).
3. Ejecutar el instalador y seguir los pasos.
4. Abrir Bruno.

## Abrir la colección del proyecto

La colección se encuentra en la carpeta `docs/bruno/` del proyecto. Existen dos formas de abrirla:

### Opción A — Desde el menú

1. En Bruno, ir a **File → Open Collection** (Ctrl+O en Windows/Linux, Cmd+O en macOS).
2. Navegar hasta la carpeta `docs/bruno/` del proyecto.
3. Seleccionar el archivo `bruno.json` y hacer clic en "Open".

### Opción B — Arrastrar la carpeta

1. Arrastrar la carpeta `docs/bruno/` directamente a la ventana de Bruno.

---

## Estructura de la colección

```
docs/bruno/
├── bruno.json                       ← configuración de la colección
├── 1 - Crear Calificación/          ← tests de creación (POST)
│   ├── Crear - Aprobado (score 85).bru
│   ├── Crear - Rechazado (score 30).bru
│   ├── Crear - Límite 59 (rechazado).bru
│   └── Crear - Límite 60 (aprobado).bru
├── 2 - Errores (400)/               ← tests de validación (POST)
│   ├── Score inválido - 150.bru
│   ├── Score inválido - negativo.bru
│   ├── UUID inválido - supplierId.bru
│   ├── UUID inválido - auditorId.bru
│   ├── JSON mal formado.bru
│   └── Cuerpo vacío.bru
└── 3 - Listar Calificaciones/       ← tests de listado (GET)
    └── Listar todas.bru
```

---

## Cómo ejecutar una prueba

1. En el panel izquierdo, hacer clic sobre el nombre de la prueba.
2. Se abrirá el editor con la petición preconfigurada.
3. Hacer clic en el botón **Send** (Ctrl+Enter).
4. Bruno mostrará la respuesta en el panel derecho:
   - Código de estado HTTP
   - Cabeceras de respuesta
   - Cuerpo de la respuesta (formato JSON)

---

## Casos de prueba paso a paso

### 1. Crear calificación — Aprobada (score 85)

| Atributo | Valor |
|---|---|
| **Método** | `POST` |
| **URL** | `http://localhost:8000/api/qualifications` |
| **Body** | `{"supplierId":"550e8400-...","auditorId":"550e8400-...","score":85,"comments":"..."}` |

**Resultado esperado:**
- Status: **201 Created**
- Body: `{ "id": "UUID v4" }`

**Validación:** Un score ≥ 60 se guarda como APPROVED.

---

### 2. Crear calificación — Rechazada (score 30)

**Resultado esperado:**
- Status: **201 Created**
- Body: `{ "id": "UUID v4" }`

**Validación:** Un score < 60 se guarda como REJECTED (se puede verificar después con GET).

---

### 3. Límite 59 (rechazado)

**Resultado esperado:**
- Status: **201 Created**
- Body: `{ "id": "..." }`

**Validación:** El score 59 está por debajo del umbral (60) → se guarda como REJECTED. El sistema no redondea.

---

### 4. Límite 60 (aprobado)

**Resultado esperado:**
- Status: **201 Created**
- Body: `{ "id": "..." }`

**Validación:** El score 60 está en el umbral → se guarda como APPROVED.

---

### 5. Score inválido — 150

**Resultado esperado:**
- Status: **400 Bad Request**
- Body: `{ "error": "QualificationScore must be between 0 and 100." }`

**Validación:** Scores > 100 son rechazados por la regla de dominio.

---

### 6. Score inválido — negativo (-5)

**Resultado esperado:**
- Status: **400 Bad Request**
- Body: `{ "error": "QualificationScore must be between 0 and 100." }`

**Validación:** Scores negativos son rechazados.

---

### 7. UUID inválido — supplierId

**Resultado esperado:**
- Status: **400 Bad Request**
- Body: `{ "error": "Invalid UUID v4 format for SupplierId." }`

**Validación:** El campo supplierId debe ser un UUID v4 válido.

---

### 8. UUID inválido — auditorId

**Resultado esperado:**
- Status: **400 Bad Request**
- Body: `{ "error": "Invalid UUID v4 format for AuditorId." }`

**Validación:** El campo auditorId debe ser un UUID v4 válido.

---

### 9. JSON mal formado

**Resultado esperado:**
- Status: **400 Bad Request**
- Body: `{ "error": "Invalid JSON body." }`

**Validación:** Si el body no es JSON válido, la API responde con error.

---

### 10. Cuerpo vacío (`{}`)

**Resultado esperado:**
- Status: **400 Bad Request**
- Body: `{ "error": "Invalid UUID v4 format for SupplierId." }`

**Validación:** Los campos requeridos vacíos disparan la validación de UUID.

---

### 11. Listar todas las calificaciones

| Atributo | Valor |
|---|---|
| **Método** | `GET` |
| **URL** | `http://localhost:8000/api/qualifications` |

**Resultado esperado:**
- Status: **200 OK**
- Body: `[ { "id": "...", "score": 85, "status": "APPROVED", ... }, ... ]`
- Array vacío `[]` si no hay datos registrados.

**Validación:** El endpoint devuelve todas las calificaciones creadas con todos sus campos.

---

## Funcionalidades adicionales de Bruno

- **Ejecución masiva (Run All):** En el panel izquierdo, hacer clic derecho sobre el nombre de la colección → **Run All Requests**. Bruno ejecuta todas las pruebas en secuencia y muestra un resumen.
- **Variables de entorno:** Se pueden definir variables (ej. `{{base_url}}`) en el ícono de engranaje → **Collections → Variables**. Esto permite cambiar el puerto o host sin modificar cada prueba individualmente.
- **Assertions automáticas:** Bruno permite agregar validaciones a las pruebas mediante una sección `assert` en el archivo `.bru`:

```bru
assert {
  res.status: eq 201
  res.body: json
}
```

- **Historial:** Todas las respuestas quedan registradas en el historial y pueden consultarse posteriormente.

---

## Solución de problemas

| Problema | Causa probable | Solución |
|---|---|---|
| `Connection refused` | Docker no está corriendo | Ejecutar `docker compose up -d` en la raíz del proyecto |
| `404 Not Found` | Ruta incorrecta | Verificar que la URL sea exactamente `http://localhost:8000/api/qualifications` |
| `500 Internal Server Error` | Error en la aplicación | Revisar los logs con `docker compose logs app` |
| La colección no aparece en Bruno | Bruno no encontró `bruno.json` | Asegurar que se abrió la carpeta `docs/bruno/` y no una subcarpeta |
