<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use SprykerSdk\SyncApi\Exception\SyncApiModuleNameNotFoundException;

class ModuleNameArgumentResolver implements ArgumentResolverInterface
{
    /**
     * @param string $resource
     * @param \cebe\openapi\spec\PathItem $pathItem
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @throws \SprykerSdk\SyncApi\Exception\SyncApiModuleNameNotFoundException
     *
     * @return string
     */
    public function resolve(string $resource, PathItem $pathItem, Operation $operation): string
    {
        $pathExtensions = $pathItem->getExtensions();
        $operationExtensions = $operation->getExtensions();

        $extensions = array_replace_recursive($pathExtensions, $operationExtensions);

        if (isset($extensions['x-spryker']) && isset($extensions['x-spryker']['module'])) {
            return $extensions['x-spryker']['module'];
        }

        // @deprecated it is replaced with x-spryker extension
        if (isset($operation->operationId)) {
            $operationId = explode('.', $operation->operationId);

            return current($operationId);
        }

        $path = trim($resource, '/');

        if ($path === '') {
            throw new SyncApiModuleNameNotFoundException('Could not resolve a module name to render the code to.');
        }

        $pathFragments = explode('/', trim($path, '/'));

        return ucwords(current($pathFragments));
    }
}
