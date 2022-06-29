<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\Console;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Console\AbstractConsole;
use SprykerSdk\SyncApi\Console\OpenApiCodeGenerateConsole;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdkTest\SyncApi\SyncApiTester;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group Console
 * @group OpenApiCodeGenerateConsoleTest
 */
class OpenApiCodeGenerateConsoleTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleReturnsSuccessCodeWhenProcessIsDone(): void
    {
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/valid/valid_openapi.yml'),
        ]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleReturnsSuccessCodeWhenProcessIsDoneAndPrintsResultToConsoleInVerboseMode(): void
    {
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/valid/valid_openapi.yml'),
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiInfo::generatedCodeFromOpenApiSchema(), $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleReturnsErrorCodeWhenAnErrorOccurred(): void
    {
        // Arrange
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/invalid/invalid_openapi.yml'),
            '--' . OpenApiCodeGenerateConsole::APPLICATION_TYPE => 'backend',
            '--' . OpenApiCodeGenerateConsole::OPTION_ORGANIZATION => 'Spryker',
        ]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleReturnsErrorCodeWhenSchemaIsMissingAndPrintsResultToConsoleInVerboseMode(): void
    {
        // Arrange
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/invalid/empty_openapi.yml'),
            '--' . OpenApiCodeGenerateConsole::APPLICATION_TYPE => 'backend',
            '--' . OpenApiCodeGenerateConsole::OPTION_ORGANIZATION => 'Spryker',
        ], ['verbosity' => OutputInterface::VERBOSITY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testBuildFromOpenApiReturnsErrorCodeWhenAnErrorOccurredAndPrintsResultToConsoleInVerboseMode(): void
    {
        // Arrange
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute(
            [
                '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/invalid/invalid_openapi.yml'),
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertNotEmpty($commandTester->getDisplay());
    }
}
