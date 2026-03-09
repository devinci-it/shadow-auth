# `DevinciIT\\ShadowAuth\\Publisher\\Publisher`

File publishing service for demo scaffold, wiki scaffold, public assets, and views/endpoints.

## Responsibilities

- Copies package artifacts into host project.
- Supports force/non-force overwrite behavior.
- Returns detailed copy/skip report.

## Public Methods

- `publishPublic(?string $projectRoot = null, bool $force = false): array`
- `publishEndpoints(?string $projectRoot = null, bool $force = false): array`
- `publishDemo(?string $projectRoot = null, bool $force = false): array`
- `publishWiki(?string $projectRoot = null, bool $force = false): array`
- `publishVariant(string $variant, ?string $projectRoot = null, bool $force = false): array`

## Variants

- `demo` -> `shadow-auth-demo`
- `wiki` -> `shadow-auth-wiki`
- `public` and `endpoints` publish directly into host root.

## Return Value

Each publish call returns:

- `variant`: mode name
- `target_root`: resolved destination path
- `copied`: list of copied file mappings
- `skipped`: list of skipped mappings and reasons
