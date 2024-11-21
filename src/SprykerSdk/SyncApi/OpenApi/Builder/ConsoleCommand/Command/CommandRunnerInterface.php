<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command;

interface CommandRunnerInterface
{
    /**
     * @param array<array<string, string>> $commands
     *
     * @return void
     */
    public function runCommands(array $commands): void;
}
