# Billing

Billing is a Symfony-oriented, tenant-aware billing foundation focused on invoice lifecycle management,
ledger side effects, billing safety, and documentation producer readiness.

This repository is **not** a SaaS subscription platform, a taxation engine, or a reporting workbench.
It should be understood as a hardened billing core that is suitable for further expansion.

## Current posture

### What the component already does

- builds deterministic invoice previews
- creates draft invoices
- finalizes invoices and records ledger side effects
- scopes billing operations to a tenant boundary
- enforces billing access checks and trusted identity context rules
- supports idempotent create/finalize flows
- applies billing-specific rate limiting
- supports PostgreSQL, file-backed, and in-memory runtime strategies
- produces OpenAPI and phpDocumentor-ready documentation surfaces
- ships with unit, integration, API, Panther, and Playwright test layers

### What this repository does not claim yet

- subscription lifecycle ownership
- recurring billing policy/state-machine ownership
- plan or price catalog ownership
- tax / VAT / sales-tax ownership
- rich reporting or export analytics surface
- customer self-service workbench
- operator/admin workbench

## Runtime surface

Canonical versioned HTTP routes live under `/api/billing/v1`.
The current invoice lifecycle surface is:

- `POST /api/billing/v1/invoices/preview`
- `POST /api/billing/v1/invoices`
- `POST /api/billing/v1/invoices/finalize`

Legacy unversioned routes still exist as a temporary compatibility bridge and are documented with deprecation semantics.

## Local setup

Install dependencies:

```bash
composer install
npm install
```

Create local environment config:

```bash
cp .env.example .env
```

Useful package-local commands:

```bash
composer qa:static
vendor/bin/phpunit
composer test:integration
composer docs:openapi
composer docs:phpdoc
```

For console-driven migration in a consuming host, register the bundle and run:

```bash
php bin/console billing:migrate
```

## Local Composer path installation

To consume this component from another local Symfony application before publishing a package,
add Billing as a Composer `path` repository in the consumer project:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../Billing",
      "options": {
        "symlink": true
      }
    }
  ],
  "require": {
    "smartresponsor/billing": "*@dev"
  }
}
```

Then wire the bundle manually in the consumer application:

- register `App\Billing\BillingBundle` in `config/bundles.php`
- add a minimal `billing:` section in `config/packages/billing.yaml`
- import `vendor/smartresponsor/billing/config/component/services.yaml`
- import `vendor/smartresponsor/billing/config/component/routes.yaml`

This repository does not ship a Flex recipe yet, so the local Composer installation surface is currently manual by design.

## Documentation map

### Repository docs

- [Architecture overview](docs/architecture/billing-platform-overview.md)
- [Billing boundary](docs/billing-boundary.md)
- [API versioning](docs/api-versioning.md)
- [API error contract](docs/api-error-contract.md)
- [Quality gates](docs/quality-gates.md)
- [Testing strategy](docs/testing-strategy.md)
- [Release readiness](docs/release-readiness.md)
- [Changelog](CHANGELOG.md)

### Antora producer surface

- `docs/antora.yml`
- `docs/modules/ROOT/pages/index.adoc`
- `docs/modules/ROOT/pages/architecture.adoc`
- `docs/modules/ROOT/pages/install.adoc`
- `docs/modules/ROOT/pages/operations.adoc`
- `docs/modules/ROOT/pages/api.adoc`
- `docs/modules/ROOT/pages/reference.adoc`
- `docs/modules/ROOT/pages/release.adoc`

## Release posture

This repository is intended to act as a documentation producer and a reusable billing component.
Before the first tagged release, release notes, OpenAPI output, phpDocumentor output, and executable gate evidence
should be refreshed from an environment with installed dependencies.

## GitHub suite

The repository now includes a minimal GitHub-facing suite intended to keep release hygiene and documentation surfaces discoverable:

- `.github/workflows/ci.yml`
- `.github/workflows/docs-surfaces.yml`
- `.github/pull_request_template.md`
- `.github/ISSUE_TEMPLATE/*`
- `.github/CODEOWNERS`

These files do not assemble a central documentation portal. They keep this repository positioned as a producer that validates its own runtime, quality, and generated documentation surfaces.

