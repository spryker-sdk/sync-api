<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer;

class SchemaBuilder implements SchemaBuilderInterface
{
    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\RefsFinderInterface
     */
    protected $refsFinder;

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\RefsFinderInterface $refsFinder
     */
    public function __construct(RefsFinderInterface $refsFinder)
    {
        $this->refsFinder = $refsFinder;
    }

    /**
     * @param array $schemaAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer
     */
    public function build(array $schemaAsArray): OpenApiDocumentSchemaTransfer
    {
        return (new OpenApiDocumentSchemaTransfer())
            ->setName(array_keys($schemaAsArray)[0])
            ->setContents($schemaAsArray[array_keys($schemaAsArray)[0]])
            ->setRefs($this->refsFinder->findRefs($schemaAsArray[array_keys($schemaAsArray)[0]]));
    }
}
