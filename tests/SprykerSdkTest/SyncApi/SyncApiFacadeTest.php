<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi;

use Codeception\Test\Unit;
use Symfony\Component\Yaml\Yaml;

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

    /**
     * @return void
     */
    public function testCreateOpenApiCreatesExpectedOpenApiFile(): void
    {
        // Arrange
        $openApiRequestTransfer = $this->tester->haveOpenApiAddRequest();

        // Act
        $this->tester->getFacade()->createOpenApi(
            $openApiRequestTransfer,
        );

        // Assert
        $openApi = Yaml::parseFile($openApiRequestTransfer->getTargetFile());
        $this->assertArrayHasKey('openapi', $openApi);
        $this->assertSame('3.0.0', $openApi['openapi']);
        $this->assertArrayHasKey('info', $openApi);

        $info = $openApi['info'];

        // Test that title is correct
        $this->assertArrayHasKey('title', $info);
        $this->assertSame('Test File', $info['title']);

        // Tests that version is correct
        $this->assertArrayHasKey('version', $info);
        $this->assertSame('0.1.0', $info['version']);
    }
}
