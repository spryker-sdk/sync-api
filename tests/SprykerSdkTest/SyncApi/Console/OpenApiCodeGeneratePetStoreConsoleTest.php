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
 * @group OpenApiCodeGeneratePetStoreConsoleTest
 */
class OpenApiCodeGeneratePetStoreConsoleTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return array<array<\string>>
     */
    public function applicationTypes(): array
    {
        return [
            'BackenApi' => ['backend', 'BackendApi'],
            'FrontendApi' => ['frontend', 'StorefrontApi'],
        ];
    }

    /**
     * @dataProvider applicationTypes
     *
     * @param string $applicationType
     * @param string $expectedModuleNameSuffix
     *
     * @return void
     */
    public function testOpenApiCodeGenerateConsoleWithSwaggerPetStoreForBackendApi(string $applicationType, string $expectedModuleNameSuffix): void
    {
        $buildFromOpenApiConsoleMock = $this->tester->getOpenApiBuilderConsoleMock();
        $commandTester = $this->tester->getConsoleTester($buildFromOpenApiConsoleMock);

        // Act
        $commandTester->execute([
            '--' . OpenApiCodeGenerateConsole::OPTION_OPEN_API_FILE => codecept_data_dir('api/valid/petstore.yml'),
            '--' . OpenApiCodeGenerateConsole::OPTION_APPLICATION_TYPE => $applicationType,
        ], ['verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE]);

        // Assert
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $commandTester->getStatusCode());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet', 'Pet' . $expectedModuleNameSuffix, 'put', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet', 'Pet' . $expectedModuleNameSuffix, 'put', '400'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet', 'Pet' . $expectedModuleNameSuffix, 'put', '404'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet', 'Pet' . $expectedModuleNameSuffix, 'put', '405'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet', 'Pet' . $expectedModuleNameSuffix, 'post', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet', 'Pet' . $expectedModuleNameSuffix, 'post', '405'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/findByStatus', 'Pet' . $expectedModuleNameSuffix, 'get', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/findByStatus', 'Pet' . $expectedModuleNameSuffix, 'get', '400'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/findByTags', 'Pet' . $expectedModuleNameSuffix, 'get', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/findByTags', 'Pet' . $expectedModuleNameSuffix, 'get', '400'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/{petId}', 'Pet' . $expectedModuleNameSuffix, 'get', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/{petId}', 'Pet' . $expectedModuleNameSuffix, 'get', '400'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/{petId}', 'Pet' . $expectedModuleNameSuffix, 'get', '404'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/{petId}', 'Pet' . $expectedModuleNameSuffix, 'post', '405'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/{petId}', 'Pet' . $expectedModuleNameSuffix, 'delete', '400'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/pet/{petId}/uploadImage', 'Pet' . $expectedModuleNameSuffix, 'post', '200'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/inventory', 'Store' . $expectedModuleNameSuffix, 'get', '200'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/order', 'Store' . $expectedModuleNameSuffix, 'post', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/order', 'Store' . $expectedModuleNameSuffix, 'post', '405'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/order/{orderId}', 'Store' . $expectedModuleNameSuffix, 'get', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/order/{orderId}', 'Store' . $expectedModuleNameSuffix, 'get', '400'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/order/{orderId}', 'Store' . $expectedModuleNameSuffix, 'get', '404'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/order/{orderId}', 'Store' . $expectedModuleNameSuffix, 'delete', '400'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/store/order/{orderId}', 'Store' . $expectedModuleNameSuffix, 'delete', '404'), $commandTester->getDisplay());

        // This one is not added currently as it has no response other than 'default' defined.
        // $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user', 'User' . $expectedModuleNameSuffix, 'post', 'default'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/createWithList', 'User' . $expectedModuleNameSuffix, 'post', '200'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/login', 'User' . $expectedModuleNameSuffix, 'get', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/login', 'User' . $expectedModuleNameSuffix, 'get', '400'), $commandTester->getDisplay());

        // This one is not added currently as it has no response other than 'default' defined.
        // $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/logout', 'User' . $expectedModuleNameSuffix, 'get', 'default'), $commandTester->getDisplay());

        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/{username}', 'User' . $expectedModuleNameSuffix, 'get', '200'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/{username}', 'User' . $expectedModuleNameSuffix, 'get', '400'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/{username}', 'User' . $expectedModuleNameSuffix, 'get', '404'), $commandTester->getDisplay());

        // This one is not added currently as it has no response other than 'default' defined.
        // $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/{username}', 'User' . $expectedModuleNameSuffix, 'put', 'default'), $commandTester->getDisplay());

        // The petstore.yml does not define what a successful response is??
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/{username}', 'User' . $expectedModuleNameSuffix, 'delete', '400'), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedGlueResourceMethodResponse('/user/{username}', 'User' . $expectedModuleNameSuffix, 'delete', '404'), $commandTester->getDisplay());

        // Transfers
        $this->assertStringContainsString(SyncApiInfo::addedTransfer('Pet', 'Pet' . $expectedModuleNameSuffix), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedTransfer('ApiResponse', 'Pet' . $expectedModuleNameSuffix), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedTransfer('Order', 'Store' . $expectedModuleNameSuffix), $commandTester->getDisplay());
        $this->assertStringContainsString(SyncApiInfo::addedTransfer('User', 'User' . $expectedModuleNameSuffix), $commandTester->getDisplay());
    }
}
