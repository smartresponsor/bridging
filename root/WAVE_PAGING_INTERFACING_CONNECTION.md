# Wave: Paging Interfacing connection

## Audit summary

Neighboring bridge implementations use the same simple pattern:

1. A component-specific `Service/*Interfacing/*Bridge` class.
2. A matching `ServiceInterface/*Interfacing/*BridgeInterface`.
3. `BridgeInterface::supports()` plus `bridge()` dispatch compatibility.
4. `#[AutoconfigureTag('bridging.dispatch_bridge')]` for registry pickup.
5. `InterfacingScreenPayloadNormalizer` as the canonical Interfacing output normalizer.
6. A `BridgeTarget` constant for the Interfacing target string.
7. A component-specific `config/component/services_*_interfacing.yaml` alias file.

## Added Paging connection

This wave adds `PageBridgePayloadToInterfacingScreenBridge` and the target `interfacing.screen.page`. The bridge consumes Paging's stable outbound Page bridge payload, not Doctrine entities.

## Non-goals

- No EasyAdmin implementation.
- No CMS expansion.
- No SEO ownership.
- No Attachment storage ownership.
- No host role hierarchy.
