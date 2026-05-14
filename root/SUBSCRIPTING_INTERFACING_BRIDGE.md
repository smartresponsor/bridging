# Subscripting → Interfacing Bridge

## Purpose

This bridge room connects Subscripting outbound presentation payloads to the Interfacing screen payload canon.

Subscripting owns:
- subscription lifecycle;
- plans;
- renewals;
- cancellations;
- entitlement projection;
- subscription presentation payload.

Bridging owns:
- adaptation from `subscription.presentation.v1` into Interfacing screen payload shape.

Interfacing owns:
- rendering;
- screen composition;
- template/view behavior.

## Input contract

Primary input shape:

- contract: `subscription.presentation.v1`
- provider on Subscripting side: `SubscriptionPresentationPayloadProviderInterface`
- payload DTO on Subscripting side: `SubscriptionPresentationPayloadDto`

Bridging intentionally uses object/shape typing and does not import Subscripting classes directly.

## Output target

Bridge target:

- `BridgeTarget::SCREEN_SUBSCRIPTING_PRESENTATION`
- canonical value: `interfacing.screen.subscripting.presentation`

The returned value is an Interfacing-facing screen payload array normalized by `InterfacingScreenPayloadNormalizer`.

## Non-ownership rule

This bridge must not:

- mutate subscriptions;
- schedule renewals;
- cancel subscriptions;
- calculate pricing;
- execute billing/payment;
- render Twig/React directly;
- expose Doctrine entities;
- duplicate Subscripting lifecycle logic;
- duplicate Interfacing renderer logic.

## Expected usage

A host application or composition layer asks Subscripting for the presentation payload, then dispatches it through the Bridging manager toward the Subscripting Interfacing target.

```php
$payload = $subscriptionPresentationPayloadProvider->provide($query);
$screen = $bridgeManager->bridge($payload, BridgeTarget::SCREEN_SUBSCRIPTING_PRESENTATION);
```
