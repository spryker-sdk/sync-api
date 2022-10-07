<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

interface RefsFinderInterface
{
    /**
     * @param array $contents
     *
     * @return array<string>
     */
    public function findRefs(array $contents): array;
}
