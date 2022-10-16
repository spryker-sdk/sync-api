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
    public function testUpdateOpenApiUpdatesExistingOpenApiFile(): void
    {
        // Arrange
        $updateOpenApiRequestTransfer = $this->tester->haveUpdateExistingFileRequest();

        // Act
        $this->tester->getFacade()->updateOpenApi($updateOpenApiRequestTransfer);

        // Assert
        $this->assertFileExists('vfs://root/resources/api/existing.yml');
        $this->assertEquals(
            Yaml::parseFile(codecept_data_dir('api/update/expected.yml')),
            Yaml::parseFile('vfs://root/resources/api/existing.yml'),
        );
    }

    /**
     * @return void
     */
    public function testUpdateOpenApiCreatesNewOpenApiFile(): void
    {
        // Arrange
        $updateOpenApiRequestTransfer = $this->tester->haveUpdateNewFileRequest();

        // Act
        $this->tester->getFacade()->updateOpenApi($updateOpenApiRequestTransfer);

        // Assert
        $this->assertFileExists('vfs://root/resources/api/new_file.yml');
        $this->assertEquals(
            Yaml::parseFile(codecept_data_dir('api/update/expected.yml')),
            Yaml::parseFile('vfs://root/resources/api/new_file.yml'),
        );
    }
}
