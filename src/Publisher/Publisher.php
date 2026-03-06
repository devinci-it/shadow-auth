<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Publisher;

use RuntimeException;

final class Publisher
{
    public static function publishPublic(?string $projectRoot = null, bool $force = false): array
    {
        return self::publishSingleDirectory('public', 'public', $projectRoot, $force);
    }

    public static function publishEndpoints(?string $projectRoot = null, bool $force = false): array
    {
        return self::publishSingleDirectory('views', 'views', $projectRoot, $force);
    }

    public static function publishDemo(?string $projectRoot = null, bool $force = false): array
    {
        return self::publishVariant('demo', $projectRoot, $force);
    }

    public static function publishWiki(?string $projectRoot = null, bool $force = false): array
    {
        return self::publishVariant('wiki', $projectRoot, $force);
    }

    public static function publishVariant(string $variant, ?string $projectRoot = null, bool $force = false): array
    {
        $projectRoot = $projectRoot !== null && $projectRoot !== '' ? $projectRoot : getcwd();
        if (!is_string($projectRoot) || $projectRoot === '') {
            throw new RuntimeException('Could not determine project root.');
        }

        $projectRoot = rtrim($projectRoot, '/');
        $packageRoot = dirname(__DIR__, 2);
        $targetDirName = match ($variant) {
            'demo' => 'shadow-auth-demo',
            'wiki' => 'shadow-auth-wiki',
            default => throw new RuntimeException('Unknown publish variant: ' . $variant),
        };
        $targetRoot = $projectRoot . '/' . $targetDirName;

        self::ensureDirectory($targetRoot);

        $map = [
            $packageRoot . '/bootstrap.php' => $targetRoot . '/bootstrap.php',
            $packageRoot . '/public' => $targetRoot . '/public',
            $packageRoot . '/views' => $targetRoot . '/views',
            $packageRoot . '/assets/templates/demo.composer.json' => $targetRoot . '/composer.json',
        ];

        $copied = [];
        $skipped = [];

        foreach ($map as $source => $destination) {
            if (!file_exists($source)) {
                $skipped[] = [
                    'source' => $source,
                    'destination' => $destination,
                    'reason' => 'missing_source',
                ];
                continue;
            }

            if (is_dir($source)) {
                self::copyDirectory($source, $destination, $force, $copied, $skipped);
                continue;
            }

            self::copyFile($source, $destination, $force, $copied, $skipped);
        }

        return [
            'variant' => $variant,
            'target_root' => $targetRoot,
            'copied' => $copied,
            'skipped' => $skipped,
        ];
    }

    private static function publishSingleDirectory(
        string $sourceRelativeDir,
        string $targetRelativeDir,
        ?string $projectRoot,
        bool $force
    ): array {
        $projectRoot = $projectRoot !== null && $projectRoot !== '' ? $projectRoot : getcwd();
        if (!is_string($projectRoot) || $projectRoot === '') {
            throw new RuntimeException('Could not determine project root.');
        }

        $projectRoot = rtrim($projectRoot, '/');
        $packageRoot = dirname(__DIR__, 2);
        $sourceDir = $packageRoot . '/' . $sourceRelativeDir;
        $targetDir = $projectRoot . '/' . $targetRelativeDir;

        if (!is_dir($sourceDir)) {
            throw new RuntimeException('Source directory not found: ' . $sourceDir);
        }

        $copied = [];
        $skipped = [];
        self::copyDirectory($sourceDir, $targetDir, $force, $copied, $skipped);

        return [
            'variant' => $targetRelativeDir,
            'target_root' => $targetDir,
            'copied' => $copied,
            'skipped' => $skipped,
        ];
    }

    private static function copyDirectory(string $sourceDir, string $targetDir, bool $force, array &$copied, array &$skipped): void
    {
        self::ensureDirectory($targetDir);

        $entries = scandir($sourceDir);
        if ($entries === false) {
            throw new RuntimeException('Could not read directory: ' . $sourceDir);
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $source = $sourceDir . '/' . $entry;
            $destination = $targetDir . '/' . $entry;

            if (is_dir($source)) {
                self::copyDirectory($source, $destination, $force, $copied, $skipped);
                continue;
            }

            self::copyFile($source, $destination, $force, $copied, $skipped);
        }
    }

    private static function copyFile(string $sourceFile, string $targetFile, bool $force, array &$copied, array &$skipped): void
    {
        self::ensureDirectory(dirname($targetFile));

        if (is_file($targetFile) && !$force) {
            $skipped[] = [
                'source' => $sourceFile,
                'destination' => $targetFile,
                'reason' => 'exists',
            ];
            return;
        }

        if (!copy($sourceFile, $targetFile)) {
            throw new RuntimeException('Could not copy file to: ' . $targetFile);
        }

        $copied[] = [
            'source' => $sourceFile,
            'destination' => $targetFile,
        ];
    }

    private static function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0775, true) && !is_dir($path)) {
            throw new RuntimeException('Could not create directory: ' . $path);
        }
    }
}
