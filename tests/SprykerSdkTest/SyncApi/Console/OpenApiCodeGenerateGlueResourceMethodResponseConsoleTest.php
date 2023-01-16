<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\Console;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Console\AbstractConsole;
use SprykerSdk\SyncApi\Console\OpenApiCodeGenerateConsole;
use SprykerSdk\SyncApi\Exception\SyncApiModuleNameNotFoundException;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdkTest\SyncApi\SyncApiTester;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group Console
 * @group OpenApiCodeGenerateGlueResourceMethodResponseConsoleTest
 */
class OpenApiCodeGenerateGlueResourceMethodResponseConsoleTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleThrowsExceptionWhenAModuleNameCanNotBeExtractedFromTheResource(): void
    {
        // Arrange
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderGlueResourceMethodResponseConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Expect
        $this->expectException(SyncApiModuleNameNotFoundException::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/invalid/slash_resource.yml'),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);
    }

    /**
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleWithSprykerExtensionInSchemaOnPathLevelReturnsSuccessCodeWhenProcessIsDone(): void
    {
        // Arrange
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderGlueResourceMethodResponseConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/valid/slash_resource_with_spryker_extension_on_path_level.yml'),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/', 'CatFace'), $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleWithSprykerExtensionInSchemaOnOperationLevelOverwritesExtensionOnPathLevel(): void
    {
        // Arrange
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderGlueResourceMethodResponseConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/valid/slash_resource_with_spryker_extension_on_operation_level.yml'),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/', 'DogNose'), $commandTester->getDisplay());
    }
}
