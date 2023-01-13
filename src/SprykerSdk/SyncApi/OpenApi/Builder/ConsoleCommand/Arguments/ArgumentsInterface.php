<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments;

interface ArgumentsInterface
{
    /**
     * @return array<string, int|string>
     */
    public function getConsoleCommandArguments(): array;
}
