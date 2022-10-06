<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentParameterTransfer;

class ParameterBuilder implements ParameterBuilderInterface
{
    /**
     * @param array $parameterAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentParameterTransfer
     */
    public function build(array $parameterAsArray): OpenApiDocumentParameterTransfer
    {
        return (new OpenApiDocumentParameterTransfer())
            ->setName(array_keys($parameterAsArray)[0])
            ->setContents($parameterAsArray[array_keys($parameterAsArray)[0]]);
    }
}
