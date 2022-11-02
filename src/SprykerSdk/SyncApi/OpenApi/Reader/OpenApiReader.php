<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Reader;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use SprykerSdk\SyncApi\Exception\OpenApiFileReadException;

class OpenApiReader implements OpenApiReaderInterface
{
 /**
  * @var string
  */
    protected const FILE_EXTENSION_YAML = 'yaml';

    /**
     * @var string
     */
    protected const FILE_EXTENSION_YML = 'yml';

    /**
     * @var string
     */
    protected const FILE_EXTENSION_JSON = 'json';

    /**
     * @param string $openApiDoc
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function readOpenApiFromJsonString(string $openApiDoc): OpenApi
    {
        return Reader::readFromJson($openApiDoc);
    }

    /**
     * @param string $filePath
     *
     * @throws \SprykerSdk\SyncApi\Exception\OpenApiFileReadException
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function readOpenApiFromFile(string $filePath): OpenApi
    {
        $fileExtension = $this->getFileExtension($filePath);

        if ($fileExtension === static::FILE_EXTENSION_JSON) {
            return Reader::readFromJsonFile($filePath);
        }

        if ($fileExtension === static::FILE_EXTENSION_YAML || $fileExtension === static::FILE_EXTENSION_YML) {
            return Reader::readFromYamlFile($filePath);
        }

        throw new OpenApiFileReadException('Unsupported OpenAPI file format');
    }

    /**
     * @param string $filePath
     *
     * @return string
     */
    protected function getFileExtension(string $filePath): string
    {
        $parts = explode('.', basename($filePath));

        return array_pop($parts);
    }
}
