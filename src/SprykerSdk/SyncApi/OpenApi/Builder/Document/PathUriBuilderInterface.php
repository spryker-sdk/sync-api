<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer;

interface PathUriBuilderInterface
{
    /**
     * @param string $uri
     * @param array $pathUrisAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer
     */
    public function build(string $uri, array $pathUrisAsArray): OpenApiDocumentPathUriTransfer;
}
