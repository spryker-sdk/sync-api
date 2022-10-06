<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class DocumentBuilder implements DocumentBuilderInterface
{
    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\InfoBuilderInterface
     */
    protected $infoBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\ServersBuilderInterface
     */
    protected $serversBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathsBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\ComponentsBuilderInterface
     */
    protected $componentsBuilder;

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\InfoBuilderInterface $infoBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\ServersBuilderInterface $serversBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathsBuilderInterface $pathBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\ComponentsBuilderInterface $componentsBuilder
     */
    public function __construct(
        InfoBuilderInterface $infoBuilder,
        ServersBuilderInterface $serversBuilder,
        PathsBuilderInterface $pathBuilder,
        ComponentsBuilderInterface $componentsBuilder
    ) {
        $this->infoBuilder = $infoBuilder;
        $this->serversBuilder = $serversBuilder;
        $this->pathBuilder = $pathBuilder;
        $this->componentsBuilder = $componentsBuilder;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentTransfer
     */
    public function buildOpenApiDocumentFromArray(array $openApiYamlAsArray): OpenApiDocumentTransfer
    {
        $documentTransfer = new OpenApiDocumentTransfer();

        $documentTransfer
            ->setVersion($this->getVersion($openApiYamlAsArray))
            ->setInfo($this->infoBuilder->build($openApiYamlAsArray))
            ->setServers($this->serversBuilder->build($openApiYamlAsArray))
            ->setPaths($this->pathBuilder->build($openApiYamlAsArray))
            ->setComponents($this->componentsBuilder->build($openApiYamlAsArray));

        return $documentTransfer;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return string
     */
    protected function getVersion(array $openApiYamlAsArray): string
    {
        return $openApiYamlAsArray['openapi'] ?? '';
    }
}
