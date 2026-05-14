# Bridging Model Manifest

## Input freedom
Components may emit their own page/result/value objects.

## Output discipline
Bridges must normalize toward limited Interfacing-facing payload shapes. Generic CRUD remains centralized in `CrudingInterfacing`.

## Drift guard
When a component needs custom pages, add a dedicated `*Interfacing` bridge room. Do not duplicate generic CRUD operations there.

## Currencing model handoff
Currencing emits `CurrencyTemplateContext` as a read model for templates. Bridging maps that read model into a generic Interfacing screen payload and does not retain currency business state.

## Subscripting model handoff
Subscripting emits `subscription.presentation.v1` as a read-only presentation payload for Bridging.
Bridging maps that payload into a generic Interfacing screen payload and does not retain subscription business state.
