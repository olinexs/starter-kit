# Sprint Roadmap — {PROJECT_NAME}

**Project**: {PROJECT_NAME}
**Description**: {PROJECT_DESC}
**Team**: {TEAM_NAME}
**Updated**: {YEAR}

---

## Dependency Graph

```
Sprint {SPRINT_PADDED} [{SPRINT_TITLE}]
  └── Sprint XX [Next sprint]
        └── Sprint XX [...]
```

---

## Sprint Registry

| Sprint | Title | Status | ETC | PIC |
|---|---|---|---|---|
| sprint-{SPRINT_PADDED} | {SPRINT_TITLE} | active | {SPRINT_ETC} | {SPRINT_PIC} |

---

## Conventions

- Each sprint → its own file: `.docs/sprints/sprint-XX.md`
- Completed sprints → moved to `.docs/sprints/archive/`
- A sprint is **closed** when every checklist item is ticked and tests pass
- The AI always reads the **latest non-archived** sprint doc before implementing
- Never start sprint N+1 until sprint N is closed
