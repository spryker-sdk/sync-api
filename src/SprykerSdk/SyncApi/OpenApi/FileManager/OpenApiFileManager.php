<?php

namespace SprykerSdk\SyncApi\OpenApi\FileManager;

use SprykerSdk\SyncApi\Exception\OpenApiFileReadException;
use Symfony\Component\Yaml\Yaml;

class OpenApiFileManager implements OpenApiFileManagerInterface
{
    /**
     * @param string $filepath
     *
     * @return array
     */
    public function readOpenApiFileAsArray(string $filepath): array
    {
        if (!$this->isCanReadFromFilepath($filepath)) {
            throw new OpenApiFileReadException('Can not read "' . $filepath . '". File is not exists or not readable');
        }

        try {
            return json_decode(json_encode(Yaml::parseFile($filepath, Yaml::PARSE_OBJECT_FOR_MAP)), true);
        } catch (\Throwable $throwable) {
            throw new OpenApiFileReadException($throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    /**
     * @param string $filepath
     * @param array $openApiContents
     *
     * @return bool
     */
    public function saveOpenApiFileFromArray(string $filepath, array $openApiContents): bool
    {
        $openApiSchemaYaml = Yaml::dump($openApiContents, 100);

        $dirname = dirname($filepath);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0770, true);
        }

        return (bool)file_put_contents($filepath, $openApiSchemaYaml);
    }

    /**
     * @param string $filepath
     *
     * @return bool
     */
    protected function isCanReadFromFilepath(string $filepath): bool
    {
        return is_file($filepath) && is_readable($filepath);
    }
}
