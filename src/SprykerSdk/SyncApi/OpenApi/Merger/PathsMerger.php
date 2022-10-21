<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\PathItem;
use SprykerSdk\SyncApi\SyncApiConfig;

class PathsMerger implements MergerInterface
{
    use OpenApiAccessorTrait;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    private SyncApiConfig $syncApiConfig;

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
            if (!$this->getPaths($targetOpenApi)->hasPath($pathName)) {
                $this->getPaths($targetOpenApi)->addPath($pathName, $sourcePathItem);

                continue;
            }

            $this->mergePath($targetOpenApi, $pathName, $sourcePathItem);
        }

        return $targetOpenApi;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param string $pathName
     * @param \cebe\openapi\spec\PathItem $sourcePathItem
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    private function mergePath(
        OpenApi $targetOpenApi,
        string $pathName,
        PathItem $sourcePathItem
    ): OpenApi {
        $targetPathItem = $this->getPaths($targetOpenApi)->getPath($pathName);

        foreach ($this->syncApiConfig->getAvailableHttpMethods() as $httpMethod) {
            if ($sourcePathItem->$httpMethod) {
                $targetPathItem->$httpMethod = $sourcePathItem->$httpMethod;
            }
        }

        return $targetOpenApi;
    }
}
