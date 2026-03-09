# `DevinciIT\\ShadowAuth\\Processors\\RegisterProcessor`

Processes registration submissions including optional custom fields.

## Responsibilities

- Validates POST + CSRF.
- Validates username/password/confirmation.
- Enforces minimum password length.
- Validates and collects configured extra fields.
- Calls `Auth::registerWithData()` and surfaces constraint messages.

## Constructor

- `__construct(string $loginRedirect = '/views/login.php', array $extraFields = [])`

## Public Methods

- `handle(): void`

## Extra Field Format

Each item in `$extraFields` can define:

- `name` (`string`, required)
- `type` (`string`, optional, e.g. `email`)
- `required` (`bool`, optional)
