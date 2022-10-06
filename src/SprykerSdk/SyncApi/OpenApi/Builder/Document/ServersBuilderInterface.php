<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

interface ServersBuilderInterface
{
    /**
     * @param array $openApiYamlAsArray
     *
     * @return array<\Generated\Shared\Transfer\OpenApiDocumentServerTransfer>
     */
    public function build(array $openApiYamlAsArray): array;
}
