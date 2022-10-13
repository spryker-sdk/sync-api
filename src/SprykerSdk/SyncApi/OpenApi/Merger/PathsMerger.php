<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\Components;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Paths;
use cebe\openapi\Writer;
use SprykerSdk\SyncApi\SyncApiConfig;

class PathsMerger implements MergerInterface
{
    /**
     * @var string
     */
    protected const YML_EXTENSION = '.yml';

    /**
     * @var string
     */
    protected const YAML_EXTENSION = '.yaml';

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected $syncApiConfig;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $syncApiConfig
     */
    public function __construct(SyncApiConfig $syncApiConfig)
    {
        $this->syncApiConfig = $syncApiConfig;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function merge(OpenApi $targetOpenApi, OpenApi $sourceOpenApi): OpenApi
    {
        foreach ($sourceOpenApi->paths as $pathName => $sourcePathItem) {
            if ($targetOpenApi->paths === null) {
                $targetOpenApi->paths = new Paths([]);
            }

            if (!$targetOpenApi->paths->hasPath($pathName)) {
                $targetOpenApi->paths->addPath($pathName, $sourcePathItem);

                $targetOpenApi = $this->addRefsFromPathItem($targetOpenApi, $sourceOpenApi, $pathName);

                continue;
            }

            $targetPathItem = $targetOpenApi->paths->getPath($pathName);

            foreach ($this->syncApiConfig->getAvailableHttpMethods() as $httpMethod) {
                if ($sourcePathItem->$httpMethod) {
                    $targetPathItem->$httpMethod = $sourcePathItem->$httpMethod;

                    $targetOpenApi = $this->addRefsFromOperation($targetOpenApi, $sourceOpenApi, $pathName, $httpMethod);
                }
            }

            $targetOpenApi = $this->removeUnusedComponents($targetOpenApi);
        }

        return $targetOpenApi;
    }

    /**
     * @param string $ref
     *
     * @return bool
     */
    protected function isParameter(string $ref): bool
    {
        return strpos($ref, '/parameters/') !== false;
    }

    /**
     * @param string $ref
     *
     * @return bool
     */
    protected function isSchema(string $ref): bool
    {
        return strpos($ref, '/schemas/') !== false;
    }

    /**
     * @param string $ref
     *
     * @return string
     */
    protected function getObjectName(string $ref): string
    {
        $refParts = explode('/', $ref);

        return end($refParts);
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $pathName
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addRefsFromPathItem(OpenApi $targetOpenApi, OpenApi $sourceOpenApi, string $pathName): OpenApi
    {
        foreach ($this->syncApiConfig->getAvailableHttpMethods() as $httpMethod) {
            $this->addRefsFromOperation($targetOpenApi, $sourceOpenApi, $pathName, $httpMethod);
        }

        return $targetOpenApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $pathName
     * @param string $httpMethod
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addRefsFromOperation(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi,
        string $pathName,
        string $httpMethod
    ): OpenApi {
        $sourceOpenApiAsArray = json_decode(Writer::writeToJson($sourceOpenApi), true);

        if (!isset($sourceOpenApiAsArray['paths'][$pathName][$httpMethod])) {
            return $targetOpenApi;
        }

        $operationAsArray = $sourceOpenApiAsArray['paths'][$pathName][$httpMethod];

        $refs = array_unique($this->getRefsFromArray($operationAsArray, []));

        foreach ($refs as $ref) {
            $this->addInternalReference($targetOpenApi, $sourceOpenApi, $ref);
        }

        return $targetOpenApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $parameterName
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addRefsFromParameter(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi,
        string $parameterName
    ): OpenApi {
        $sourceOpenApiAsArray = json_decode(Writer::writeToJson($sourceOpenApi), true);

        $parameterAsArray = $sourceOpenApiAsArray['components']['parameters'][$parameterName];

        if (!$parameterAsArray) {
            return $targetOpenApi;
        }

        $refs = array_unique($this->getRefsFromArray($parameterAsArray, []));

        foreach ($refs as $ref) {
            $this->addInternalReference($targetOpenApi, $sourceOpenApi, $ref);
        }

        return $targetOpenApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $schemaName
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addRefsFromSchema(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi,
        string $schemaName
    ): OpenApi {
        $sourceOpenApiAsArray = json_decode(Writer::writeToJson($sourceOpenApi), true);

        $schemaAsArray = $sourceOpenApiAsArray['components']['schemas'][$schemaName];

        if (!$schemaAsArray) {
            return $targetOpenApi;
        }

        $refs = array_unique($this->getRefsFromArray($schemaAsArray, []));

        foreach ($refs as $ref) {
            $this->addInternalReference($targetOpenApi, $sourceOpenApi, $ref);
        }

        return $targetOpenApi;
    }

    /**
     * @param array $array
     * @param array $refs
     *
     * @return array
     */
    protected function getRefsFromArray(array $array, array $refs): array
    {
        foreach ($array as $key => $value) {
            if ($key === '$ref') {
                $refs[] = $value;
            }

            if (is_array($value)) {
                $refs = $this->getRefsFromArray($value, $refs);
            }
        }

        return $refs;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $reference
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addInternalReference(OpenApi $targetOpenApi, OpenApi $sourceOpenApi, string $reference): OpenApi
    {
        if ($this->isExternalReference($reference)) {
            return $targetOpenApi;
        }

        if ($this->isParameter($reference)) {
            return $this->addInternalParameter($targetOpenApi, $sourceOpenApi, $reference);
        }

        if ($this->isSchema($reference)) {
            return $this->addInternalSchema($targetOpenApi, $sourceOpenApi, $reference);
        }

        return $targetOpenApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $reference
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addInternalParameter(OpenApi $targetOpenApi, OpenApi $sourceOpenApi, string $reference): OpenApi
    {
        $parameterName = $this->getObjectName($reference);

        $parameterItem = $this->findParameterByReference($sourceOpenApi, $parameterName);

        if ($parameterItem) {
            if (!$targetOpenApi->components) {
                $targetOpenApi->components = new Components([]);
            }

            if (!$targetOpenApi->components->parameters) {
                $targetOpenApi->components->parameters = [];
            }

            $targetOpenApi->components->parameters = array_merge(
                $targetOpenApi->components->parameters,
                [$parameterName => $parameterItem],
            );

            $targetOpenApi = $this->addRefsFromParameter($targetOpenApi, $sourceOpenApi, $parameterName);
        }

        return $targetOpenApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $reference
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addInternalSchema(OpenApi $targetOpenApi, OpenApi $sourceOpenApi, string $reference): OpenApi
    {
        $schemaName = $this->getObjectName($reference);

        $schemaItem = $this->findSchemaByReference($sourceOpenApi, $schemaName);

        if ($schemaItem) {
            if (!$targetOpenApi->components) {
                $targetOpenApi->components = new Components([]);
            }

            if (!$targetOpenApi->components->schemas) {
                $targetOpenApi->components->schemas = [];
            }

            $targetOpenApi->components->schemas = array_merge(
                $targetOpenApi->components->schemas,
                [$schemaName => $schemaItem],
            );

            $targetOpenApi = $this->addRefsFromSchema($targetOpenApi, $sourceOpenApi, $schemaName);
        }

        return $targetOpenApi;
    }

    /**
     * @param string $reference
     *
     * @return bool
     */
    protected function isExternalReference(string $reference): bool
    {
        return strpos($reference, static::YML_EXTENSION) !== false
            || strpos($reference, static::YAML_EXTENSION) !== false;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param string $parameterName
     *
     * @return \cebe\openapi\spec\Parameter|\cebe\openapi\spec\Reference|null
     */
    protected function findParameterByReference(OpenApi $openApi, string $parameterName)
    {
        foreach ($openApi->components->parameters as $currentParameterName => $parameterItem) {
            if ($currentParameterName === $parameterName) {
                return $parameterItem;
            }
        }

        return null;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param string $schemaName
     *
     * @return \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference|null
     */
    protected function findSchemaByReference(OpenApi $openApi, string $schemaName)
    {
        foreach ($openApi->components->schemas as $currentSchemaName => $schema) {
            if ($currentSchemaName === $schemaName) {
                return $schema;
            }
        }

        return null;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function removeUnusedComponents(OpenApi $openApi): OpenApi
    {
        $openApiAsArray = json_decode(Writer::writeToJson($openApi), true);

        $refs = $this->getRefsFromArray($openApiAsArray, []);

        foreach (array_keys($openApi->components->parameters) as $parameterName) {
            $paramReferenceName = $this->createParameterReferenceName($parameterName);

            if (!in_array($paramReferenceName, $refs)) {
                $openApi->components->parameters
                    = $this->getArrayWithoutKey(
                        $openApi->components->parameters,
                        $parameterName,
                    );
            }
        }

        foreach (array_keys($openApi->components->schemas) as $schemaName) {
            $schemaReferenceName = $this->createSchemaReferenceName($schemaName);

            if (!in_array($schemaReferenceName, $refs)) {
                $openApi->components->schemas
                    = $this->getArrayWithoutKey(
                        $openApi->components->schemas,
                        $schemaName,
                    );
            }
        }

        return $openApi;
    }

    /**
     * @param string $parameterName
     *
     * @return string
     */
    protected function createParameterReferenceName(string $parameterName): string
    {
        return '#/components/parameters/' . $parameterName;
    }

    /**
     * @param string $schemaName
     *
     * @return string
     */
    protected function createSchemaReferenceName(string $schemaName): string
    {
        return '#/components/schemas/' . $schemaName;
    }

    /**
     * @param array $schemas
     * @param string $schemaName
     *
     * @return array
     */
    protected function getArrayWithoutKey(array $schemas, string $schemaName): array
    {
        unset($schemas[$schemaName]);

        return $schemas;
    }
}
