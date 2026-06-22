# SKILL: Writing Plans

## When to use
Before implementing any brief, new module, or non-trivial change.

## Plan format

```markdown
## Goal
One sentence: what does success look like?

## Approach
2-3 sentences on strategy and key trade-offs.

## Files to create / modify
- `path/to/file.php` — what it does
- `path/to/another.php` — what it does

## Steps
1. Scaffold with `php artisan module:make X` (if new module)
2. ...

## Risks
- Risk → mitigation

## Definition of Done
- [ ] Tests pass
- [ ] Sprint checklist updated
- [ ] Conventions followed (no violations of CLAUDE.md)
```

## Rules
- Write the plan BEFORE any code
- Get developer alignment on the plan
- Update the plan if the approach changes
- Keep plans in the sprint doc or as a reply in the conversation
