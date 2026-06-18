# Application Blueprint

> Domain model and business rules for this application.
> Update this file as the domain evolves.
> The AI reads this to understand entities, relationships, and constraints.

---

## Application Purpose

<!-- One paragraph: what does this application do and who uses it? -->

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

1. <!-- Example: A Purchase Order cannot be deleted once it has been approved. -->

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
