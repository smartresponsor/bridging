# Bridging Architecture Manifest

## Mission
Bridging owns inter-component adaptation only. It must not become a second CRUD component, a second Interfacing component, or a host-application glue dump.

## Core rule
A bridge translates one component-owned result/context into another component-owned rendering or integration contract.

## Canonical boundaries
- Generic CRUD HTML operations belong to `CrudingInterfacing` only.
- Custom non-CRUD routes belong to component-specific `*Interfacing` rooms.
- Cross-domain business integrations belong to dedicated pair rooms such as `OrderingTaxating`.
- Host applications should consume bridge services, not implement bridge logic.

## Forbidden drift
- Re-implementing generic CRUD orchestration inside component bridges.
- Owning Twig layout policy that should live in Interfacing.
- Owning business access rules that belong to the source component.
- Dumping unrelated adapters, DTO transport code, or random listeners into Bridging.

## Current rooms
- `src/Service/CrudingInterfacing/` — canonical generic CRUD → Interfacing bridges.
- `src/Service/AccessingInterfacing/` — custom Accessing routes → Interfacing bridges.
- `src/Service/BillingInterfacing/` — reserved room for Billing custom screens.
- `src/Service/CatalogingInterfacing/` — reserved room for Cataloging custom screens.
- `src/Service/TaxatingInterfacing/` — reserved room for Taxating custom screens.
- `src/Service/OrderingTaxating/` — reserved room for cross-domain Order ↔ Tax bridges.

## Activation model
Controllers or component responders call contracts they own. Concrete bridge implementations live here and call target-component contracts such as Interfacing renderers.
