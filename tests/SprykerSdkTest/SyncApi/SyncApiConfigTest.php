<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\SyncApiConfig;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group SyncApiConfigTest
 */
class SyncApiConfigTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected $tester;

    /**
     * Tests that ensures we get the default executable path when installed the "normal" way.
     *
     * @return void
     */
    public function testGetSprykRunExecutableReturnsDefaultExecutable(): void
    {
        // Arrange
        $expectedExecutable = getcwd();
        $config = new SyncApiConfig();

        // Act
        $sprykRunExecutable = $config->getSprykRunExecutablePath();

        // Assert
        $this->assertSame($expectedExecutable, $sprykRunExecutable);
    }

    /**
     * Tests that ensures we get the default executable path when installed the "normal" way.
     *
     * @return void
     */
    public function testGetProjectRootPathReturnsCurrentWorkingDirectory(): void
    {
        // Arrange
        $expectedExecutable = getcwd();
        $config = new SyncApiConfig();

        // Act
        $projectRootPath = $config->getProjectRootPath();

        // Assert
        $this->assertSame($expectedExecutable, $projectRootPath);
    }

    /**
     * Tests that ensures we get a path to where this SDK is installed. Usually only when used within the SprykerSDK.
     *
     * @return void
     */
    public function testGetSprykRunExecutableReturnsExternalDefinedExecutable(): void
    {
        putenv('INSTALLED_ROOT_DIRECTORY=foo-bar');
        // Arrange
        $expectedExecutable = 'foo-bar';
        $config = new SyncApiConfig();

        // Act
        $sprykRunExecutable = $config->getSprykRunExecutablePath();

        // Assert
        $this->assertSame($expectedExecutable, $sprykRunExecutable);
        putenv('INSTALLED_ROOT_DIRECTORY');
    }

    /**
     * @return void
     */
    public function testGetAvailableHttpMethodsReturnsCorrectHttpMethods(): void
    {
        // Arrange
        $config = new SyncApiConfig();

        // Act
        $httpMethods = $config->getAvailableHttpMethods();

        // Assert
        $this->assertEquals(
            [
                'get',
                'put',
                'post',
                'delete',
                'options',
                'head',
                'patch',
                'trace',
            ],
            $httpMethods,
        );
    }
}
