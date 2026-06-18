# CLAUDE.md — EO-ADS AI Development Guide

> This file is the primary instruction set for Claude Code.
> Read it at the start of every session. All conventions here are non-negotiable.
>
> Architecture detail  → `.docs/ARCHITECTURE.md`
> Domain model         → `.docs/app-blueprint.md`
> Active sprint        → `.docs/sprints/sprint-XX.md` (latest non-archived)
> Design system        → `.design/DESIGN-SYSTEM.md`

---

## 🤖 AI Automatic Behaviours

These actions happen WITHOUT the developer needing to ask:

| Trigger | AI Action |
|---|---|
| "create a module for X" | Run `php artisan module:make X`, then implement |
| "add a new module" | Ask for the module name, then run `module:make` |
| Starting a new brief | Read the active sprint doc first |
| Finishing a brief | Tick the checklist in the sprint doc + commit |
| Any new file created | Follow naming conventions below without being told |
| Asked to implement an API | Create FormRequest + Controller + Action + Repository |
| Asked to build a page | Create Service + Store + View + wire route in routes.js |

### Creating a new module — exact steps

When the developer says they want a new module (any phrasing):

```bash
# 1. Scaffold both backend and frontend
php artisan module:make {ModuleName}

# 2. Register the frontend route in resources/js/plugins/router/routes.js
# 3. Add the nav item in resources/js/layouts/components/NavItems.vue
# 4. Add a brief to the active sprint doc
# 5. Confirm to the developer what was created
```

Never ask the developer to run these commands themselves. Run them directly.

---

## Project Identity

| Item | Value |
|---|---|
| Backend | Laravel 12, API-only, port 8000 |
| Frontend | Vue 3 + Vite SPA, port 5173 |
| Module system | nwidart/laravel-modules (backend) + `resources/js/modules/` (frontend) |
| Auth | Keycloak (primary) + Sanctum (fallback) |
| Database | MariaDB (Galera multi-node in production) |
| State | Pinia |
| UI | Vuetify 3 + Ecogreen design system |
| API format | `{ data, message, meta }` JSON |

---

## Backend Conventions

### Rule 1 — Controllers are thin orchestrators
```php
// ✅ correct
public function store(StoreRequest $request, CreateAction $action): JsonResponse
{
    return new ResourceResponse($action->execute($request->validated()), 201);
}

// ❌ wrong — never put logic in a controller
public function store(Request $request): JsonResponse
{
    $item = Item::create($request->all());
    return response()->json($item);
}
```

### Rule 2 — All validation in FormRequests
Never `$request->validate()` inline. Every endpoint has its own Request class.

### Rule 3 — Business logic in Actions (single-purpose) or Services (stateful)
```php
// Action — one public method, one responsibility
class CreatePurchaseOrderAction
{
    public function execute(array $data): PurchaseOrder { ... }
}
```

### Rule 4 — Data access only through Repositories
```php
// Always inject the Interface, never the concrete class
public function __construct(private PurchaseOrderRepositoryInterface $repo) {}
```

### Rule 5 — Migrations live in `database/migrations/` only
Never inside `Modules/`. The module directory is for app code only.

### Rule 6 — Route middleware
```php
Route::middleware('auth:keycloak,sanctum,web')
```

### Rule 7 — API response shape
```php
// Always use JsonResource / ResourceCollection
return new PurchaseOrderResource($order);          // single
return PurchaseOrderResource::collection($orders); // list
```

---

## Frontend Conventions

### Rule 1 — Module structure (mirror the backend)
```
resources/js/modules/{moduleName}/
├── services/{moduleName}Service.js   ← ALL axios calls — nowhere else
├── stores/{moduleName}Store.js       ← Pinia store — one per module
├── views/{ModuleName}View.vue        ← page component
├── components/                       ← local-only components
└── routes.js                         ← route definitions for this module
```

### Rule 2 — Always use the shared axios instance
```js
import api from '@/plugins/axios'   // ✅
import axios from 'axios'           // ❌ never
```

### Rule 3 — Composition API only, no Options API
```vue
<script setup>          <!-- ✅ always -->
<script>                <!-- ❌ never -->
export default { ... }  <!-- ❌ never -->
```

### Rule 4 — Service layer owns all API calls
```js
// ✅ correct — in the store
const res = await purchaseOrderService.index(params)

// ❌ wrong — API call inside a component
const res = await api.get('/purchase-order')
```

### Rule 5 — Register every new module route
After `module:make`, immediately add to `resources/js/plugins/router/routes.js`:
```js
import purchaseOrderRoutes from '@/modules/purchaseOrder/routes'
export const routes = [...existingRoutes, ...purchaseOrderRoutes]
```

### Rule 6 — Add nav item for every user-facing module
In `resources/js/layouts/components/NavItems.vue`:
```js
{ title: 'Purchase Order', icon: 'mdi-file-document', to: '/purchase-order' }
```

---

## Sprint Workflow

### Reading the sprint doc
Before implementing anything, find the active sprint:
```
.docs/sprints/sprint-XX.md  (highest number, not in archive/)
```
Read the active brief. Implement exactly what the brief describes — nothing more.

### Updating the sprint doc
After completing each brief item:
- Tick `[x]` the checklist item
- Commit: `git commit -m "feat(ModuleName): brief description [sprint-XX brief-YY]"`

### Starting a new brief
Write a plan first (see `.skills/writing-plans/SKILL.md`).
Get alignment before writing code.

---

## Git Conventions

```
Branch:  feat/{module-name}-{brief-description}
Commit:  feat(ModuleName): what was done [sprint-XX brief-YY]
         fix(ModuleName): what was fixed
         chore: what was updated
```

---

## What to NEVER do

- Add business logic to controllers
- Call `axios` directly from a Vue component
- Use Options API (`export default {}`)
- Put migrations inside `Modules/`
- Hardcode colours — use CSS tokens from `.design/colors_and_type.css`
- Leave `dd()`, `var_dump()`, or `console.log()` in committed code
- Skip writing the FormRequest for any user input
- Implement beyond what the current sprint brief specifies
- Mark a brief complete without running `php artisan test`
