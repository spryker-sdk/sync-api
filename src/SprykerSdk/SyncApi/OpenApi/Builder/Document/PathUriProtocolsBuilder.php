<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use ArrayObject;
use Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer;

class PathUriProtocolsBuilder implements PathUriProtocolsBuilderInterface
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
     * @param string $protocol
     * @param array $pathUriProtocolArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer
     */
    public function build(string $protocol, array $pathUriProtocolArray): OpenApiDocumentPathUriProtocolTransfer
    {
        return (new OpenApiDocumentPathUriProtocolTransfer())
            ->setProtocol($protocol)
            ->setContents($pathUriProtocolArray)
            ->setRefs($this->refsFinder->findRefs($pathUriProtocolArray));
    }
}
