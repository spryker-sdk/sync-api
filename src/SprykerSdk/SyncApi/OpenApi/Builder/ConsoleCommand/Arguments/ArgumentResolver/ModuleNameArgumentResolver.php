<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use SprykerSdk\SyncApi\Exception\SyncApiModuleNameNotFoundException;

class ModuleNameArgumentResolver implements ArgumentResolverInterface
{
    /**
     * @param string $resource
     * @param PathItem $pathItem
     * @param Operation $operation
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
