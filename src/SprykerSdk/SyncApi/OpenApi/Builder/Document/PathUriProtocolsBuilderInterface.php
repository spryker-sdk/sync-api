<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer;

interface PathUriProtocolsBuilderInterface
{
    /**
     * @param string $protocol
     * @param array $pathUriProtocolArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer
     */
    public function build(string $protocol, array $pathUriProtocolArray): OpenApiDocumentPathUriProtocolTransfer;
}
