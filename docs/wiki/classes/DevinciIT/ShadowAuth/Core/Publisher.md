# `DevinciIT\\ShadowAuth\\Core\\Publisher`

Core-level convenience wrapper that forwards calls to `DevinciIT\\ShadowAuth\\Publisher\\Publisher`.

## Public Methods

- `publishDemo(?string $projectRoot = null, bool $force = false): array`
- `publishWiki(?string $projectRoot = null, bool $force = false): array`

## Return Shape

All methods return array payload from publisher service, including:

- `variant`
- `target_root`
- `copied`
- `skipped`
