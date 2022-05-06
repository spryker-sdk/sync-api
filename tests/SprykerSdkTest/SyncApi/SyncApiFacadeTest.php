<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi;

use Codeception\Test\Unit;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group SyncApiFacadeTest
 */
class SyncApiFacadeTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testCreateOpenApiAddsANewOpenApiFile(): void
    {
        // Arrange
        $openApiRequestTransfer = $this->tester->haveOpenApiAddRequest();

        // Act
        $this->tester->getFacade()->createOpenApi(
            $openApiRequestTransfer,
        );

        // Assert
        $this->assertFileExists($openApiRequestTransfer->getTargetFile());
    }
}
