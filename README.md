# eoads/eoads-starter-kit

> EO-ADS Laravel Starter Kit — scaffolds a full project structure with AI-assisted module development via Claude Code.

---

## Project Structure

This starter kit is designed for a **decoupled** backend + frontend architecture:

```
my-project/
├── backend/                        ← Laravel 12 API (run artisan from here)
│   ├── Modules/                    ← Business modules (nwidart/laravel-modules)
│   ├── .claude/CLAUDE.md           ← AI instruction file
│   ├── .docs/                      ← Architecture & sprint docs
│   ├── .skills/                    ← AI process methodology
│   └── .design/                    ← Ecogreen design system
└── frontend/                       ← Vue 3 + Vite SPA
    └── resources/js/
        └── modules/                ← Frontend modules (mirror of backend)
```

> **Note on ordering:** the `backend/` folder is created by `laravel new backend`,
> and the `frontend/` folder is created by `php artisan eoads:install`. You don't
> create these folders yourself — the tooling generates them.

---

## Requirements

Make sure these are installed and on your `PATH` before starting:

- **PHP** 8.2+ and **Composer**
- The **`laravel`** installer (`composer global require laravel/installer`)
- **Node.js** and **npm**

---

## Quick start (one command — recommended)

Install the global `eoads` command once:

```bash
composer global require eoads/installer
```

> Ensure Composer's global bin is on your `PATH` (see [installer/README.md](installer/README.md)).

Then, from **any folder**, create a complete project:

```bash
eoads new my-project
```

This does **everything** automatically and **asks your preferences** during install
(project name, team, sprint, frontend template, JS/TS, version, auth):

1. Creates `my-project/`
2. Runs `laravel new backend`
3. Runs `composer require eoads/eoads-starter-kit`
4. Runs `php artisan eoads:install` (the interactive questionnaire) + scaffolds the
   frontend with `npm install`

The result is `my-project/backend/` + `my-project/frontend/`, ready to run.
Omit the name (`eoads new`) and it will prompt for one.

### Alternative: bootstrap script (no global install)

If you'd rather not install the global command, run the bundled script from this
repo instead — same result:

```bash
# macOS / Linux / Git-Bash
./create-project.sh my-project
```
```bat
:: Windows
create-project.bat my-project
```

> Run it from the **parent** directory — the script creates `my-project/` for you.

Prefer to do each step by hand? See **Manual installation** below.

---

## Manual installation

These steps do exactly what the one-command script does, just by hand:

```bash
# 1. Create the project root
mkdir my-project && cd my-project

# 2. Create the Laravel backend (creates the backend/ folder)
laravel new backend
cd backend

# 3. Require the starter kit
composer require eoads/eoads-starter-kit

# 4. Run the interactive installer (creates the frontend/ folder + npm install)
php artisan eoads:install
```

The installer will ask for:
- Project name & description
- Team / department name
- First sprint number, title, PIC, and ETC
- **Frontend template** (discovered from the kit's bundled templates)
- **JavaScript or TypeScript**
- **Version** — Starter Kit or Full
- **Authentication system** — Local (Sanctum), LDAP (Active Directory), or Keycloak (SSO)

Then open the **project root** in Claude Code:

```bash
cd ..
claude .
```

Tell Claude: *"I want to create a module for Purchase Orders"* — it scaffolds everything automatically.

---

## Running the project

```bash
# Backend API (port 8000)
cd backend && php artisan serve

# Frontend SPA (port 5173) — in a second terminal
cd frontend && npm run dev
```

---

## What gets installed

### Backend (`backend/`)

| Path | Purpose |
|---|---|
| `.claude/CLAUDE.md` | AI instruction file — conventions & automatic behaviours |
| `.claude/settings.local.json` | Pre-approved artisan/git commands (no permission prompts) |
| `AGENTS.md` | Alias pointing to CLAUDE.md |
| `.docs/ARCHITECTURE.md` | Full system architecture reference |
| `.docs/app-blueprint.md` | Domain model — fill in your entities and rules |
| `.docs/sprints/sprint-XX.md` | First sprint doc with your info filled in |
| `.docs/sprints/sprint-roadmap.md` | Sprint registry |
| `.skills/` | 4 AI process skills (TDD, debugging, planning, verification) |
| `.design/DESIGN-SYSTEM.md` | Vuetify 3 theme — Ecogreen brand |
| `.design/colors_and_type.css` | CSS design tokens |
| `dev-agent.sh` | Shell launcher for Claude Code |

### Frontend (`frontend/`)

> The exact file set depends on the **template**, **JS/TS**, and **auth** options
> chosen during install. The table below shows the core working files common to
> the default Vue + Vuetify template; the selected auth adds login pages and an
> auth module, and the full template source is parked under
> `resources/js/.template/` as read-only AI reference.

| Path | Purpose |
|---|---|
| `package.json` | Vue 3 + Vuetify 3 + Pinia + Axios + Vue Router + Vite |
| `vite.config.js` | `@` alias → `resources/js/`, proxy `/api` → `localhost:8000` |
| `index.html` | App entry point |
| `resources/js/main.js` | App bootstrap — wires Vuetify, Pinia, Router |
| `resources/js/App.vue` | Root component |
| `resources/js/plugins/vuetify.js` | Ecogreen Vuetify theme config |
| `resources/js/plugins/axios.js` | Shared Axios instance (`export { api }`) with auth interceptors |
| `resources/js/plugins/router/routes.js` | Root route registry |
| `resources/js/stores/toastStore.js` | Pinia toast/alert store |
| `resources/js/layouts/DefaultLayout.vue` | App shell + navigation (`navItems`) |

> `npm install` runs automatically after install.

---

## Adding a new module

**AI way (recommended):**
```
Tell Claude Code: "Create a module for [feature name]"
```
Claude runs `module:make`, implements the feature, and wires the route and nav item.

**Manual way (from `backend/`):**
```bash
php artisan module:make PurchaseOrder
```

Creates:
```
backend/Modules/PurchaseOrder/          ← full backend structure
frontend/resources/js/modules/purchaseOrder/  ← service, store, view, routes
```

Skip frontend scaffold:
```bash
php artisan module:make PurchaseOrder --no-frontend
```

---

## How the AI workflow works

```
Developer says: "create a module for Inventory"
       ↓
Claude reads: .claude/CLAUDE.md + .docs/ARCHITECTURE.md + active sprint doc
       ↓
Runs: php artisan module:make Inventory  (from backend/)
       ↓
Backend:  Controller, FormRequest, Action, Repository, ServiceProvider, routes
Frontend: Service, Pinia store, Vue view, routes.js
       ↓
Wires: route import in router/routes.js + nav item in DefaultLayout.vue
       ↓
Commits: feat(Inventory): scaffold module [sprint-XX brief-XX]
```

---

## Upgrading

```bash
cd backend
composer update eoads/eoads-starter-kit eoads/module-make
php artisan eoads:install --force
```

> Use `--force` to overwrite existing stub files. Back up any customised docs first.

---

## Dependencies

- [`eoads/module-make`](https://github.com/olinexs/eoads-module-make) — the scaffolding engine (auto-installed)
- `illuminate/console` ^11.0|^12.0
- `illuminate/support` ^11.0|^12.0
