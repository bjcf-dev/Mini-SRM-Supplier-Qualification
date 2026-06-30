# SRM — Supplier Qualification (Homologación de Proveedores)

Módulo de calificación de proveedores (Supplier Relationship Management) con **Domain-Driven Design** estricto y **Arquitectura Hexagonal**.

---

## Stack

| Capa | Tecnología |
|---|---|
| Lenguaje | PHP 8.4 |
| Framework | Symfony 8.1 |
| ORM | Doctrine ORM 3.6 |
| BD | PostgreSQL 16 |
| Infra | Docker + docker compose |
| Tests | PHPUnit 13.2 |

---

## Casos de uso

### 1. Calificar un proveedor

Un auditor emite un dictamen técnico, financiero y legal sobre un proveedor. El sistema asigna automáticamente el estado según la puntuación:

| Score | Estado |
|---|---|
| 0 – 59 | `REJECTED` |
| 60 – 100 | `APPROVED` |

```
POST /api/qualifications
Content-Type: application/json

{
    "supplierId": "550e8400-e29b-41d4-a716-446655440001",
    "auditorId":  "550e8400-e29b-41d4-a716-446655440002",
    "score":      85,
    "comments":   "Auditoría superada"
}

→ 201 Created
{ "id": "47ba6d03-8a5b-480a-b22e-7363404117c2" }
```

Errores de validación:

```
→ 400 Bad Request
{ "error": "QualificationScore must be between 0 and 100." }
```

### 2. Listar calificaciones

```
GET /api/qualifications

→ 200 OK
[
    {
        "id":         "47ba6d03-...",
        "supplierId": "550e8400-...",
        "auditorId":  "550e8400-...",
        "score":      85,
        "status":     "APPROVED",
        "comments":   "Auditoría superada",
        "createdAt":  "2026-06-30T07:48:02+00:00",
        "expiresAt":  "2027-06-30T07:48:02+00:00"
    }
]
```

### 3. Interfaz web

```
http://localhost:8000/app
```

Panel interactivo con formulario de creación y tabla de calificaciones.

---

## Reglas de negocio

- **Rango de score:** Entero entre 0 y 100 inclusive. Fuera de rango → `InvalidArgumentException`.
- **Estado automático:** Score < 60 → `REJECTED`, Score ≥ 60 → `APPROVED`.
- **Inmutabilidad:** Una vez creada, una calificación no se modifica. No hay PUT/PATCH.
- **Vigencia:** Por defecto 12 meses desde la creación. `expiresAt` debe ser estrictamente posterior a `createdAt`.

---

## Cómo arrancar

```bash
# Levantar contenedores
docker compose up -d

# Ver logs
docker compose logs -f app

# Ejecutar tests
docker compose exec -e APP_ENV=test app php vendor/bin/phpunit

# Acceder a la API
curl http://localhost:8000/api/qualifications

# Migraciones (si se agregan nuevas entidades)
docker compose exec app php bin/console doctrine:migrations:diff
docker compose exec app php bin/console doctrine:migrations:migrate
```

---

## Archivos de configuración (.gitignore)

Se provee una plantilla `.gitignore.example` con los archivos que **nunca** deben commitearse.

Para comenzar desde cero:
```bash
cp .gitignore.example .gitignore
```

Exclusiones principales:
- `.env` y variantes (`.env.dev`, `.env.test`, `.env.local`, etc.) — claves y credenciales
- `/vendor/` y `/var/` — dependencias y caché de Symfony
- Artefactos del SO: `.DS_Store`, `Thumbs.db`, `*.swp`

---

## Arquitectura

```
src/
└── Qualification/
    ├── Domain/
    │   ├── Model/
    │   │   ├── Qualification.php              ← Agregado raíz
    │   │   ├── QualificationId.php            ← VO (UUID v4)
    │   │   ├── SupplierId.php                 ← VO (UUID v4)
    │   │   ├── AuditorId.php                  ← VO (UUID v4)
    │   │   ├── QualificationScore.php         ← VO (0–100)
    │   │   └── QualificationStatus.php        ← Enum
    │   └── Repository/
    │       └── QualificationRepositoryInterface.php  ← Puerto
    ├── Application/
    │   └── Create/
    │       ├── CreateQualificationCommand.php
    │       └── CreateQualificationCommandHandler.php
    └── Infrastructure/
        ├── Persistence/
        │   └── Doctrine/
        │       ├── Mapping/Qualification.orm.xml
        │       ├── Type/
        │       │   ├── QualificationIdType.php
        │       │   ├── SupplierIdType.php
        │       │   ├── AuditorIdType.php
        │       │   ├── QualificationScoreType.php
        │       │   └── QualificationStatusType.php
        │       └── Repository/
        │           └── DoctrineQualificationRepository.php
        └── Ui/
            └── Http/
                └── Controller/
                    ├── CreateQualificationController.php
                    └── ListQualificationsController.php
```

Capas con dependencia unidireccional: **Infrastructure → Application → Domain** (nada del dominio conoce el exterior).

---

## Tests

```bash
docker compose exec -e APP_ENV=test app php vendor/bin/phpunit
```

- **36 tests unitarios** — dominio puro, sin framework (VOs, entidad, reglas de negocio, límites 59/60).
- **7 tests funcionales** — cliente HTTP de Symfony, ciclo completo con persistencia real en PostgreSQL.

---

## Bruno — colección API

En `docs/bruno/` hay una colección completa para [Bruno](https://www.usebruno.com/):

```
1 - Crear Calificación/  4 requests (aprobado, rechazado, límites)
2 - Errores (400)/       6 requests (validaciones)
3 - Listar/              1 request
```

Abrir con **File → Open Collection** → seleccionar `docs/bruno/bruno.json`.

Guía detallada paso a paso: [`docs/testing-con-bruno.md`](docs/testing-con-bruno.md)
