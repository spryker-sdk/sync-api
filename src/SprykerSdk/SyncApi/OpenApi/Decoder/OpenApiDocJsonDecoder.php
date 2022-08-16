<?php

namespace SprykerSdk\SyncApi\OpenApi\Decoder;

class OpenApiDocJsonDecoder implements OpenApiDocDecoderInterface
{
    /**
     * @var int
     */
    protected const DECODE_DEPTH = 512;

    /**
     * @param string $encodedDoc
     *
     * @return array
     */
    public function decode(string $encodedDoc): array
    {
        return json_decode($encodedDoc, true, static::DECODE_DEPTH, JSON_THROW_ON_ERROR);
    }
}
