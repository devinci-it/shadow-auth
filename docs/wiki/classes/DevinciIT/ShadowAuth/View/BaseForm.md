# `DevinciIT\\ShadowAuth\\View\\BaseForm`

Abstract HTML form field renderer with built-in CSRF hidden input injection.

## Responsibilities

- Combines required fields with optional extra fields.
- Prevents duplicate field names against required definitions.
- Escapes names, labels, values, and attributes.
- Generates HTML inputs and labels.

## Public Methods

- `setExtraFields(array $fields): static`: Appends custom field descriptors.
- `render(): string`: Returns concatenated HTML for all fields.

## Field Descriptor Keys

Recognized keys include:

- `name`
- `type`
- `label`
- `autocomplete`
- `required`
- `value`

Unknown keys that match valid attribute syntax are emitted as additional HTML attributes.
