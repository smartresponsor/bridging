# Subscripting → Interfacing Audit

## Current slice

Archive: `BridgingSan1.zip`

SHA-256:

```text
d57e33b98f6d6eb46a21e78943211f1ca3e1466c82ddcf4fb89bce82b008fb13
```

## Existing pattern found

The current Bridging slice already contains:
- `BridgeInterface`;
- `BridgeTarget`;
- `BridgeRegistry`;
- `BridgeManager`;
- `InterfacingScreenPayloadNormalizer`;
- dedicated `*Interfacing` rooms;
- auto-loaded `services_*_interfacing.yaml` files.

## Decision

Add a dedicated room:

```text
src/Service/SubscriptingInterfacing
src/ServiceInterface/SubscriptingInterfacing
```

## Boundary

No direct dependency on Subscripting concrete classes is introduced.

The bridge accepts object/shape payloads matching:
- component: `subscripting`
- contract: `subscription.presentation.v1`

## Added target

```text
interfacing.screen.subscripting.presentation
```
