# CLAUDE.md — {PROJECT_NAME}

**Project**: {PROJECT_NAME}
**Team**: {TEAM_NAME}
**Year**: {YEAR}

> Primary AI instruction file. Read at the start of every session.
>
> Architecture  → `backend/.docs/ARCHITECTURE.md`
> Domain model  → `backend/.docs/app-blueprint.md`
> Active sprint → `backend/.docs/sprints/sprint-{SPRINT_PADDED}.md` (latest non-archived)
> Design system → `backend/.design/DESIGN-SYSTEM.md`

---

## 🤖 AI Automatic Behaviours

These actions happen WITHOUT the developer needing to ask:

| Trigger | AI Action |
|---|---|
| "create a module for X" | Run `php artisan module:make X` from `backend/`, then implement |
| "add a new module" | Ask for the module name, then run `module:make` |
| Starting a new brief | Read the active sprint doc first |
| Finishing a brief | Tick the checklist in the sprint doc + commit |
| Any new file created | Follow naming conventions below without being told |
| Asked to implement an API | Create FormRequest + Controller + Action + Repository |
| Asked to build a page | Create Service + Store + View + wire route in routes.{EXT} |

### Creating a new module — exact steps

When the developer says they want a new module (any phrasing):

```bash
# 1. Run from the backend directory
cd backend
php artisan module:make {ModuleName}

# This single command (eoads/module-make) scaffolds AND wires both sides:
# backend/Modules/{ModuleName}/              ← backend module (+ composer PSR-4, modules_statuses.json)
# frontend/{SRC_DIR}/modules/{moduleName}/   ← frontend module (service, store, view, routes)
# It also auto-imports the route into frontend/{SRC_DIR}/plugins/router/routes.{EXT}
# and adds the nav item in frontend/{SRC_DIR}/layouts/DefaultLayout.vue — no manual wiring needed.

# 2. composer dump-autoload
# 3. Add a brief to the active sprint doc
# 4. Confirm to the developer what was created
```

`module:make` is PascalCase only (e.g. `PurchaseOrder`). It handles both backend and
frontend in one step — do NOT scaffold the frontend folder by hand or re-edit the
router/nav; the command already did it. Never ask the developer to run these commands
themselves. Run them directly.

---

## Project Identity

| Item | Value |
|---|---|
| Project | {PROJECT_NAME} |
| Team | {TEAM_NAME} |
| Structure | `project-root/backend/` + `project-root/frontend/` |
| Backend | Laravel 12, API-only, port 8000 |
| Frontend | Vue 3 + Vite SPA, port 5173 |
| Module system | nwidart/laravel-modules (`backend/Modules/`) |
| Frontend modules | `frontend/{SRC_DIR}/modules/` |
| Auth | {AUTH_LABEL} |
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
class CreateItemAction
{
    public function execute(array $data): Item { ... }
}
```

### Rule 4 — Data access only through Repositories
```php
public function __construct(private ItemRepositoryInterface $repo) {}
```

### Rule 5 — Migrations live in `backend/database/migrations/` only
Never inside `Modules/`.

### Rule 6 — Route middleware
```php
Route::middleware('{AUTH_MIDDLEWARE}')
```

### Rule 7 — API response shape
```php
return new ItemResource($item);
return ItemResource::collection($items);
```

---

## Backend Module Structure

```
backend/Modules/{Name}/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Actions/
│   ├── Services/
│   ├── Repositories/
│   │   ├── {Name}RepositoryInterface.php
│   │   └── Eloquent{Name}Repository.php
│   ├── Models/
│   ├── Enums/
│   ├── Events/
│   ├── Observers/
│   ├── Notifications/
│   └── Providers/
│       └── {Name}ServiceProvider.php
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   ├── api.php
│   └── web.php
└── module.json
```

> `ServiceProvider` lives at `app/Providers/` and registers routes with
> `loadRoutesFrom(__DIR__ . '/../../routes/api.php')`.

---

## Frontend Conventions

### Rule 1 — Module structure
```
frontend/{SRC_DIR}/modules/{moduleName}/
├── services/{moduleName}Service.{EXT}   ← ALL axios calls
├── stores/{moduleName}Store.{EXT}       ← Pinia store
├── views/{ModuleName}View.vue        ← page component
├── components/                       ← local components
└── routes.{EXT}                         ← route definitions
```

### Rule 2 — Always use the shared axios instance
```{CODE_LANG}
import { api } from '@/plugins/axios'   // ✅ named export — the axios instance
import axios from 'axios'               // ❌
```

### Rule 3 — Composition API only
```vue
<script setup{TS_ATTR}>  <!-- ✅ always -->
```

### Rule 4 — Service layer owns all API calls
```{CODE_LANG}
const res = await itemService.index(params)
```

### Rule 5 — Register every new module route
```{CODE_LANG}
// frontend/{SRC_DIR}/plugins/router/routes.{EXT}
import itemRoutes from '@/modules/item/routes'
export const routes = [...existing, ...itemRoutes]
```

### Rule 6 — Add nav item for every user-facing module
```{CODE_LANG}
// frontend/{SRC_DIR}/layouts/DefaultLayout.vue — navItems array
{ title: 'Item', icon: 'ri-package-line', to: '/item' }
```

---

## Sprint Workflow

1. Find the active sprint: latest `backend/.docs/sprints/sprint-XX.md` not in `archive/`
2. Read the brief before implementing
3. Implement exactly what the brief describes — nothing more
4. After completing: tick `[x]` the checklist + commit

**Commit format:**
```
feat(ModuleName): what was done [sprint-{SPRINT_PADDED} brief-XX]
fix(ModuleName): what was fixed
chore: what was updated
```

---

## What to NEVER do

- Add business logic to controllers
- Call `axios` directly from a Vue component
- Use Options API (`export default {}`)
- Put migrations inside `Modules/`
- Hardcode colours — use `backend/.design/colors_and_type.css` tokens
- Leave `dd()`, `var_dump()`, or `console.log()` in committed code
- Skip writing the FormRequest for any user input
- Implement beyond what the current sprint brief specifies
- Mark a brief complete without running `php artisan test`
