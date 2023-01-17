<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;

interface ArgumentResolverInterface
{
    /**
     * @param string $resource
     * @param \cebe\openapi\spec\PathItem $pathItem
     * @param \cebe\openapi\spec\Operation $operation
     * @param string $applicationType
     *
     * @return string
     */
    public function resolve(string $resource, PathItem $pathItem, Operation $operation, string $applicationType): string;
}
