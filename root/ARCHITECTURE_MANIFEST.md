# Bridging Architecture Manifest

## Mission
Bridging adapts component-native results into canonical cross-component integration outputs without moving business ownership into the host application.

## Room law
- `CrudingInterfacing/` is the only room for generic CRUD to Interfacing workbench bridging.
- `*Interfacing/` rooms are for custom non-CRUD component screens.
- Pair rooms like `OrderingTaxating/` are for domain-to-domain bridges.
- Host applications should compose Bridging, not reimplement bridge services.

## Boundary law
Bridging may adapt, normalize, and route presentation payloads. It must not become a second renderer, a second CRUD component, or a storage for arbitrary host logic.
