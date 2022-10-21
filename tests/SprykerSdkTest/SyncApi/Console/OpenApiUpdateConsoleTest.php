<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\Console;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Console\AbstractConsole;
use SprykerSdk\SyncApi\Console\OpenApiUpdateConsole;
use SprykerSdkTest\SyncApi\SyncApiTester;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group Console
 * @group OpenApiUpdateConsoleTest
 */
class OpenApiUpdateConsoleTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleSuccessfullyUpdatesAnExistingFile(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);
        $this->tester->haveValidOpenApiFile();

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => $this->tester->getValidOpenApiContentsAsJson(),
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertEquals(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Successfully updated', $commandTester->getDisplay());
        $this->assertFileExists('vfs://root/resources/api/openapi.yml');
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleSuccessfullyAddsANewFileWithPassedJson(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => $this->tester->getValidOpenApiContentsAsJson(),
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Successfully updated', $commandTester->getDisplay());
        $this->assertFileExists('vfs://root/resources/api/openapi.yml');
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleSuccessfullyAddsANewFileWithPassedJsonAndCustomOptions(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => $this->tester->getValidOpenApiContentsAsJson(),
                '--' . OpenApiUpdateConsole::OPTION_OPEN_API_FILE => 'custom_openapi.yml',
                '--' . OpenApiUpdateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath() . '/custom_dir/',
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Successfully updated', $commandTester->getDisplay());
        $this->assertFileExists('vfs://root/custom_dir/custom_openapi.yml');
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleStopsWithErrorWhenSourceDataHasInvalidJson(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => 'INVALID_JSON',
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertStringContainsString('Provided Open API data is invalid', $commandTester->getDisplay());
        $this->assertFileNotExists('vfs://root/resources/api/openapi.yml');
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleStopsWithErrorWhenMergeProcessWillFails(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => '{"components":null}',
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertStringContainsString('Update Open API failed with error', $commandTester->getDisplay());
        $this->assertFileNotExists('vfs://root/resources/api/openapi.yml');
    }
}
