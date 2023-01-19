<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command;

use SprykerSdk\SyncApi\SyncApiConfig;
use Symfony\Component\Process\Process;

class CommandRunner implements CommandRunnerInterface
{
    protected SyncApiConfig $config;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     */
    public function __construct(SyncApiConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param array<array> $commands
     *
     * @return void
     */
    public function runCommands(array $commands): void
    {
        foreach ($commands as $command) {
            $this->runProcess($command);
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @param array $command
     *
     * @return void
     */
    protected function runProcess(array $command): void
    {
        $process = new Process($command, $this->config->getProjectRootPath(), null, null, 300);

        $process->run(function ($a, $buffer) {
            echo $buffer;
            // For debugging purposes, set a breakpoint here to see issues.
        });
    }
}
