<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use ArrayObject;

interface ServersBuilderInterface
{
    /**
     * @param array $openApiYamlAsArray
     *
     * @return ArrayObject|\Generated\Shared\Transfer\OpenApiDocumentServerTransfer[]
     */
    public function build(array $openApiYamlAsArray): \ArrayObject;
}
