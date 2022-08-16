<?php

namespace SprykerSdk\SyncApi\OpenApi\FileManager;

interface OpenApiFileManagerInterface
{
    /**
     * @param string $filepath
     *
     * @return array
     */
    public function readOpenApiFileAsArray(string $filepath): array;

    /**
     * @param string $filepath
     * @param array $openApiContents
     *
     * @return bool
     */
    public function saveOpenApiFileFromArray(string $filepath, array $openApiContents): bool;
}
