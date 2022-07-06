<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\Console;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Console\AbstractConsole;
use SprykerSdk\SyncApi\Console\OpenApiCreateConsole;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdkTest\SyncApi\SyncApiTester;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group Console
 * @group OpenApiCreateConsoleTest
 */
class OpenApiCreateConsoleTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testOpenApiCreateConsole(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiCreateConsole::class);

        // Act
        $commandTester->execute(
            [
                OpenApiCreateConsole::ARGUMENT_TITLE => 'Test File',
                '--' . OpenApiCreateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
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
    public function testCreateOpenApiConsoleWithFileExists(): void
    {
        // Arrange
        $commandTester = $this->tester->getConsoleTester(OpenApiCreateConsole::class);
        $this->tester->haveOpenApiFile();

        // Act
        $commandTester->execute(
            [
                OpenApiCreateConsole::ARGUMENT_TITLE => 'Test File',
                '--' . OpenApiCreateConsole::OPTION_PROJECT_ROOT => $this->tester->getRootPath(),
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE,
            ],
        );

        // Assert
        $this->assertSame(AbstractConsole::CODE_ERROR, $commandTester->getStatusCode());
        $this->assertStringContainsString(SyncApiError::openApiFileAlreadyExists('vfs://root/resources/api/openapi.yml'), $commandTester->getDisplay());
    }
}
