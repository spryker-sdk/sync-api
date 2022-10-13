<?php

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;

class InfoMerger implements MergerInterface
{
    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function merge(OpenApi $targetOpenApi, OpenApi $sourceOpenApi): OpenApi
    {
        if ($sourceOpenApi->info) {
            $targetOpenApi->info = $sourceOpenApi->info;
        }

        return $targetOpenApi;
    }
}
