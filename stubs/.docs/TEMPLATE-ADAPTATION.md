# Template Adaptation Guide

## Purpose

`.template/` is a READ-ONLY reference of the base UI template (Materio / Vuetify).
This guide tells the AI how to adapt template patterns to the Ecogreen design system.

## Rules

1. **Never modify** anything inside `.template/` — read-only reference only.
2. Copy the relevant pattern into `resources/js/` and adapt it.
3. Always replace hardcoded colours with Ecogreen CSS tokens from `.design/colors_and_type.css`.
4. Always apply the Vuetify theme defined in `.design/DESIGN-SYSTEM.md`.

## Component Mapping

| Template pattern | How to adapt |
|---|---|
| AppTextField | Use `v-text-field variant="outlined" density="compact"` |
| AppSelect | Use `v-select variant="outlined" density="compact"` |
| Stat / KPI card | Copy structure, apply `color="primary"` and CSS tokens |
| Confirm dialog | Copy from `.template/.../dialogs/`, rebrand with Ecogreen colours |
| Data table | Use `v-data-table density="compact"`, add action slot |
| Default layout | Reference `.template/.../layouts/default`, keep nav structure |

## AI Instructions

When building a new page or component:
1. Check if a similar pattern exists in `.template/`
2. If yes — copy the structure, replace colours/fonts with Ecogreen tokens
3. If no — build from scratch following `.design/DESIGN-SYSTEM.md`
4. Never reference `.template/` paths in production code imports
