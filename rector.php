<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\EarlyReturn\Rector\If_\ChangeAndIfToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfReturnToEarlyReturnRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryAndToEarlyReturnRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Set\ValueObject\SetList;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;

defined('APPLICATION_ROOT_DIR') || define('APPLICATION_ROOT_DIR', __DIR__);

return static function (RectorConfig $rectorConfig) {
    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(SetList::EARLY_RETURN);
    $rectorConfig->import(SetList::PHP_82);

    $rectorConfig->skip([
        RemoveUselessParamTagRector::class,
        RemoveUnusedPromotedPropertyRector::class,
        RemoveUselessReturnTagRector::class,
        RemoveUselessVarTagRector::class,
    ]);
};
