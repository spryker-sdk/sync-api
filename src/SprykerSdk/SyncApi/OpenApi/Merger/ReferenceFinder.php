<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

class ReferenceFinder
{
    /**
     * @param array $array
     *
     * @return array
     */
    public static function findInArray(array $array): array
    {
        return array_unique(static::doFindInArray($array, []));
    }

    /**
     * @param array $array
     * @param array<string> $references
     *
     * @return array
     */
    protected static function doFindInArray(array $array, array $references): array
    {
        foreach ($array as $key => $value) {
            if ($key === '$ref') {
                $references[] = $value;
            }

            if (is_array($value)) {
                $references = static::doFindInArray($value, $references);
            }
        }

        return $references;
    }
}
