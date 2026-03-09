<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Providers;

/**
 * Persists users in a PHP file returning an array of user records.
 */
final class FileUserProvider
{
    public function __construct(private readonly string $storagePath)
    {
    }

    /**
     * Ensures storage directory and file exist with restrictive permissions.
     */
    public function initialize(): void
    {
        $directory = dirname($this->storagePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        @chmod($directory, 0700);

        if (!file_exists($this->storagePath)) {
            $oldUmask = umask(0077);
            file_put_contents($this->storagePath, "<?php\n\ndeclare(strict_types=1);\n\nreturn [];\n", LOCK_EX);
            umask($oldUmask);
        }

        @chmod($this->storagePath, 0600);
    }

    public function all(): array
    {
        $this->initialize();

        $data = require $this->storagePath;

        return is_array($data) ? $data : [];
    }

    /**
     * Finds a user by exact username match.
     */
    public function findByUsername(string $username): ?array
    {
        $users = $this->all();

        foreach ($users as $user) {
            if (($user['username'] ?? '') === $username) {
                return $user;
            }
        }

        return null;
    }

    public function findByField(string $field, mixed $value, bool $caseInsensitive = false): ?array
    {
        if ($field === '') {
            return null;
        }

        $users = $this->all();

        foreach ($users as $user) {
            if (!is_array($user) || !array_key_exists($field, $user)) {
                continue;
            }

            $current = $user[$field];
            if ($this->fieldMatches($current, $value, $caseInsensitive)) {
                return $user;
            }
        }

        return null;
    }

    public function existsByField(string $field, mixed $value, bool $caseInsensitive = false): bool
    {
        return $this->findByField($field, $value, $caseInsensitive) !== null;
    }

    public function create(array $user): bool
    {
        $users = $this->all();
        $users[] = $user;

        return $this->write($users);
    }

    public function updateByUsername(string $username, array $newData): bool
    {
        $users = $this->all();

        foreach ($users as $index => $user) {
            if (($user['username'] ?? '') === $username) {
                $users[$index] = array_merge($user, $newData);
                return $this->write($users);
            }
        }

        return false;
    }

    private function write(array $users): bool
    {
        $export = var_export($users, true);
        $content = "<?php\n\ndeclare(strict_types=1);\n\nreturn {$export};\n";

        $written = file_put_contents($this->storagePath, $content, LOCK_EX) !== false;
        if ($written) {
            @chmod($this->storagePath, 0600);
        }

        return $written;
    }

    private function fieldMatches(mixed $left, mixed $right, bool $caseInsensitive): bool
    {
        if ($caseInsensitive) {
            return strtolower((string) $left) === strtolower((string) $right);
        }

        return (string) $left === (string) $right;
    }
}
