# App Host Interfacing Bridge

The host application owns runtime entry routes and composition decisions. It must
not become a visual renderer for primary UI surfaces.

Canonical dashboard flow:

```text
App dashboard contract
  -> Bridging AppHostInterfacing bridge
  -> Interfacing provider surface
  -> Ant Design ProComponents primary provider
```

The App dashboard bridge normalizes host-level dashboard composition payloads
into provider-surface payloads consumed by Interfacing. Cruding, Cataloging,
Vendoring, and other components can contribute data or widget contracts, but the
host dashboard is not assigned to a single component route.
