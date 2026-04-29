# Bridging Contract Manifest

## Stable contracts in this component
- `App\Bridging\Bridge\Contract\BridgeInterface`
- `App\Bridging\Bridge\Contract\BridgeTarget`
- component-specific bridge interfaces such as `CrudPageToWorkbenchViewBridgeInterface`

## Contract rule
A bridge contract must describe adaptation only:
- source payload support
- target support
- adapted output

## Do not require
Do not require every source component to implement a single universal Interfacing contract. Source components may stay component-native.

## Interfacing rule
Bridges target Interfacing-owned renderer/view contracts; they must not own Interfacing internals beyond the public renderer contract.

## Interfacing payload canon
- Bridges targeting Interfacing must return a normalized screen payload.
- Generic CRUD payloads belong only to `CrudingInterfacing`.
- Component-specific bridges may adapt custom routes/screens, but must not redefine generic CRUD flow.
- Interfacing-facing payloads must define at least `id` and `kind`; `bridgeContext` is attached by Bridging.
