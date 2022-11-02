<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;

class ComponentMerger implements MergerInterface
{
    use OpenApiAccessorTrait;

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function merge(OpenApi $targetOpenApi, OpenApi $sourceOpenApi): OpenApi
    {
        foreach ($this->getParameters($sourceOpenApi) as $parameterName => $parameter) {
            $this->addParameter($targetOpenApi, $parameterName, $parameter);
        }

        foreach ($this->getSchemas($sourceOpenApi) as $schemaName => $schema) {
            $this->addSchema($targetOpenApi, $schemaName, $schema);
        }

        return $targetOpenApi;
    }
}
