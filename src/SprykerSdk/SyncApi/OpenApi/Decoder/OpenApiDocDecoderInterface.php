<?php

namespace SprykerSdk\SyncApi\OpenApi\Decoder;

interface OpenApiDocDecoderInterface
{
    /**
     * @param string $encodedDoc
     *
     * @return array
     */
    public function decode(string $encodedDoc): array;
}
