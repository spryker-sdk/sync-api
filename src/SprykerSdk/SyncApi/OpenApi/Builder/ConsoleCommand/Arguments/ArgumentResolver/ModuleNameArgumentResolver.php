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
     * @param string $applicationType
     *
     * @throws \SprykerSdk\SyncApi\Exception\SyncApiModuleNameNotFoundException
     *
     * @return string
     */
    public function resolve(string $resource, PathItem $pathItem, Operation $operation, string $applicationType): string
    {
        $pathExtensions = $pathItem->getExtensions();
        $operationExtensions = $operation->getExtensions();

        $extensions = array_replace_recursive($pathExtensions, $operationExtensions);

        if (isset($extensions['x-spryker']) && isset($extensions['x-spryker']['module'])) {
            return $this->toModuleName($extensions['x-spryker']['module'], $applicationType);
        }

        $path = trim($resource, '/');

        if ($path === '') {
            throw new SyncApiModuleNameNotFoundException('Could not resolve a module name to render the code to.');
        }

        return $this->toModuleName($path, $applicationType);
    }

    /**
     * @param string $moduleNameCandidate
     * @param string $applicationType
     *
     * @return string
     */
    protected function toModuleName(string $moduleNameCandidate, string $applicationType): string
    {
        $pathFragments = explode('/', trim($moduleNameCandidate, '/'));
        $moduleNameName = current($pathFragments);

        $moduleNameName = str_replace(['-', '_'], ' ', $moduleNameName);
        $moduleNameName = ucwords($moduleNameName);
        $moduleNameName = implode('', explode(' ', $moduleNameName));

        return $this->ensureApplicationTypeModuleName($moduleNameName, $applicationType);
    }

    /**
     * @param string $moduleName
     * @param string $applicationType
     *
     * @return string
     */
    protected function ensureApplicationTypeModuleName(string $moduleName, string $applicationType): string
    {
        if ($applicationType === 'Backend' || $applicationType === 'backend') {
            return $this->ensureBackendApiModuleName($moduleName);
        }

        return $this->ensureStorefrontApiModuleName($moduleName);
    }

    /**
     * @param string $moduleName
     *
     * @return string
     */
    protected function ensureBackendApiModuleName(string $moduleName): string
    {
        if (!preg_match('/BackendApi$/', $moduleName)) {
            return $moduleName . 'BackendApi';
        }

        return $moduleName;
    }

    /**
     * @param string $moduleName
     *
     * @return string
     */
    protected function ensureStorefrontApiModuleName(string $moduleName): string
    {
        if (!preg_match('/StorefrontApi$/', $moduleName)) {
            return $moduleName . 'StorefrontApi';
        }

        return $moduleName;
    }
}
