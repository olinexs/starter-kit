# SKILL: Verification Before Completion

## When to use
Before marking ANY brief, task, or sprint as complete.

## Backend checklist
- [ ] `php artisan test` — zero failures
- [ ] `php artisan route:list` — expected routes present
- [ ] Happy path returns correct status code (200 / 201)
- [ ] Invalid input returns 422 with field errors
- [ ] Unauthenticated request returns 401
- [ ] No `dd()` or debug output in committed code
- [ ] Migration runs on a fresh database without error

## Frontend checklist
- [ ] Happy path works in browser (open the page, submit a form)
- [ ] Error state shows correctly when API fails
- [ ] Toast notification appears on success and error
- [ ] No `console.log()` in committed code
- [ ] Mobile viewport (375px) renders without overflow

## General checklist
- [ ] Follows all rules in `.claude/CLAUDE.md`
- [ ] No secrets or credentials in code
- [ ] Sprint doc checklist item ticked
- [ ] Commit created with correct message format
