<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

class SyncApiConfig
{
    /**
     * @return string
     * @api
     *
     */
    public function getDefaultRelativePathToOpenApiFile(): string
    {
        $pathFragments = [
            'resources',
            'api',
            'openapi.yml',
        ];

        return implode(DIRECTORY_SEPARATOR, $pathFragments);
    }

    /**
     * @return string
     * @api
     *
     */
    public function getProjectRootPath(): string
    {
        return (string)getcwd();
    }

    /**
     * Returns the current working directory or `INSTALLED_ROOT_DIRECTORY` (when INSTALLED_ROOT_DIRECTORY is defined).
     * This is needed to be able to execute this tool within the SprykerSdk and not inside of a project directly.
     *
     * @return string
     */
    public function getSprykRunExecutablePath(): string
    {
        if (getenv('INSTALLED_ROOT_DIRECTORY')) {
            return getenv('INSTALLED_ROOT_DIRECTORY');
        }

        return (string)getcwd();
    }

    /**
     * @return array
     */
    public function getAvailableHttpMethods(): array
    {
        return [
            'get',
            'post',
            'put',
            'patch',
            'delete',
        ];
    }

    /**
     * @return string
     */
    public function getPackageRootDirPath(): string
    {
        return SRYKER_SYNCAPI_PACKAGE_ROOT_DIR;
    }
}
