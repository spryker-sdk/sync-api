<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer;

class PathUriBuilder implements PathUriBuilderInterface
{
    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriProtocolsBuilderInterface
     */
    protected $pathUriProtocolsBuilder;

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriProtocolsBuilderInterface $pathUriProtocolBuilder
     */
    public function __construct(PathUriProtocolsBuilderInterface $pathUriProtocolsBuilder)
    {
        $this->pathUriProtocolsBuilder = $pathUriProtocolsBuilder;
    }

    /**
     * @param array $pathUrisAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer
     */
    public function build(string $uri, array $pathUrisAsArray): OpenApiDocumentPathUriTransfer
    {
        $pathUriTransfer = new OpenApiDocumentPathUriTransfer();

        foreach ($pathUrisAsArray as $protocol => $pathUriProtocolAsArray) {
            $pathUriTransfer
                ->setUri($uri)
                ->addProtocol($this->pathUriProtocolsBuilder->build($protocol, $pathUriProtocolAsArray));
        }

        return $pathUriTransfer;
    }
}
