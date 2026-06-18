# SKILL: Design System Application

## When to use
When building any UI component, page layout, or form.

## Steps
1. Check `.design/DESIGN-SYSTEM.md` for the correct Vuetify component props
2. Use `--eo-*` CSS tokens — never hardcode hex values
3. Look in `.template/` for structural patterns, then apply Ecogreen branding

## Component quick reference

| Element | Correct usage |
|---|---|
| Primary button | `<v-btn color="primary">` |
| Danger button | `<v-btn color="error">` |
| Text field | `<v-text-field variant="outlined" density="compact">` |
| Select | `<v-select variant="outlined" density="compact">` |
| Data table | `<v-data-table density="compact">` |
| Card | `<v-card rounded="lg" elevation="1">` |
| Chip / badge | `<v-chip color="primary" size="small">` |

## Checklist
- [ ] No hardcoded hex colours in `<style>` blocks
- [ ] Uses `--eo-*` tokens or Vuetify `color="primary"` props
- [ ] Renders correctly at 375px (mobile) and 1280px (desktop)
- [ ] Matches Ecogreen brand palette
