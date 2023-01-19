<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments;
use SprykerSdkTest\SyncApi\SyncApiTester;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group OpenApi
 * @group Builder
 * @group ConsoleCommand
 * @group Arguments
 * @group GlueResourceMethodResponseArgumentsTest
 */
class GlueResourceMethodResponseArgumentsTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * Tests that ensures we get a path to where this SDK is installed. Usually only when used within the SprykerSDK.
     *
     * @return void
     */
    public function testGetConsoleCommandArgumentsOnlyReturnsNotNullProperties(): void
    {
        // Arrange
        $glueResourceMethodResonseArguments = new GlueResourceMethodResponseArguments();

        // Act
        $consoleArguments = $glueResourceMethodResonseArguments->getConsoleCommandArguments();

        // Assert
        $this->assertTrue(in_array('--apiType', $consoleArguments));
        $this->assertFalse(in_array('--module', $consoleArguments));
    }
}
