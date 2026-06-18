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

---

## Installation

```bash
# 1. Create the project root
mkdir my-project && cd my-project

# 2. Create the Laravel backend
laravel new backend
cd backend

# 3. Require the starter kit
composer require eoads/eoads-starter-kit

# 4. Run the interactive installer
php artisan eoads:install
```

The installer will ask for:
- Project name & description
- Team / department name
- First sprint number, title, PIC, and ETC

Then open the **project root** in Claude Code:

```bash
cd ..
claude .
```

Tell Claude: *"I want to create a module for Purchase Orders"* — it scaffolds everything automatically.

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

| Path | Purpose |
|---|---|
| `resources/js/plugins/axios.js` | Shared Axios instance with auth interceptors |
| `resources/js/plugins/router/routes.js` | Root route registry |
| `resources/js/stores/toastStore.js` | Pinia toast/alert store |
| `resources/js/layouts/components/NavItems.vue` | Navigation menu |

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
Wires: route import in router/routes.js + nav item in NavItems.vue
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
