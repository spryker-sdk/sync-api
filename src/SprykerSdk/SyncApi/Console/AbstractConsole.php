<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Console;

use SprykerSdk\SyncApi\SyncApiConfig;
use SprykerSdk\SyncApi\SyncApiFacade;
use SprykerSdk\SyncApi\SyncApiFacadeInterface;
use Symfony\Component\Console\Command\Command;

class AbstractConsole extends Command
{
    /**
     * @var int
     */
    public const CODE_SUCCESS = 0;

    /**
     * @var int
     */
    public const CODE_ERROR = 1;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig|null
     */
    protected ?SyncApiConfig $config = null;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiFacadeInterface|null
     */
    protected ?SyncApiFacadeInterface $facade = null;

    /**
     * @return \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected function getConfig(): SyncApiConfig
    {
        if ($this->config === null) {
            $this->config = new SyncApiConfig();
        }

        return $this->config;
    }

    /**
     * @param \SprykerSdk\SyncApi\SyncApiFacadeInterface $facade
     *
     * @return void
     */
    public function setFacade(SyncApiFacadeInterface $facade): void
    {
        $this->facade = $facade;
    }

    /**
     * @return \SprykerSdk\SyncApi\SyncApiFacadeInterface
     */
    protected function getFacade(): SyncApiFacadeInterface
    {
        if (!$this->facade) {
            $this->facade = new SyncApiFacade();
        }

        return $this->facade;
    }
}
