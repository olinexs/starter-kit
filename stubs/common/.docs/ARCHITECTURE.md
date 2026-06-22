# Architecture

## Overview

Decoupled architecture: Laravel 12 API backend + Vue 3 SPA frontend, in separate directories.

```
project-root/
├── backend/                        ← Laravel app (API only)
│   ├── Modules/                    ← Business modules (nwidart/laravel-modules)
│   │   └── {Name}/
│   │       ├── app/
│   │       │   ├── Http/
│   │       │   │   ├── Controllers/
│   │       │   │   ├── Requests/
│   │       │   │   └── Resources/
│   │       │   ├── Actions/
│   │       │   ├── Services/
│   │       │   ├── Repositories/
│   │       │   │   ├── {Name}RepositoryInterface.php
│   │       │   │   └── Eloquent{Name}Repository.php
│   │       │   ├── Models/
│   │       │   ├── Enums/
│   │       │   ├── Events/
│   │       │   ├── Observers/
│   │       │   └── Notifications/
│   │       ├── Providers/
│   │       │   └── {Name}ServiceProvider.php
│   │       └── routes/
│   │           ├── api.php
│   │           └── web.php
│   ├── app/
│   │   └── Models/
│   ├── database/
│   │   ├── migrations/             ← ALL migrations here (never inside Modules/)
│   │   ├── seeders/
│   │   └── factories/
│   ├── .claude/CLAUDE.md           ← AI instruction file
│   ├── .docs/                      ← Architecture & sprint documentation
│   ├── .skills/                    ← AI process methodology
│   └── .design/                    ← Ecogreen design system
└── frontend/                       ← Vue 3 + Vite SPA
    └── resources/js/
        ├── modules/                ← Frontend modules (mirror of backend)
        │   └── {moduleName}/
        │       ├── services/{moduleName}Service.js
        │       ├── stores/{moduleName}Store.js
        │       ├── views/{Name}View.vue
        │       ├── components/
        │       └── routes.js
        ├── plugins/
        │   ├── axios.js            ← Shared Axios instance
        │   └── router/routes.js    ← Root route registry
        ├── stores/
        │   └── toastStore.js
        └── layouts/
            └── components/
                └── NavItems.vue
```

---

## Request Lifecycle (Backend)

```
HTTP Request
  → FormRequest (validate input)
  → Controller (orchestrate — thin)
  → Action / Service (business logic)
  → Repository (data access)
  → JsonResource (transform output)
  → JSON Response { data, message, meta }
```

---

## Data Flow (Frontend)

```
User action → Component → Store action → Service (axios) → Store state → Component (reactive)
```

---

## Authentication

- **Primary**: Keycloak OIDC — `auth:keycloak`
- **Fallback**: Laravel Sanctum — `auth:sanctum`
- **Route guard**: `auth:keycloak,sanctum,web` on all protected routes
- **Frontend**: Bearer token stored in `localStorage`, injected by axios interceptor

---

## API Contract

All API responses:

```json
{
  "data": {},
  "message": "Success",
  "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 72 }
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
- **Migrations**: always in `backend/database/migrations/` — never inside `Modules/`

---

## Shared Infrastructure

| Layer | Tool | Location |
|---|---|---|
| HTTP client | Axios (shared instance) | `frontend/resources/js/plugins/axios.js` |
| Router | Vue Router | `frontend/resources/js/plugins/router/routes.js` |
| Toast / alerts | Pinia toastStore | `frontend/resources/js/stores/toastStore.js` |
| Navigation | NavItems component | `frontend/resources/js/layouts/components/NavItems.vue` |
| Design tokens | CSS variables | `backend/.design/colors_and_type.css` |
| Vuetify theme | Theme config | `backend/.design/DESIGN-SYSTEM.md` |
