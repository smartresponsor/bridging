# Visible provider route adoption

Bridge owns route/resource/component adoption for visible e-commerce and admin URLs. Interfacing owns shell, provider mount, schema, and rendering.

Canonical visible routes now enter the Interfacing provider surface through Bridging routes instead of direct consumer template rewrites:

- `/catalog/` and `/catalog/{resourcePath}` -> Cataloging context
- `/crud/` and `/crud/{resourcePath}` -> Cruding context
- `/cruding/` and `/cruding/{resourcePath}` -> Cruding context
- `/vendor/` and `/vendor/{resourcePath}` -> Vendoring context
- `/vendoring/` and `/vendoring/{resourcePath}` -> Vendoring context

The controller renders only `interfacing/bridge/provider_surface.html.twig`. Twig remains the shell/schema handoff layer; it must not become a Bootstrap, EasyAdmin, handmade CSS, table, or form frontend.

Provider canon:

- Ant Design ProComponents is the primary provider.
- PrimeReact is the secondary/rich-facade provider.
- No fallback or legacy UI is a primary or insurance path.

Consumer repositories provide business/resource metadata. They are not directly rewritten by Interfacing scripts as the main migration path.
