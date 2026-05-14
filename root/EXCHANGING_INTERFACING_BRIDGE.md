# Exchanging ↔ Interfacing Bridge

## Purpose

This bridge converts the Exchanging outbound template context into the canonical Interfacing screen payload shape.

The bridge is intentionally shape-based and does not import Exchanging classes. This keeps Bridging independently bootable while still allowing a host app to connect Exchanging at runtime.

## Source contract

Expected Exchanging source object shape:

- `component`
- `viewType`
- `title`
- `money`
- `rate`
- `provider`
- `freshness`
- `audit`
- `links`
- `metadata`

Primary Exchanging provider on the source side:

```php
App\Exchanging\ServiceInterface\Exchange\ExchangeTemplateContextProviderInterface
```

## Bridge target

```php
App\Bridging\Bridge\Contract\BridgeTarget::SCREEN_EXCHANGING_TEMPLATE_CONTEXT
```

Value:

```text
interfacing.screen.exchanging.template.context
```

## Output payload

The bridge emits a normalized Interfacing screen payload with `id`, `kind`, `title`, `subtitle`, `eyebrow`, `items`, `facts`, `primaryActions`, and `meta`.

## Neighbor awareness

Exchanging neighbor hooks remain on the Exchanging side. Bridging maps the resulting Exchanging context to Interfacing; it should not calculate rates or own neighbor decisions.

Relevant neighbor areas: Currencing, Taxating, Billing, Paying, Ordering.
