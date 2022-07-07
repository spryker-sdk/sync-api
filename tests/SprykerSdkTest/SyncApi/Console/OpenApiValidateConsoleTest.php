<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\Console;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Console\AbstractConsole;
use SprykerSdk\SyncApi\Console\OpenApiValidateConsole;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdkTest\SyncApi\SyncApiTester;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group Console
 * @group OpenApiValidateConsoleTest
 */
class OpenApiValidateConsoleTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsSuccessCodeWhenValidationIsSuccessful(): void
    {
        // Arrange
        $this->tester->haveValidOpenApiFile();

        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsErrorCodeAndPrintsErrorMessagesWhenValidationFailed(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertNotEmpty($commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsErrorCodeAndPrintsErrorMessagesWhenFileCouldNotBeParsed(): void
    {
        // Arrange
        $this->tester->haveOpenApiFileThatCouldNotBeParsed();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiError::couldNotParseOpenApi('vfs://root/resources/api/openapi.yml'), $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsErrorCodeAndPrintsErrorMessagesWhenFileDoesNotContainAnyPaths(): void
    {
        // Arrange
        $this->tester->haveDefaultOpenApiFile();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertStringContainsString(
            SyncApiError::openApiDoesNotDefineAnyPath(
                sprintf('%s/%s/openapi.yml', $this->tester->getRootPath(), $this->tester->getOpenApiSchemaPath()),
            ),
            $commandTester->getDisplay(),
        );
    }

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsErrorCodeAndPrintsErrorMessagesWhenFileDoesNotContainAnyComponents(): void
    {
        // Arrange
        $this->tester->haveDefaultOpenApiFile();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertStringContainsString(
            SyncApiError::openApiDoesNotDefineAnyComponents(
                sprintf('%s/%s/openapi.yml', $this->tester->getRootPath(), $this->tester->getOpenApiSchemaPath()),
            ),
            $commandTester->getDisplay(),
        );
    }

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsErrorCodeAndPrintsErrorMessagesWhenFileDoesNotContainValidHttpMethodsInPathDefinition(): void
    {
        // Arrange
        $this->tester->haveOpenApiFileWithPathButInvalidHttpMethod();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiError::openApiContainsInvalidHttpMethodForPath('bar', '/foo'), $commandTester->getDisplay());
    }
}
