<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

class SyncApiConfig
{
    /**
     * @var string
     */
    public const STRATEGY_REPLACE = 'replace';

    /**
     * @var string
     */
    public const STRATEGY_REPLACE_RECURSIVE = 'replaceRecursive';

    /**
     * @var string
     */
    public const STRATEGY_SERVERS_MERGE = 'serversMerge';

    /**
     * @var string
     */
    public const STRATEGY_PATHS_MERGE = 'pathMerge';

    /**
     * @var array<string>
     */
    protected const FIELDS_MERGE_STRATEGY_MAP = [
        'version' => self::STRATEGY_REPLACE,
        'info' => self::STRATEGY_REPLACE_RECURSIVE,
        'servers' => self::STRATEGY_SERVERS_MERGE,
        'paths' => self::STRATEGY_PATHS_MERGE,
    ];

    /**
     * @api
     *
     * @return string
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
     * @api
     *
     * @return string
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
     * @return string
     */
    public function getPackageRootDirPath(): string
    {
        return SRYKER_SYNCAPI_PACKAGE_ROOT_DIR;
    }

    /**
     * @return string
     */
    public function getDefaultAbsolutePathToOpenApiFile(): string
    {
        return implode(
            DIRECTORY_SEPARATOR,
            [
                $this->getPackageRootDirPath(),
                $this->getDefaultRelativePathToOpenApiFile()
            ]
        );
    }

    /**
     * @return string
     */
    public function getSyncApiDirPath(): string
    {
        $pathFragments = [
            'resources',
            'api',
            'syncapi',
        ];

        return implode(DIRECTORY_SEPARATOR, $pathFragments);
    }

    /**
     * @return array<string>
     */
    public function getFieldsMergeStrategyMap(): array
    {
        return static::FIELDS_MERGE_STRATEGY_MAP;
    }
}
