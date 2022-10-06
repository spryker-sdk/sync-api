<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentInfoTransfer;

interface InfoBuilderInterface
{
    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentInfoTransfer
     */
    public function build(array $openApiYamlAsArray): OpenApiDocumentInfoTransfer;
}
