<?php

namespace SprykerSdk\SyncApi\OpenApi\Converter;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

interface ComponentsToArrayConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $openApiDocumentTransfer
     *
     * @return array
     */
    public function convert(OpenApiDocumentTransfer $openApiDocumentTransfer): array;
}
