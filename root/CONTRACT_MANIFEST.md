# Bridging Contract Manifest

## Core contracts
- `BridgeInterface` is the registry-facing dispatch contract.
- `BridgeTarget` defines canonical target identifiers.
- `InterfacingScreenPayloadNormalizer` enforces the minimal Interfacing-facing payload canon.

## Minimal payload canon
Every Interfacing-facing screen payload must provide:
- `id`
- `kind`

The normalizer also canonicalizes optional keys such as:
- `title`
- `subtitle`
- `eyebrow`
- `primaryActions`
- `secondaryActions`
- `context`
- `items`
- `itemCount`
- `facts`
- `meta`
- `bridgeContext`
