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
        $openApiResponseTransfer = $this->tester->getFacade()->createOpenApi(
            $openApiRequestTransfer,
        );

        // Assert
        $this->assertFileExists($openApiRequestTransfer->getTargetFile());
        $this->assertCount(0, $openApiResponseTransfer->getErrors());
        $this->assertCount(1, $openApiResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testCreateOpenApiCreatesExpectedOpenApiFile(): void
    {
        // Arrange
        $openApiRequestTransfer = $this->tester->haveOpenApiAddRequest();

        // Act
        $openApiResponseTransfer = $this->tester->getFacade()->createOpenApi(
            $openApiRequestTransfer,
        );

        // Assert
        $expectedOpenApi = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Test title',
                'version' => '0.1.0',
            ],
        ];

        $openApi = Yaml::parseFile($openApiRequestTransfer->getTargetFile());
        $this->assertSame($expectedOpenApi, $openApi);
        $this->assertCount(0, $openApiResponseTransfer->getErrors());
        $this->assertCount(1, $openApiResponseTransfer->getMessages());
    }
}
