<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Core;

use Devinci\ShadowAuth\Providers\FileUserProvider;

final class RegistrationConstraintPolicy
{
    private array $uniqueFields;
    private array $caseInsensitiveFields;

    public function __construct(
        private readonly FileUserProvider $provider,
        array $constraints = []
    ) {
        $this->uniqueFields = $this->normalizeFields($constraints['unique_fields'] ?? ['username']);
        $this->caseInsensitiveFields = $this->normalizeFields($constraints['case_insensitive_fields'] ?? ['username', 'email']);
    }

    public function violationMessageFor(array $attributes): ?string
    {
        foreach ($this->uniqueFields as $field) {
            if (!array_key_exists($field, $attributes)) {
                continue;
            }

            $value = trim((string) $attributes[$field]);
            if ($value === '') {
                continue;
            }

            $exists = $this->provider->existsByField(
                $field,
                $value,
                in_array($field, $this->caseInsensitiveFields, true)
            );

            if ($exists) {
                return sprintf('%s is already in use.', $this->humanizeField($field));
            }
        }

        return null;
    }

    private function normalizeFields(mixed $fields): array
    {
        if (!is_array($fields)) {
            return [];
        }

        $normalized = [];

        foreach ($fields as $field) {
            if (!is_string($field)) {
                continue;
            }

            $name = trim(strtolower($field));
            if ($name === '' || !preg_match('/^[a-z_][a-z0-9_]*$/', $name)) {
                continue;
            }

            if (!in_array($name, $normalized, true)) {
                $normalized[] = $name;
            }
        }

        return $normalized;
    }

    private function humanizeField(string $field): string
    {
        return ucfirst(str_replace('_', ' ', $field));
    }
}
