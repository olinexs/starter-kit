# Architecture

## Overview

Decoupled architecture: Laravel 12 API backend + Vue 3 SPA frontend, both modular.

```
project-root/
├── Modules/                    ← Laravel business modules (nwidart/laravel-modules)
├── database/migrations/        ← ALL migrations here (never inside Modules/)
├── resources/js/
│   └── modules/                ← Vue frontend modules (mirror of backend)
├── .claude/CLAUDE.md           ← AI instruction file (read every session)
├── .docs/                      ← Architecture & sprint documentation
├── .skills/                    ← AI process methodology
└── .design/                    ← Ecogreen design system
```

---

## Backend Module Structure

```
Modules/{Name}/
├── app/
│   ├── Http/
│   │   ├── Controllers/        ← thin: orchestrate only, no logic
│   │   ├── Requests/           ← ALL validation here
│   │   └── Resources/          ← API response transformers (JsonResource)
│   ├── Actions/                ← single-purpose business logic
│   ├── Services/               ← stateful / multi-step business logic
│   ├── Repositories/
│   │   ├── {Name}RepositoryInterface.php
│   │   └── Eloquent{Name}Repository.php
│   ├── Models/
│   ├── Enums/
│   ├── Events/
│   ├── Observers/
│   ├── Notifications/
│   └── Providers/
│       └── {Name}ServiceProvider.php  ← binds repo, loads routes
└── routes/
    ├── api.php
    └── web.php                 ← empty (SPA handles routing)
```

### Request lifecycle
```
HTTP Request
  → FormRequest (validate input)
  → Controller (orchestrate)
  → Action / Service (business logic)
  → Repository (data access)
  → JsonResource (transform output)
  → JSON Response { data, message, meta }
```

---

## Frontend Module Structure

```
resources/js/modules/{moduleName}/
├── services/{moduleName}Service.js   ← axios API calls (only place)
├── stores/{moduleName}Store.js       ← Pinia state management
├── views/{ModuleName}View.vue        ← page-level component
├── components/                       ← module-local components
└── routes.js                         ← vue-router route definitions
```

### Data flow
```
User action → Component → Store action → Service (API) → Store state → Component (reactive)
```

---

## Authentication

- **Primary**: Keycloak OIDC — `auth:keycloak`
- **Fallback**: Laravel Sanctum — `auth:sanctum`
- **Route guard**: `auth:keycloak,sanctum,web` on all protected routes
- **Frontend**: Bearer token stored in `localStorage`, injected by axios interceptor

---

## API Contract

All API responses follow this shape:

```json
{
  "data": {},
  "message": "Success",
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

Error responses:
```json
{
  "message": "Validation failed",
  "errors": { "field": ["The field is required."] }
}
```

---

## Database

- **Engine**: MariaDB 10.11 (Galera multi-node cluster in production)
- **Migrations**: always in `database/migrations/` — never inside `Modules/`
- **Naming**: `{timestamp}_create_{table}_table.php`

---

## Shared Infrastructure

| Layer | Tool | Location |
|---|---|---|
| HTTP client | Axios (shared instance) | `resources/js/plugins/axios.js` |
| Router | Vue Router | `resources/js/plugins/router/routes.js` |
| Toast / alerts | Pinia toastStore | `resources/js/stores/toastStore.js` |
| Navigation | NavItems component | `resources/js/layouts/components/NavItems.vue` |
| Design tokens | CSS variables | `.design/colors_and_type.css` |
| Vuetify theme | Theme config | `.design/DESIGN-SYSTEM.md` |
