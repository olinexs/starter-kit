# Application Blueprint — {PROJECT_NAME}

**Project**: {PROJECT_NAME}
**Description**: {PROJECT_DESC}
**Team**: {TEAM_NAME}
**Year**: {YEAR}

> Update this file as the domain evolves.
> The AI reads this to understand entities, relationships, and business rules.

---

## Application Purpose

{PROJECT_DESC}

<!-- Expand with more detail: who uses it, what problems it solves -->

---

## Core Entities

<!-- Define each entity, its key fields, and relationships.
     Use the tree format below so the AI can parse it easily. -->

```
Entity [key fields]
├── RelatedEntity [foreign key → relationship type]
└── AnotherEntity [foreign key → relationship type]
```

---

## Module Inventory

| Module | Purpose | Status | Phase |
|---|---|---|---|
| Auth | Authentication & authorisation | planned | 1 |

---

## Business Rules

<!-- List rules as facts the AI must never violate when generating code. -->

1. <!-- Example: A record cannot be deleted once it has been approved. -->

---

## Integration Points

| External System | Direction | Purpose | Notes |
|---|---|---|---|
| SAP | inbound | master data | via RFC/REST |

---

## Glossary

| Term | Definition |
|---|---|
| | |
