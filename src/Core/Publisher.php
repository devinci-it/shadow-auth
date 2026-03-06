<?php

declare(strict_types=1);

namespace Devinci\ShadowAuth\Core;

use Devinci\ShadowAuth\Publisher\Publisher as NamespacePublisher;

final class Publisher
{
    public static function publishDemo(?string $projectRoot = null, bool $force = false): array
    {
        return NamespacePublisher::publishDemo($projectRoot, $force);
    }

    public static function publishWiki(?string $projectRoot = null, bool $force = false): array
    {
        return NamespacePublisher::publishWiki($projectRoot, $force);
    }
}
