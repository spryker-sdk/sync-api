<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder;

class FilepathBuilder implements FilepathBuilderInterface
{
    /**
     * @var string
     */
    protected const YAML_EXTENSION_PATTERN = '/(\.yaml|\.yml)/';

    /**
     * @var string
     */
    protected const YAML_EXTENSION = 'yml';

    /**
     * @var string
     */
    protected const FILENAME_PATTERN = '%s.%s';

    /**
     * @param string $filename
     * @param string $rootDirectoryPath
     *
     * @return string
     */
    public function buildSyncApiFilepath(string $filename, string $rootDirectoryPath): string
    {
        return rtrim($rootDirectoryPath, DIRECTORY_SEPARATOR) . '/' . $this->prepareFilename($filename);
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function prepareFilename(string $filename): string
    {
        $filename = trim($filename, DIRECTORY_SEPARATOR);
        $filename = preg_replace(static::YAML_EXTENSION_PATTERN, '', $filename);

        return sprintf(static::FILENAME_PATTERN, $filename, static::YAML_EXTENSION);
    }
}
