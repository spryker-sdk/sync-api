<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder;

interface FilepathBuilderInterface
{
    /**
     * @param string $filename
     * @param string $rootDirectoryPath
     *
     * @return string
     */
    public function buildSyncApiFilepath(string $filename, string $rootDirectoryPath): string;
}
