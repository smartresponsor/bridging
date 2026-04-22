# Changelog

All notable changes to this repository should be documented in this file.

The project is still pre-1.0.0. Until the first tagged release, this changelog acts as a release-facing summary
of the currently established billing foundation and the evidence that should accompany the first public tag.

## [Unreleased]

### Added

- Antora-compatible documentation producer surface
- narrative API documentation for versioning and error contracts
- operational documentation for health, readiness, metrics, alerting, and migration discipline
- phpDocumentor-ready reference surface configuration
- OpenAPI generation path based on attribute metadata
- repository-facing release readiness guidance
- owner-facing root README surface

### Established

- tenant-aware invoice preview, create, and finalize flows
- ledger postings on invoice finalization
- trusted identity context boundary and local/test header bridge
- idempotency handling for mutating invoice operations
- billing-specific rate limiting
- PostgreSQL migration command and persistence strategy
- layered testing surface across unit, integration, API, browser, and Playwright levels

### Not yet claimed as released scope

- subscription lifecycle ownership
- recurring policy/state-machine ownership
- taxation ownership
- reporting workbench ownership
- operator/admin workbench ownership
