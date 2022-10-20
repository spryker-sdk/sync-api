<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\PathItem;
use cebe\openapi\Writer;
use SprykerSdk\SyncApi\OpenApi\Merger\Exception\ParameterNotFoundInSourceOpenApiException;
use SprykerSdk\SyncApi\OpenApi\Merger\Exception\SchemaNotFoundInSourceOpenApiException;
use SprykerSdk\SyncApi\SyncApiConfig;

class PathsMerger implements MergerInterface
{
    use OpenApiAccessorTrait;

    /**
     * @var string
     */
    protected const YAML_EXTENSION_PATTERN = '/(\.yaml|\.yml)/';

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected SyncApiConfig $syncApiConfig;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Merger\ComponentsCleanerInterface
     */
    protected ComponentsCleanerInterface $componentsCleaner;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $syncApiConfig
     * @param \SprykerSdk\SyncApi\OpenApi\Merger\ComponentsCleanerInterface $componentsCleaner
     */
    public function __construct(SyncApiConfig $syncApiConfig, ComponentsCleanerInterface $componentsCleaner)
    {
        $this->syncApiConfig = $syncApiConfig;
        $this->componentsCleaner = $componentsCleaner;
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
            if (!$this->getPaths($targetOpenApi)->hasPath($pathName)) {
                $this->getPaths($targetOpenApi)->addPath($pathName, $sourcePathItem);

                $targetOpenApi = $this->addReferencesFromPathItem($targetOpenApi, $sourceOpenApi, $pathName);

                continue;
            }

            $this->mergePath($targetOpenApi, $sourceOpenApi, $pathName, $sourcePathItem);
        }

        return $this->componentsCleaner->cleanUnused($targetOpenApi);
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $pathName
     * @param \cebe\openapi\spec\PathItem $sourcePathItem
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function mergePath(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi,
        string $pathName,
        PathItem $sourcePathItem
    ): OpenApi {
        $targetPathItem = $this->getPaths($targetOpenApi)->getPath($pathName);

        foreach ($this->syncApiConfig->getAvailableHttpMethods() as $httpMethod) {
            if ($sourcePathItem->$httpMethod) {
                $targetPathItem->$httpMethod = $sourcePathItem->$httpMethod;

                $targetOpenApi = $this->addReferencesFromOperation($targetOpenApi, $sourceOpenApi, $pathName, $httpMethod);
            }
        }

        return $targetOpenApi;
    }

    /**
     * Copies schemas by references from source OpenApi Path Item to target OpenApi
     *
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $pathName
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addReferencesFromPathItem(OpenApi $targetOpenApi, OpenApi $sourceOpenApi, string $pathName): OpenApi
    {
        foreach ($this->syncApiConfig->getAvailableHttpMethods() as $httpMethod) {
            $this->addReferencesFromOperation($targetOpenApi, $sourceOpenApi, $pathName, $httpMethod);
        }

        return $targetOpenApi;
    }

    /**
     * Copies schemas by references from source OpenApi Operation Item to target OpenApi
     *
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $pathName
     * @param string $httpMethod
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addReferencesFromOperation(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi,
        string $pathName,
        string $httpMethod
    ): OpenApi {
        $sourceOpenApiAsArray = json_decode(Writer::writeToJson($sourceOpenApi), true);

        if (!isset($sourceOpenApiAsArray['paths'][$pathName][$httpMethod])) {
            return $targetOpenApi;
        }

        $references = ReferenceFinder::findInArray($sourceOpenApiAsArray['paths'][$pathName][$httpMethod]);

        foreach ($references as $reference) {
            $this->addInternalReference($targetOpenApi, $sourceOpenApi, $reference);
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
    protected function addReferencesFromParameter(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi,
        string $parameterName
    ): OpenApi {
        $sourceOpenApiAsArray = json_decode(Writer::writeToJson($sourceOpenApi), true);

        $references = ReferenceFinder::findInArray($sourceOpenApiAsArray['components']['parameters'][$parameterName]);

        foreach ($references as $reference) {
            $this->addInternalReference($targetOpenApi, $sourceOpenApi, $reference);
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
    protected function addReferencesFromSchema(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi,
        string $schemaName
    ): OpenApi {
        $sourceOpenApiAsArray = json_decode(Writer::writeToJson($sourceOpenApi), true);

        $references = ReferenceFinder::findInArray($sourceOpenApiAsArray['components']['schemas'][$schemaName]);

        foreach ($references as $reference) {
            $this->addInternalReference($targetOpenApi, $sourceOpenApi, $reference);
        }

        return $targetOpenApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     * @param string $reference
     *
     * @return void
     */
    protected function addInternalReference(OpenApi $targetOpenApi, OpenApi $sourceOpenApi, string $reference): void
    {
        if ($this->isExternalReference($reference)) {
            return;
        }

        if ($this->isParameter($reference)) {
            $this->addInternalParameter($targetOpenApi, $sourceOpenApi, $reference);
        }

        if ($this->isSchema($reference)) {
            $this->addInternalSchema($targetOpenApi, $sourceOpenApi, $reference);
        }
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

        $parameterItem = $this->getParameterByReference($sourceOpenApi, $parameterName);

        $this->addParameter($targetOpenApi, $parameterName, $parameterItem);

        return $this->addReferencesFromParameter($targetOpenApi, $sourceOpenApi, $parameterName);
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
        $schemaItem = $this->getSchemaByReference($sourceOpenApi, $schemaName);

        $this->addSchema($targetOpenApi, $schemaName, $schemaItem);

        return $this->addReferencesFromSchema($targetOpenApi, $sourceOpenApi, $schemaName);
    }

    /**
     * @param string $reference
     *
     * @return bool
     */
    protected function isParameter(string $reference): bool
    {
        return strpos($reference, '/parameters/') !== false;
    }

    /**
     * @param string $reference
     *
     * @return bool
     */
    protected function isSchema(string $reference): bool
    {
        return strpos($reference, '/schemas/') !== false;
    }

    /**
     * @param string $reference
     *
     * @return string
     */
    protected function getObjectName(string $reference): string
    {
        $referenceParts = explode(DIRECTORY_SEPARATOR, $reference);

        return end($referenceParts);
    }

    /**
     * @param string $reference
     *
     * @return bool
     */
    protected function isExternalReference(string $reference): bool
    {
        return (bool)preg_match(static::YAML_EXTENSION_PATTERN, $reference);
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param string $parameterName
     *
     * @throws \SprykerSdk\SyncApi\OpenApi\Merger\Exception\ParameterNotFoundInSourceOpenApiException
     *
     * @return \cebe\openapi\spec\Parameter|\cebe\openapi\spec\Reference
     */
    protected function getParameterByReference(OpenApi $openApi, string $parameterName)
    {
        foreach ($this->getParameters($openApi) as $currentParameterName => $parameterItem) {
            if ($currentParameterName === $parameterName) {
                return $parameterItem;
            }
        }

        throw new ParameterNotFoundInSourceOpenApiException(
            $this->createParameterIsNotFoundMessage($parameterName),
        );
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param string $schemaName
     *
     * @throws \SprykerSdk\SyncApi\OpenApi\Merger\Exception\SchemaNotFoundInSourceOpenApiException
     *
     * @return \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference
     */
    protected function getSchemaByReference(OpenApi $openApi, string $schemaName)
    {
        foreach ($this->getSchemas($openApi) as $currentSchemaName => $schema) {
            if ($currentSchemaName === $schemaName) {
                return $schema;
            }
        }

        throw new SchemaNotFoundInSourceOpenApiException($this->createSchemaIsNotFoundMessage($schemaName));
    }

    /**
     * @param string $parameterName
     *
     * @return string
     */
    protected function createParameterIsNotFoundMessage(string $parameterName): string
    {
        return sprintf('Parameter "%s" is not found in given Open API', $parameterName);
    }

    /**
     * @param string $schemaName
     *
     * @return string
     */
    protected function createSchemaIsNotFoundMessage(string $schemaName): string
    {
        return sprintf('Schema "%s" is not found in given Open API', $schemaName);
    }
}
