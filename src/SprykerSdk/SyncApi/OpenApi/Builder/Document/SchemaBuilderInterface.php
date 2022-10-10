<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer;

interface SchemaBuilderInterface
{
    /**
     * @param string $schemaName
     * @param array $schemaAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer
     */
    public function build(string $schemaName, array $schemaAsArray): OpenApiDocumentSchemaTransfer;
}
