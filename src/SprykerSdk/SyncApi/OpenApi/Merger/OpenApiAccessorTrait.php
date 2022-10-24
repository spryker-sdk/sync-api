<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\Components;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Paths;

trait OpenApiAccessorTrait
{
    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return \cebe\openapi\spec\Paths
     */
    protected function getPaths(OpenApi $openApi): Paths
    {
        if ($openApi->paths === null) {
            $openApi->paths = new Paths([]);
        }

        return $openApi->paths;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return \cebe\openapi\spec\Components
     */
    protected function getComponents(OpenApi $openApi): Components
    {
        if ($openApi->components === null) {
            $openApi->components = new Components([]);
        }

        return $openApi->components;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return array<\cebe\openapi\spec\Parameter|\cebe\openapi\spec\Reference>
     */
    protected function getParameters(OpenApi $openApi): array
    {
        if (!$this->getComponents($openApi)->parameters) {
            $this->getComponents($openApi)->parameters = [];
        }

        return $this->getComponents($openApi)->parameters;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return array<\cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference>
     */
    protected function getSchemas(OpenApi $openApi): array
    {
        if (!$this->getComponents($openApi)->schemas) {
            $this->getComponents($openApi)->schemas = [];
        }

        return $this->getComponents($openApi)->schemas;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param string $parameterName
     * @param \cebe\openapi\spec\Parameter|\cebe\openapi\spec\Reference $parameter
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addParameter(OpenApi $openApi, string $parameterName, $parameter): OpenApi
    {
        $parameters = $this->getParameters($openApi);

        $parameters = array_merge($parameters, [$parameterName => $parameter]);

        $this->getComponents($openApi)->parameters = $parameters;

        return $openApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param string $schemaName
     * @param \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference $schema
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function addSchema(OpenApi $openApi, string $schemaName, $schema): OpenApi
    {
        $schemas = $this->getSchemas($openApi);

        $schemas = array_merge($schemas, [$schemaName => $schema]);

        $this->getComponents($openApi)->schemas = $schemas;

        return $openApi;
    }
}
