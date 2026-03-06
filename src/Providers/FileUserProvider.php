<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Providers;

final class FileUserProvider
{
    public function __construct(private readonly string $storagePath)
    {
    }

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
}
