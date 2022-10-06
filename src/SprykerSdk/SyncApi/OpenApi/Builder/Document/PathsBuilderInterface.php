<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentPathsTransfer;

interface PathsBuilderInterface
{
    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathsTransfer
     */
    public function build(array $openApiYamlAsArray): OpenApiDocumentPathsTransfer;
}
