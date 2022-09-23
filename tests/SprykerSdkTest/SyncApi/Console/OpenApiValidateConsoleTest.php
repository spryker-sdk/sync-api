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
        ], ['verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE]);

        // Assert
        $this->tester->assertSuccessStatusCode($commandTester->getStatusCode());
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
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
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
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
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
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
        $this->assertStringContainsString(
            SyncApiError::openApiDoesNotDefineAnyPath(
                sprintf('%s/%s/openapi.yml', $this->tester->getRootPath(), $this->tester->getOpenApiSchemaDirectory()),
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
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
        $this->assertStringContainsString(
            SyncApiError::openApiDoesNotDefineAnyComponents(
                $this->tester->getOpenApiFilePath(),
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
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiError::openApiContainsInvalidHttpMethodForPath('bar', '/foo'), $commandTester->getDisplay());
    }

    /**
     * Since Open API specification v3 status codes must be enclosed in quotation marks for v2 it is allowed to not enclose.
     *
     * @return void
     */
    public function testValidateOpenApiV2ReturnsSuccessCodeWhenHttpStatusCodeIsNotEnclosedInQuotationMarks(): void
    {
        // Arrange
        $this->tester->haveOpenApiV2FileWithHttpStatusCodeNotEnclosedInQuotationMarks();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->tester->assertSuccessStatusCode($commandTester->getStatusCode());
        $this->assertStringNotContainsString(SyncApiError::openApiHttpStatusCodeIsNotEnclosedInQuotationMarks('/wrong-status-code-definition', '200', 'post', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
    }

    /**
     * Since Open API specification v3 status codes must be enclosed in quotation marks.
     *
     * @return void
     */
    public function testValidateOpenApiV3ReturnsErrorCodeAndPrintsErrorMessagesWhenHttpStatusCodeIsNotEnclosedInQuotationMarks(): void
    {
        // Arrange
        $this->tester->haveOpenApiV3FileWithHttpStatusCodeNotEnclosedInQuotationMarks();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiError::openApiHttpStatusCodeIsNotEnclosedInQuotationMarks('/wrong-status-code-definition', '200', 'post', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiError::openApiHttpStatusCodeIsNotEnclosedInQuotationMarks('/wrong-status-code-definition-2', '300', 'post', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsErrorCodeAndPrintsErrorMessagesWhenPathsEnclosedInQuotationMarks(): void
    {
        // Arrange
        $this->tester->haveInvalidOpenApiFile();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiError::openApiPathMustNotBeEnclosedInQuotationMarks('/', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiError::openApiPathMustNotBeEnclosedInQuotationMarks('/apps/{appId}', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testValidateOpenApiReturnsErrorCodeAndPrintsErrorMessagesWhenServerUrlHasATrailingSlash(): void
    {
        // Arrange
        $this->tester->haveInvalidOpenApiFile();
        $commandTester = $this->tester->getConsoleTester(OpenApiValidateConsole::class);

        // Act
        $commandTester->execute([
            '--' . OpenApiValidateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->tester->assertErrorStatusCode($commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiError::openApiServerUrlMustNotHaveATrailingASlash('https://glue.trs.demo-spryker.com/', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiError::openApiServerUrlMustNotHaveATrailingASlash('https://glue.trs-staging.demo-spryker.com/', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiError::openApiServerUrlMustNotHaveATrailingASlash('http://glue.registry.spryker.local/', $this->tester->getOpenApiFilePath()), $commandTester->getDisplay());
    }
}
