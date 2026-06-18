# eoads/eoads-starter-kit

> EO-ADS Laravel Starter Kit — full project scaffold with AI-assisted module development.

## Onboarding in 4 steps

```bash
# 1. Create a new Laravel project
laravel new myproject

# 2. Enter the project and require the starter kit
cd myproject
composer require eoads/eoads-starter-kit

# 3. Run the interactive installer
php artisan eoads:install

# 4. Open in Claude Code — you're ready
claude .
```

That's it. The new developer can immediately say:

> *"I want to create a module for Purchase Orders"*

And Claude Code will scaffold, implement, and wire everything automatically —
guided by the conventions pre-loaded into `.claude/CLAUDE.md`.

---

## What gets installed

| Path | Purpose |
|---|---|
| `.claude/CLAUDE.md` | AI instruction file — conventions, automatic behaviours |
| `.claude/settings.local.json` | Pre-approved artisan/git commands (no permission prompts) |
| `AGENTS.md` | Alias to CLAUDE.md |
| `.docs/ARCHITECTURE.md` | System architecture — the AI reads this every session |
| `.docs/app-blueprint.md` | Domain model template — fill in your entities |
| `.docs/sprints/sprint-01.md` | First sprint — ready to use |
| `.skills/` | 4 AI process skills (TDD, debugging, planning, verification) |
| `.design/DESIGN-SYSTEM.md` | Vuetify 3 theme — Ecogreen brand |
| `.design/colors_and_type.css` | CSS design tokens |
| `resources/js/plugins/axios.js` | Shared Axios instance with auth interceptors |
| `resources/js/plugins/router/routes.js` | Root router — add module routes here |
| `resources/js/stores/toastStore.js` | Pinia toast store |
| `resources/js/layouts/components/NavItems.vue` | Navigation menu |
| `dev-agent.sh` | Shell launcher — injects context automatically |

---

## Adding a new module

**AI way (recommended for onboarding):**
```
Just tell Claude Code:  "Create a module for [feature name]"
```
The AI runs `module:make`, implements the feature, and wires the route and nav.

**Manual way:**
```bash
php artisan module:make PurchaseOrder
```
Creates:
- `Modules/PurchaseOrder/` — full Laravel backend structure
- `resources/js/modules/purchaseOrder/` — Vue service, store, view, routes

---

## How the AI workflow works

```
Developer says: "create a warehouse module"
       ↓
Claude Code reads: .claude/CLAUDE.md + .docs/ARCHITECTURE.md + active sprint
       ↓
Runs: php artisan module:make Warehouse
       ↓
Implements: Service, Action, Repository, Controller, FormRequest (backend)
            Service, Store, View, Routes (frontend)
       ↓
Wires: route registration + nav item
       ↓
Confirms: what was created + next steps
```

---

## Package dependency

This starter kit requires and auto-installs:

- [`eoads/module-make`](https://packagist.org/packages/eoads/module-make) — the scaffolding engine

---

## Upgrading an existing project

```bash
composer update eoads/eoads-starter-kit
php artisan eoads:install --force
```
