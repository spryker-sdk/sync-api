<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;

class OpenApiMerger implements MergerInterface
{
    /**
     * @var array<\SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface>
     */
    protected array $mergerCollection;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Merger\ComponentsCleanerInterface
     */
    protected ComponentsCleanerInterface $componentsCleaner;

    /**
     * @param array<\SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface> $mergerCollection
     * @param \SprykerSdk\SyncApi\OpenApi\Merger\ComponentsCleanerInterface $componentsCleaner
     */
    public function __construct(
        array $mergerCollection,
        ComponentsCleanerInterface $componentsCleaner
    ) {
        $this->mergerCollection = $mergerCollection;
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
        foreach ($this->mergerCollection as $merger) {
            $targetOpenApi = $merger->merge($targetOpenApi, $sourceOpenApi);
        }

        return $this->componentsCleaner->cleanUnused($targetOpenApi);
    }
}
