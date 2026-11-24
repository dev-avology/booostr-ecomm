<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    // 1. Directories to analyze
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
    ]);

    // 2. Directories and paths to skip
    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
        __DIR__ . '/bootstrap/cache',
    ]);

    // 3. Apply pure PHP version modernization rules directly targeting PHP 8.5
    // Note: Opinionated CODE_QUALITY sets are strictly omitted so empty(), compact(), and conditional flows remain 100% untouched
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_85,
    ]);
};
