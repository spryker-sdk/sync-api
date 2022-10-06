<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer;

class SchemaBuilder implements SchemaBuilderInterface
{
    /**
     * @param array $schemaAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer
     */
    public function build(array $schemaAsArray): OpenApiDocumentSchemaTransfer
    {
        return (new OpenApiDocumentSchemaTransfer())
            ->setName(array_keys($schemaAsArray)[0])
            ->setContents($schemaAsArray[array_keys($schemaAsArray)[0]]);
    }
}
