# Paging → Interfacing bridge

This bridge connects the Paging component's outbound `PageBridgePayload` contract to the canonical Interfacing screen payload used by Bridging.

## Canonical target

`BridgeTarget::PAGING_INTERFACING_SCREEN` / `BridgeTarget::SCREEN_PAGING_PAGE` resolves to:

```text
interfacing.screen.page
```

## Boundary rule

Bridging does not import Doctrine `Page` entities and does not require Paging classes to be autoloadable. It accepts PageBridgePayload-shaped objects through `toArray()`, `toInterfacingScreenPayload()`, or reader methods. This keeps the connector safe when Bridging is tested as a standalone sibling package.

## Output shape

The bridge normalizes Paging output into an Interfacing screen payload with:

- `id`: `page.{code}`
- `kind`: `document`
- `title`: Page title
- `subtitle`: page kind/version/effective date summary
- `eyebrow`: `Paging · Interfacing`
- `items`: attachment references converted to display rows
- `facts`: kind, version, publication/effective metadata, checksum
- `context`: code, slug, kind, status, publication status, acceptance flag
- `meta`: source payload, body HTML/text/Markdown/JSON, render hints, legal notice, attachments

## Ownership

Paging owns the page lifecycle and bridge payload. Interfacing owns visual shell/rendering. Bridging only adapts the Paging outbound contract into Interfacing's canonical screen payload.
