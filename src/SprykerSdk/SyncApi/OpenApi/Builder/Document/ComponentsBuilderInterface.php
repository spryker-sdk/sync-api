<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentComponentsTransfer;

interface ComponentsBuilderInterface
{
    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentComponentsTransfer
     */
    public function build(array $openApiYamlAsArray): OpenApiDocumentComponentsTransfer;
}
