# Bridging

Bridging is a thin Symfony-oriented integration component for explicit handoff logic between Smart Responsor ecosystem components.

Its purpose is to remove repetitive host-application glue code by formalizing source-to-target transfer, lightweight transformation, and bridge registration.

## Current package surface

This repository currently ships the minimal package surface required to be consumed from a host Symfony application:

- Composer package name: `bridging/bridge`
- Symfony bundle class: `App\BridgeBundle`
- Extension alias: `bridge`
- Component service import: `vendor/bridging/bridge/config/component/services.yaml`
- Component route import: `vendor/bridging/bridge/config/component/routes.yaml`

## Runtime posture

This is an early skeleton. It intentionally keeps the runtime surface narrow:

- bundle registration
- config alias
- service loading
- seed bridge contract
- seed bridge registry
- QA tool dependencies for CS Fixer and PHPStan

## Local setup

```bash
composer install
```

Useful package-local commands:

```bash
composer cs:check
composer cs:fix
composer phpstan
composer test
```

## Local Composer path installation

To consume this component from another local Symfony application before publishing it, add Bridging as a Composer `path` repository in the consumer project:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../Bridging",
      "options": {
        "symlink": true
      }
    }
  ],
  "require": {
    "bridging/bridge": "*@dev"
  }
}
```

Then register and import it manually in the consumer application:

1. register `App\BridgeBundle` in `config/bundles.php`
2. add a minimal `bridge:` section in `config/packages/bridge.yaml`
3. import `vendor/bridging/bridge/config/component/services.yaml`
4. import `vendor/bridging/bridge/config/component/routes.yaml`

## Minimal host configuration

```yaml
# config/packages/bridge.yaml
bridge:
    defaults:
        strict_resolution: true
```

## Scope discipline

Bridging should stay thin.
It must not absorb business logic from source or target domains.
