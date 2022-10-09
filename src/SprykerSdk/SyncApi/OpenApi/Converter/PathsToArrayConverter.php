<?php

namespace SprykerSdk\SyncApi\OpenApi\Converter;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class PathsToArrayConverter implements PathsToArrayConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $openApiDocumentTransfer
     *
     * @return array
     */
    public function convert(OpenApiDocumentTransfer $openApiDocumentTransfer): array
    {
        $paths = [];

        foreach ($openApiDocumentTransfer->getPaths()->getPathUris() as $pathUriTransfer) {
            foreach ($pathUriTransfer->getProtocols() as $pathUriProtocolTransfer) {
                $paths[$pathUriTransfer->getUri()][$pathUriProtocolTransfer->getProtocol()] = $pathUriProtocolTransfer->getContents();
            }
        }

        return $paths;
    }
}
