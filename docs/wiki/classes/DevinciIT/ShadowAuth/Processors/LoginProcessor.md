# `DevinciIT\\ShadowAuth\\Processors\\LoginProcessor`

Processes login form submissions and routes user to success or TOTP challenge.

## Responsibilities

- Validates POST + CSRF.
- Validates required username/password fields.
- Calls `Auth::beginLogin()`.
- Redirects based on outcome:
  - success -> dashboard
  - TOTP required -> setup/verify page
  - failure -> flash error

## Constructor

- `__construct(string $successRedirect = '/dashboard.php', string $totpRedirect = '/views/setup_2fa.php')`

## Public Methods

- `handle(): void`
