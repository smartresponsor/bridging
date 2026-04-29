# Bridging Model Manifest

## Source-side model rule
Source components may emit their own result/context objects. They are not required to emit Interfacing-native contracts.

## Bridge model rule
Bridges adapt source-native models into target-native models.

## Current model families
- `CrudPageDefinition` → `CrudWorkbenchView` via `CrudingInterfacing`.
- `PageView` → screen payload array for Interfacing renderer via `AccessingInterfacing`.

## Stability rule
Control strict canonical contracts on the Bridging + Interfacing side, not across every ecosystem component. This keeps machine-written component code freer and cheaper to maintain.
