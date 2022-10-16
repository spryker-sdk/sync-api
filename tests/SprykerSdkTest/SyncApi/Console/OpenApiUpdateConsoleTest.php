<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\Console;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Console\AbstractConsole;
use SprykerSdk\SyncApi\Console\OpenApiUpdateConsole;
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
    protected $tester;

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleSuccessfullyUpdatesExistedFile(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);
        $this->tester->haveValidOpenApiFile();

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => $this->tester->getValidOpenApiContentsAsJson(),
                '--' . OpenApiUpdateConsole::OPTION_OPEN_API_FILE => $this->tester->getOpenApiSchemaPath() . '/openapi.yml',
                '--' . OpenApiUpdateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleSuccessfullyUpdatesNewFile(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => $this->tester->getValidOpenApiContentsAsJson(),
                '--' . OpenApiUpdateConsole::OPTION_OPEN_API_FILE => $this->tester->getOpenApiSchemaPath() . '/openapi.yml',
                '--' . OpenApiUpdateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleStopsWithErrorWhenSourceDataHasInvalidJson(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);
        $this->tester->haveValidOpenApiFile();

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => 'INVALID_JSON',
                '--' . OpenApiUpdateConsole::OPTION_OPEN_API_FILE => $this->tester->getOpenApiSchemaPath() . '/openapi.yml',
                '--' . OpenApiUpdateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleStopsWithErrorWhenSourceDataHasInvalidOpenApiSchema(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);
        $this->tester->haveValidOpenApiFile();

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => '{}',
                '--' . OpenApiUpdateConsole::OPTION_OPEN_API_FILE => $this->tester->getOpenApiSchemaPath() . '/openapi.yml',
                '--' . OpenApiUpdateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testOpenApiUpdateConsoleStopsWithErrorWhenMergeProcessWillFails(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiUpdateConsole::class);
        $this->tester->haveValidOpenApiFile();

        // Act
        $commandTester->execute(
            [
                OpenApiUpdateConsole::ARGUMENT_OPENAPI_DOC => $this->tester->getOpenApiContentsWithMissedReferenceJson(),
                '--' . OpenApiUpdateConsole::OPTION_OPEN_API_FILE => $this->tester->getOpenApiSchemaPath() . '/openapi.yml',
                '--' . OpenApiUpdateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
    }
}
