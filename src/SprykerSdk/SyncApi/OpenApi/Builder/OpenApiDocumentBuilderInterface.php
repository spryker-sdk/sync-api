<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder;

interface OpenApiDocumentBuilderInterface
{

    public function buildOpenApiDocumentFromArray(array $openApiYamlAsArray): OpenApiDocumentTransfer;
}
