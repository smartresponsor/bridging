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

## Currencing → Interfacing room
- `CurrencingInterfacing/` adapts Currencing template-context read models into Interfacing-facing selector/screen payloads.
- Currencing remains the owner of currency metadata and selector context; Interfacing remains the renderer/UI owner.

## Subscripting → Interfacing room
- `SubscriptingInterfacing/` adapts Subscripting presentation payloads into Interfacing-facing screen payloads.
- Subscripting remains the owner of subscription lifecycle/read models.
- Bridging owns shape adaptation.
- Interfacing remains the renderer/UI owner.
