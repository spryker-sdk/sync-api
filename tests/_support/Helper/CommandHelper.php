<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Helper;

use Codeception\Module;
use SprykerSdk\SyncApi\Console\AbstractConsole;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CommandHelper extends Module
{
    use SyncApiHelperTrait;

    /**
     * @param \SprykerSdk\SyncApi\Console\AbstractConsole|string $command
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    public function getConsoleTester($command): CommandTester
    {
        if (!($command instanceof AbstractConsole)) {
            $command = new $command(null, $this->getSyncApiHelper()->getConfig());
        }

        $application = new Application();
        $application->add($command);

        $command = $application->find($command->getName());

        return new CommandTester($command);
    }
}
