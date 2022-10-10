<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentParameterTransfer;

interface ParameterBuilderInterface
{
    /**
     * @param string $parameterName
     * @param array $parameterAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentParameterTransfer
     */
    public function build(string $parameterName, array $parameterAsArray): OpenApiDocumentParameterTransfer;
}
