<?php

namespace SprykerSdk\SyncApi\OpenApi\Converter;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class OpenApiDocumentToArrayConverter implements OpenApiDocumentToArrayConverterInterface
{
    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Converter\PathsToArrayConverterInterface
     */
    protected $pathsToArrayConverter;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Converter\ComponentsToArrayConverterInterface
     */
    protected $componentsToArrayConverter;


    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Converter\PathsToArrayConverterInterface $pathsToArrayConverter
     * @param \SprykerSdk\SyncApi\OpenApi\Converter\ComponentsToArrayConverterInterface $componentsToArrayConverter
     */
    public function __construct(
        PathsToArrayConverterInterface $pathsToArrayConverter,
        ComponentsToArrayConverterInterface $componentsToArrayConverter
    ) {
        $this->pathsToArrayConverter = $pathsToArrayConverter;
        $this->componentsToArrayConverter = $componentsToArrayConverter;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $openApiDocumentTransfer
     *
     * @return array
     */
    public function convert(OpenApiDocumentTransfer $openApiDocumentTransfer): array
    {
        return [
            'version' => $openApiDocumentTransfer->getVersion(),
            'info' => $openApiDocumentTransfer->getInfo()->getContents(),
            'servers' => $this->convertServers($openApiDocumentTransfer->getServers()),
            'paths' => $this->pathsToArrayConverter->convert($openApiDocumentTransfer),
            'components' => $this->componentsToArrayConverter->convert($openApiDocumentTransfer),
        ];
    }

    /**
     * @param array<\Generated\Shared\Transfer\OpenApiDocumentServerTransfer> $serverTransfers
     *
     * @return array
     */
    protected function convertServers(array $serverTransfers): array
    {
        $servers = [];

        foreach($serverTransfers as $serverTransfer) {
            $servers[] = $serverTransfer->toArray();
        }

        return $servers;
    }
}
