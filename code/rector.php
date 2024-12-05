<?php

declare(strict_types=1);

use App\Rector\NamedArgumentConstructorRector;
use App\Rector\NamedArgumentInvokeRector;
use App\Rector\NamedArgumentMethodCallRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
    ]);
    $rectorConfig->disableParallel();

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::NAMING,
        SetList::TYPE_DECLARATION,
    ]);

    $rectorConfig->rule(NamedArgumentConstructorRector::class);
    $rectorConfig->rule(NamedArgumentInvokeRector::class);
    $rectorConfig->rule(NamedArgumentMethodCallRector::class);

    $rectorConfig->importNames();
};
