<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

class RefsFinder implements RefsFinderInterface
{
    /**
     * @var string
     */
    protected const FIELD_REF = '$ref';

    /**
     * @param array $contents
     *
     * @return array<string>
     */
    public function findRefs(array $contents): array
    {
        $refs = [];

        $refs = $this->doFindRefs($refs, $contents);

        return array_unique($refs);
    }

    /**
     * @param array<string> $refs
     * @param array<string> $contents
     *
     * @return array<string>
     */
    protected function doFindRefs(array $refs, array $contents): array
    {
        foreach ($contents as $key => $value) {
            if (is_array($value)) {
                $refs = $this->doFindRefs($refs, $value);
                continue;
            }

            if ($key === static::FIELD_REF && is_string($value)) {
                $refs[] = $value;
            }
        }

        return $refs;
    }
}
