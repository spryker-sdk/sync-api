<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

interface DocumentBuilderInterface
{

    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentTransfer
     */
    public function buildOpenApiDocumentFromArray(array $openApiYamlAsArray): OpenApiDocumentTransfer;
}
