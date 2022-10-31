<?php

namespace SprykerSdk\SyncApi\OpenApi\Reader;

use cebe\openapi\spec\OpenApi;

interface OpenApiReaderInterface
{
    /**
     * @param string $openApiDoc
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function readOpenApiFromJsonString(string $openApiDoc): OpenApi;

    /**
     * @param string $filePath
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function readOpenApiFromFile(string $filePath): OpenApi;
}
