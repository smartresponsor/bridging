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

## Currencing bridge contract
- `BridgeTarget::SCREEN_CURRENCING_TEMPLATE_CONTEXT` targets the Currencing selector/template-context Interfacing screen.
- `CurrencyTemplateContextToInterfacingScreenBridgeInterface` is the Bridge-side outbound adapter contract.
- Input is `App\Dto\Currency\CurrencyTemplateContext`; output is the normalized Interfacing screen payload array.

## Subscripting bridge contract
- `BridgeTarget::SCREEN_SUBSCRIPTING_PRESENTATION` targets the Subscripting presentation Interfacing screen.
- `SubscriptionPresentationToInterfacingScreenBridgeInterface` is the Bridge-side outbound adapter contract.
- Input is the shape emitted by Subscripting `subscription.presentation.v1`.
- Output is the normalized Interfacing screen payload array.
