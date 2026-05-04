# Currencing → Interfacing Bridge

## Purpose

This bridge room connects Currencing outbound template context to the Interfacing screen payload canon.

Currencing owns currency metadata, selector context, minor units, formatting context, and normalization policy signals.
Interfacing owns rendering and UI composition.
Bridging owns the adaptation boundary between them.

## Input contract

Primary input payload:

- `App\Dto\Currency\CurrencyTemplateContext`

The bridge expects the Currencing component to provide the payload through its outbound template context contract, for example through `CurrencyTemplateContextProviderInterface` on the Currencing side.

## Output target

Bridge target:

- `BridgeTarget::SCREEN_CURRENCING_TEMPLATE_CONTEXT`
- canonical value: `interfacing.screen.currencing.template.context`

The returned value is an Interfacing-facing screen payload array normalized by `InterfacingScreenPayloadNormalizer`.

## Non-ownership rule

This bridge must not:

- fetch exchange rates;
- calculate converted amounts;
- render Twig or React directly;
- expose Doctrine entities;
- duplicate Currencing catalog logic;
- duplicate Interfacing renderer logic.

## Expected usage

A host application or composition layer asks Currencing for the template context, then dispatches it through the Bridging manager toward the Currencing Interfacing target.

```php
$context = $currencyTemplateContextProvider->provide(...);
$screen = $bridgeManager->bridge($context, BridgeTarget::SCREEN_CURRENCING_TEMPLATE_CONTEXT);
```
