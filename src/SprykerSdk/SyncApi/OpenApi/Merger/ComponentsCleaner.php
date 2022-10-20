<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\Components;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\Writer;

class ComponentsCleaner implements ComponentsCleanerInterface
{
    use OpenApiAccessorTrait;

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function cleanUnused(OpenApi $openApi): OpenApi
    {
        $openApiAsArray = json_decode(Writer::writeToJson($openApi), true);

        $references = ReferenceFinder::findInArray($openApiAsArray);

        $openApi = $this->cleanUnusedParameters($openApi, $references);
        $openApi = $this->cleanUnusedSchemas($openApi, $references);

        return $openApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param array<string> $references
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function cleanUnusedParameters(OpenApi $openApi, array $references): OpenApi
    {
        foreach (array_keys($this->getParameters($openApi)) as $parameterName) {
            $paramReferenceName = $this->createParameterReferenceName($parameterName);

            if (!in_array($paramReferenceName, $references)) {
                $this->getComponents($openApi)->parameters = $this->filterSchemasByKey(
                    $this->getComponents($openApi)->parameters,
                    $parameterName,
                );
            }
        }

        return $this->repackComponents($openApi);
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param array<string> $references
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function cleanUnusedSchemas(OpenApi $openApi, array $references): OpenApi
    {
        foreach (array_keys($this->getSchemas($openApi)) as $schemaName) {
            $schemaReferenceName = $this->createSchemaReferenceName($schemaName);

            if (!in_array($schemaReferenceName, $references)) {
                $this->getComponents($openApi)->schemas = $this->filterSchemasByKey(
                    $this->getComponents($openApi)->schemas,
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
    protected function filterSchemasByKey(array $schemas, string $schemaName): array
    {
        unset($schemas[$schemaName]);

        return $schemas;
    }

    /**
     * This prevents appearing empty keys of components, params and schemas when they are empty
     *
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function repackComponents(OpenApi $openApi): OpenApi
    {
        $parameters = $this->getParameters($openApi);
        $schemas = $this->getSchemas($openApi);

        if (!$parameters && !$schemas) {
            $openApi->components = null;

            return $openApi;
        }

        $openApi->components = new Components([]);

        if ($schemas) {
            $openApi->components->schemas = $schemas;
        }

        if ($parameters) {
            $openApi->components->parameters = $parameters;
        }

        return $openApi;
    }
}
