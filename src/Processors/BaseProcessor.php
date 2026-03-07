<?php

declare(strict_types=1);

namespace DevinciIT\ShadowAuth\Processors;

abstract class BaseProcessor
{
    abstract public function handle(): void;

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
