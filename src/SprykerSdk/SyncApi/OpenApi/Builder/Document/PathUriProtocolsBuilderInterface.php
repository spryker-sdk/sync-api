<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

interface PathUriProtocolsBuilderInterface
{
    /**
     * @param array $pathUriProtocolArray
     *
     * @return array<\Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer>
     */
    public function build(array $pathUriProtocolArray): array;
}
