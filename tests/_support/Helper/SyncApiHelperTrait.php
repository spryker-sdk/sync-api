<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Helper;

trait SyncApiHelperTrait
{
    /**
     * @return \SprykerSdkTest\Helper\SyncApiHelper
     */
    protected function getSyncApiHelper(): SyncApiHelper
    {
        /** @var \SprykerSdkTest\Helper\SyncApiHelper $syncApiHelper */
        $syncApiHelper = $this->getModule('\\' . SyncApiHelper::class);

        return $syncApiHelper;
    }

    /**
     * @param string $name
     *
     * @return \Codeception\Module
     */
    abstract protected function getModule($name);
}
