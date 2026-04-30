# Bridging Model Manifest

## Input freedom
Components may emit their own page/result/value objects.

## Output discipline
Bridges must normalize toward limited Interfacing-facing payload shapes. Generic CRUD remains centralized in `CrudingInterfacing`.

## Drift guard
When a component needs custom pages, add a dedicated `*Interfacing` bridge room. Do not duplicate generic CRUD operations there.
