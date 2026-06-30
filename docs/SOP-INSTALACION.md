# SOP — Instalación y puesta en marcha del proyecto SRM

**Versión:** 1.0  
**Última actualización:** 2026-06-30  
**Perfil de audiencia:** Personal sin conocimientos técnicos avanzados  
**Tiempo estimado:** 30–60 minutos  

---

## Índice

1. [Descripción del proyecto](#1-descripción-del-proyecto)
2. [Requisitos previos](#2-requisitos-previos)
3. [Opción A — Instalación con Docker (recomendada)](#3-opción-a--instalación-con-docker-recomendada)
4. [Opción B — Instalación manual](#4-opción-b--instalación-manual)
5. [Verificación del funcionamiento](#5-verificación-del-funcionamiento)
6. [Pruebas de la API con Bruno](#6-pruebas-de-la-api-con-bruno)
7. [Estructura del proyecto](#7-estructura-del-proyecto)
8. [Solución de problemas](#8-solución-de-problemas)

---

## 1. Descripción del proyecto

SRM (Supplier Relationship Management) es un sistema para la homologación de proveedores. Sus funciones principales son:

- Calificar proveedores mediante una puntuación numérica (0 a 100).
- Asignación automática del estado: **APROBADO** (≥ 60) o **RECHAZADO** (< 60).
- Registro inmutable de cada calificación con fecha de creación y vencimiento (12 meses).
- Consulta y eliminación de calificaciones desde una interfaz web o mediante API REST.

**Stack tecnológico:** PHP 8.4, Symfony 8.1, PostgreSQL 16, Docker.

---

## 2. Requisitos previos

| Requisito | Opción A: Docker | Opción B: Manual |
|---|---|---|
| Sistema operativo | Windows, macOS o Linux | Windows, macOS o Linux |
| RAM recomendada | 4 GB | 4 GB |
| Espacio en disco | 5 GB | 5 GB |
| Software requerido | **Docker Desktop** (o Docker Engine en Linux) | PHP 8.4, Composer, PostgreSQL 16, Symfony CLI (opcional) |
| Dificultad | Baja | Media |

> **Nota:** Si no se tiene experiencia técnica, se recomienda la Opción A (Docker). Requiere un único programa y el resto se configura automáticamente.

---

## 3. Opción A — Instalación con Docker (recomendada)

Docker permite ejecutar la aplicación en contenedores aislados sin instalar PHP ni PostgreSQL directamente en el sistema operativo.

### 3.1. Instalación de Docker

#### Windows
1. Acceder a https://docs.docker.com/desktop/setup/install/windows-install/
2. Descargar **Docker Desktop for Windows**.
3. Ejecutar el instalador con las opciones por defecto.
4. Reiniciar el equipo cuando se solicite.
5. Abrir Docker Desktop desde el menú Inicio y esperar hasta que aparezca el indicador "Engine running".

#### macOS
1. Acceder a https://docs.docker.com/desktop/setup/install/mac-install/
2. Descargar **Docker Desktop for Mac** (seleccionar la versión correcta según el procesador: Apple Silicon o Intel).
3. Arrastrar Docker a la carpeta de Aplicaciones.
4. Abrir Docker y aceptar los permisos solicitados.
5. Esperar hasta que aparezca el mensaje "Engine running".

#### Linux (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install docker.io docker-compose-plugin -y
sudo usermod -aG docker $USER
# Cerrar sesión y volver a entrar, o reiniciar
```

#### Verificación
```bash
docker --version
# Salida esperada: Docker version 27.x.x
```

### 3.2. Descarga del proyecto

Abrir una terminal:
- **Windows:** PowerShell (menú Inicio → "PowerShell")
- **macOS:** Terminal (Cmd+Espacio → "Terminal")
- **Linux:** Terminal (Ctrl+Alt+T)

```bash
# Ubicarse en la carpeta donde se desea guardar el proyecto
cd ~/Documentos

# Clonar el repositorio
git clone <URL_DEL_REPOSITORIO> srm-project

# Ingresar a la carpeta del proyecto
cd srm-project
```

> Si no se dispone de Git, se puede descargar el archivo ZIP desde GitHub y extraerlo manualmente.

### 3.3. Creación del archivo de configuración `.env`

El proyecto necesita un archivo `.env` con las credenciales de la base de datos. Crear un archivo nuevo llamado `.env` en la raíz del proyecto con el siguiente contenido:

```bash
# En la terminal, ejecutar:
cat > .env << 'EOF'
DATABASE_URL="postgresql://symfony_user:symfony_password@database:5432/srm_qualification?serverVersion=16&charset=utf8"
APP_SECRET=choose-a-random-secret-string-here
APP_ENV=dev
APP_DEBUG=1
EOF
```

> **Importante:** Este archivo contiene credenciales de acceso a la base de datos. No debe compartirse ni subirse a repositorios públicos.

El repositorio incluye una plantilla `.gitignore.example` con la lista de archivos y carpetas que deben excluirse del control de versiones. Antes de realizar el primer commit, copiar dicha plantilla como `.gitignore`:

```bash
cp .gitignore.example .gitignore
```

Esto evita la inclusión accidental de archivos sensibles como `.env`, así como de carpetas generadas automáticamente como `vendor/`, `var/`, entre otras.

### 3.4. Inicio de los contenedores

```bash
docker compose up -d
```

Este comando descarga las imágenes necesarias (PHP 8.4, PostgreSQL 16), crea los contenedores, instala las dependencias de PHP y configura la base de datos.

La primera ejecución puede tardar entre 3 y 10 minutos. Las ejecuciones posteriores serán inmediatas.

Salida esperada:
```
Container srm_postgres Started
Container srm_app Started
```

### 3.5. Migración de la base de datos

```bash
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

Salida esperada:
```
[OK] Successfully migrated to version: DoctrineMigrations\Version20260630074747
```

### 3.6. Verificación inicial

Abrir el navegador web y acceder a las siguientes direcciones:

| URL | Contenido esperado |
|---|---|
| http://localhost:8000 | Página principal del proyecto |
| http://localhost:8000/app | Panel de prueba de calificaciones |
| http://localhost:8000/api/qualifications | Listado JSON de calificaciones (puede estar vacío) |

---

## 4. Opción B — Instalación manual

Utilizar esta opción únicamente si Docker no está disponible en el equipo.

### 4.1. Instalación de PHP 8.4

#### Windows
1. Acceder a https://windows.php.net/download/
2. Seleccionar la última versión de **PHP 8.4** (Thread Safe, VS16 x64).
3. Descargar el archivo ZIP.
4. Extraer el contenido en `C:\php`.
5. Agregar `C:\php` a las variables de entorno del sistema:
   - Abrir "Variables de entorno" desde el menú Inicio.
   - En "Variables del sistema", seleccionar "Path" y hacer clic en "Editar".
   - Agregar `C:\php` y aceptar.
6. Abrir PowerShell y ejecutar:
   ```powershell
   php --version
   ```
   Salida esperada: `PHP 8.4.x`

#### macOS
```bash
brew install php@8.4
```
Si no se dispone de Homebrew, instalarlo desde https://brew.sh

#### Linux (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install php8.4 php8.4-cli php8.4-pgsql php8.4-xml php8.4-mbstring php8.4-intl -y
```

### 4.2. Instalación de Composer

1. Acceder a https://getcomposer.org/download/
2. Descargar e instalar la última versión según el sistema operativo.

Alternativamente, desde la terminal:
```bash
# Descargar el instalador
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Windows: mover a la carpeta de PHP
move composer.phar C:\php\composer.bat

# macOS/Linux: mover a la ruta global
sudo mv composer.phar /usr/local/bin/composer
```

Verificación:
```bash
composer --version
# Salida esperada: Composer version 2.x
```

### 4.3. Instalación de PostgreSQL

#### Windows
1. Acceder a https://www.enterprisedb.com/downloads/postgres-postgresql-downloads
2. Descargar la versión 16 para Windows.
3. Ejecutar el instalador.
4. Cuando se solicite la contraseña del superusuario, utilizar: `symfony_password`
5. Mantener las opciones por defecto en el resto de pasos.

#### macOS
```bash
brew install postgresql@16
brew services start postgresql@16
```

#### Linux
```bash
sudo apt install postgresql-16 -y
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

#### Configuración de la base de datos
```bash
# Conectarse a PostgreSQL
sudo -u postgres psql

# Ejecutar dentro de psql:
CREATE USER symfony_user WITH PASSWORD 'symfony_password';
CREATE DATABASE srm_qualification OWNER symfony_user;
GRANT ALL PRIVILEGES ON DATABASE srm_qualification TO symfony_user;
\q
```

### 4.4. Configuración del proyecto

```bash
# Clonar el proyecto
git clone <URL_DEL_REPOSITORIO> srm-project
cd srm-project
```

Crear el archivo `.env` con el siguiente contenido:
```bash
cat > .env << 'EOF'
DATABASE_URL="postgresql://symfony_user:symfony_password@localhost:5432/srm_qualification?serverVersion=16&charset=utf8"
APP_SECRET=choose-a-random-secret-string-here
APP_ENV=dev
APP_DEBUG=1
EOF
```

> **Nota:** La diferencia con la versión Docker es que el host de la base de datos es `localhost` en lugar de `database`.

Instalar las dependencias:
```bash
composer install
```

Ejecutar las migraciones:
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### 4.5. Inicio del servidor

```bash
php -S localhost:8000 -t public
```

Salida esperada:
```
Listening on http://localhost:8000
```

Mantener esta terminal abierta. Para ejecutar otros comandos, abrir una nueva terminal.

---

## 5. Verificación del funcionamiento

### 5.1. Interfaz web
- http://localhost:8000 — Página de inicio con información del proyecto.
- http://localhost:8000/app — Formulario para crear y gestionar calificaciones.
- http://localhost:8000/contact — Formulario de contacto.

### 5.2. API REST
```bash
# Crear una calificación
curl -X POST http://localhost:8000/api/qualifications \
  -H "Content-Type: application/json" \
  -d '{"supplierId":"550e8400-e29b-41d4-a716-446655440001","auditorId":"550e8400-e29b-41d4-a716-446655440002","score":85,"comments":"Prueba"}'

# Listar calificaciones
curl http://localhost:8000/api/qualifications

# Eliminar una calificación (reemplazar {id} con el UUID real)
curl -X DELETE http://localhost:8000/api/qualifications/{id}

# Eliminar todas las calificaciones
curl -X DELETE http://localhost:8000/api/qualifications
```

### 5.3. Consulta directa a la base de datos

#### Docker
```bash
docker compose exec database psql -U symfony_user -d srm_qualification -c "SELECT id, score, status, created_at, expires_at FROM qualification;"
```

#### Instalación manual
```bash
psql -U symfony_user -d srm_qualification -c "SELECT id, score, status, created_at, expires_at FROM qualification;"
```

#### Herramientas gráficas

**pgAdmin** (incluido con el instalador de Windows):
1. Abrir pgAdmin.
2. Registrar un servidor: botón derecho sobre "Servers" → "Register" → "Server".
3. Nombre: "SRM Local".
4. Pestaña "Connection": Host=localhost, Port=5432, Username=symfony_user, Password=symfony_password.
5. Explorar: Databases → srm_qualification → Schemas → public → Tables → qualification.

**DBeaver** (gratuito, multiplataforma): https://dbeaver.io/
1. Descargar e instalar.
2. Crear conexión PostgreSQL: Host=localhost, Database=srm_qualification, Username=symfony_user, Password=symfony_password.
3. Explorar la tabla `qualification` y sus datos.

### 5.4. Ejecución de tests automatizados

```bash
# Con Docker
docker compose exec -e APP_ENV=test app php vendor/bin/phpunit

# Con instalación manual
php vendor/bin/phpunit
```

Salida esperada:
```
OK (43 tests, 62 assertions)
```

---

## 6. Pruebas de la API con Bruno

### 6.1. ¿Qué es Bruno?

Bruno es una herramienta gráfica para probar APIs REST. Permite enviar peticiones HTTP y visualizar las respuestas. Es gratuita y de código abierto, alternativa a Postman.

Los archivos de configuración de las pruebas se encuentran en la carpeta `docs/bruno/` del proyecto.

### 6.2. Instalación

1. Acceder a https://www.usebruno.com/downloads
2. Seleccionar el sistema operativo correspondiente.
3. Descargar e instalar.

### 6.3. Apertura de la colección

1. Abrir Bruno.
2. **File → Open Collection** (Ctrl+O / Cmd+O).
3. Navegar hasta la carpeta del proyecto y seleccionar la carpeta `docs/bruno/`.
4. Seleccionar el archivo `bruno.json` y hacer clic en "Open".

El panel izquierdo mostrará las pruebas organizadas en tres grupos:

```
1 - Crear Calificación/       Pruebas de creación
   Crear - Aprobado (score 85)
   Crear - Rechazado (score 30)
   Crear - Límite 59 (rechazado)
   Crear - Límite 60 (aprobado)

2 - Errores (400)/            Pruebas de validación
   Score inválido - 150
   Score inválido - negativo
   UUID inválido - supplierId
   UUID inválido - auditorId
   JSON mal formado
   Cuerpo vacío

3 - Listar Calificaciones/    Prueba de listado
   Listar todas
```

### 6.4. Ejecución de pruebas

1. Seleccionar una prueba en el panel izquierdo.
2. Hacer clic en el botón **Send** (Ctrl+Enter).
3. El panel derecho muestra la respuesta: código de estado HTTP y contenido JSON.

### 6.5. Casos de prueba

| # | Prueba | Método | Código esperado | Descripción |
|---|---|---|---|---|
| 1 | Aprobado (score 85) | POST | 201 Created | Score ≥ 60 → se crea como APPROVED |
| 2 | Rechazado (score 30) | POST | 201 Created | Score < 60 → se crea como REJECTED |
| 3 | Límite 59 | POST | 201 Created | Score 59 → REJECTED (límite inferior) |
| 4 | Límite 60 | POST | 201 Created | Score 60 → APPROVED (límite superior) |
| 5 | Score inválido 150 | POST | 400 Bad Request | Score > 100 → error de validación |
| 6 | Score negativo | POST | 400 Bad Request | Score < 0 → error de validación |
| 7 | UUID inválido (supplierId) | POST | 400 Bad Request | Formato UUID incorrecto |
| 8 | UUID inválido (auditorId) | POST | 400 Bad Request | Formato UUID incorrecto |
| 9 | JSON mal formado | POST | 400 Bad Request | Body no es JSON válido |
| 10 | Cuerpo vacío { } | POST | 400 Bad Request | Campos requeridos ausentes |
| 11 | Listar todas | GET | 200 OK | Array de calificaciones (posiblemente vacío) |

### 6.6. Ejecución masiva (Run All)

1. En el panel izquierdo, hacer clic derecho sobre "SRM Qualification API".
2. Seleccionar **"Run All Requests"**.
3. Bruno ejecutará todas las pruebas secuencialmente y mostrará un resumen con los resultados.

---

## 7. Estructura del proyecto

### Archivos raíz

| Archivo/Carpeta | Propósito | Requerido |
|---|---|---|
| `composer.json` | Declaración de dependencias PHP del proyecto. | Sí |
| `composer.lock` | Versiones exactas de las dependencias. | Sí |
| `symfony.lock` | Registro de recetas de Symfony aplicadas. | Sí |
| `Dockerfile` | Definición del contenedor PHP (versión, extensiones). | Sí (Docker) |
| `compose.yaml` | Definición de servicios: `app` (PHP) y `database` (PostgreSQL). | Sí (Docker) |
| `phpunit.dist.xml` | Configuración del framework de pruebas PHPUnit. | Sí |
| `.gitignore` | Lista de archivos excluidos del control de versiones. | Sí |
| `spec.md` | Especificación técnica del proyecto. | No (documentación interna) |
| `README.md` | Documentación general del proyecto. | Sí |

### Carpeta `public/`

| Archivo | Función |
|---|---|
| `public/index.php` | Controlador frontal de Symfony. Punto de entrada de todas las peticiones HTTP. |

### Carpeta `src/` — Código fuente

| Archivo | Función |
|---|---|
| `src/Kernel.php` | Núcleo de Symfony. Configura el entorno de ejecución. |
| `src/Controller/LandingController.php` | Controlador de la página de inicio (/). |
| `src/Controller/AppController.php` | Controlador del panel de prueba (/app). |
| `src/Controller/ContactController.php` | Controlador de la página de contacto (/contact). |
| `src/Qualification/Domain/Model/` | Modelo de dominio: entidad Qualification y Value Objects. |
| `src/Qualification/Domain/Repository/` | Puerto de repositorio (interfaz). |
| `src/Qualification/Application/Create/` | Caso de uso: creación de calificaciones (CQRS). |
| `src/Qualification/Infrastructure/Persistence/Doctrine/` | Implementación de persistencia con Doctrine ORM. |
| `src/Qualification/Infrastructure/Ui/Http/Controller/` | Controladores de la API REST. |

### Carpeta `templates/` — Plantillas Twig

| Archivo | Contenido |
|---|---|
| `templates/base.html.twig` | Plantilla base con navegación y selector de modo oscuro. |
| `templates/landing.html.twig` | Página de inicio con descripción del proyecto. |
| `templates/app.html.twig` | Panel interactivo de prueba. |
| `templates/contact.html.twig` | Formulario de contacto. |

### Carpeta `config/` — Configuración de Symfony

| Archivo | Configura |
|---|---|
| `config/bundles.php` | Módulos de Symfony activos. |
| `config/services.yaml` | Registro y cableado de servicios. |
| `config/routes.yaml` | Enrutamiento de URLs. |
| `config/packages/doctrine.yaml` | Conexión a base de datos y mapeo ORM. |
| `config/packages/doctrine_migrations.yaml` | Configuración de migraciones. |
| `config/packages/framework.yaml` | Configuración general del framework. |
| `config/packages/twig.yaml` | Configuración del motor de plantillas Twig. |

### Carpeta `migrations/`

Archivos generados por Doctrine que definen la estructura de la base de datos (tablas, columnas, índices).

### Carpeta `tests/`

| Archivo | Contenido |
|---|---|
| `tests/Unit/Qualification/Domain/Model/` | Tests unitarios del modelo de dominio (4 archivos, 36 tests). |
| `tests/Functional/QualificationCreationTest.php` | Tests funcionales de la API REST (7 tests). |

### Carpeta `docs/`

| Archivo | Contenido |
|---|---|
| `docs/bruno/` | Colección de 11 pruebas para Bruno. |
| `docs/testing-con-bruno.md` | Guía detallada de uso de Bruno. |
| `docs/SOP-INSTALACION.md` | Este documento. |

### Carpetas generadas automáticamente

| Carpeta | Descripción |
|---|---|
| `var/` | Caché, logs y archivos temporales. No se modifica manualmente. |
| `vendor/` | Dependencias externas (Symfony, Doctrine, etc.). Se instala con `composer install`. |

---

## 8. Solución de problemas

| Problema | Causa probable | Solución |
|---|---|---|
| `docker compose up` falla | Docker no está instalado o no está en ejecución | Verificar paso 3.1 |
| El navegador muestra error 500 | Error interno del servidor | Ejecutar `docker compose logs app` para ver el error |
| `Connection refused` al conectar a la API | El servidor no está corriendo | Ejecutar `docker compose up -d` |
| `phpunit` no encuentra la BD de tests | La BD de test no existe | `docker compose exec -e APP_ENV=test app php bin/console doctrine:database:create` |
| No se visualizan datos en la interfaz | No se han creado calificaciones | Usar el formulario en /app para crear una |
| `composer` no se reconoce como comando | Composer no está instalado o no está en el PATH | Revisar paso 4.2 |
| `php` no se reconoce como comando | PHP no está instalado o no está en el PATH | Revisar paso 4.1 |
| No se puede conectar a PostgreSQL | El servicio no está iniciado | Verificar con `systemctl status postgresql` (Linux) o `brew services list` (macOS) |
| La colección de Bruno no aparece | La carpeta seleccionada no contiene `bruno.json` | Asegurar que se selecciona `docs/bruno/` |
