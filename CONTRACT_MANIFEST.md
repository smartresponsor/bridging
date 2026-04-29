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
