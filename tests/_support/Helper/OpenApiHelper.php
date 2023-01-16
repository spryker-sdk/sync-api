<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Helper;

use cebe\openapi\spec\OpenApi;
use Codeception\Module;
use Codeception\Stub;
use Codeception\Stub\Expected;
use SprykerSdk\SyncApi\Console\OpenApiCodeGenerateConsole;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunner;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\TransferCommand;
use SprykerSdk\SyncApi\SyncApiFacade;
use SprykerSdk\SyncApi\SyncApiFactory;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;
use Transfer\OpenApiTransfer;

class OpenApiHelper extends Module
{
    use SyncApiHelperTrait;

    /**
     * @return \Transfer\OpenApiRequestTransfer
     */
    public function haveOpenApiAddRequest(): OpenApiRequestTransfer
    {
        $config = $this->getSyncApiHelper()->getConfig();

        $openApiTransfer = new OpenApiTransfer();
        $openApiTransfer
            ->setTitle('Test title')
            ->setVersion('0.1.0');

        $openApiRequestTransfer = new OpenApiRequestTransfer();
        $openApiRequestTransfer
            ->setTargetFile($config->getDefaultRelativePathToOpenApiFile())
            ->setOpenApi($openApiTransfer);

        return $openApiRequestTransfer;
    }

    /**
     * return void
     *
     * @return void
     */
    public function haveOpenApiFile(): void
    {
        $this->prepareOpenApiFile(codecept_data_dir('api/valid/valid_openapi.yml'));
    }

    /**
     * @param string $pathToOpenApi
     *
     * @return void
     */
    protected function prepareOpenApiFile(string $pathToOpenApi): void
    {
        $filePath = sprintf('%s/resources/api/openapi.yml', $this->getSyncApiHelper()->getRootPath());

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0770, true);
        }

        file_put_contents($filePath, file_get_contents($pathToOpenApi));
    }

    /**
     * @return \SprykerSdk\SyncApi\Console\OpenApiCodeGenerateConsole
     */
    public function getOpenApiBuilderConsoleMock(): OpenApiCodeGenerateConsole
    {
        $commandRunnerStub = Stub::make(
            CommandRunner::class,
            [
                'runProcess' => Expected::atLeastOnce(),
            ],
        );

        $factoryStub = Stub::make(SyncApiFactory::class, [
            'createCommandRunner' => $commandRunnerStub,
        ]);

        $facade = new SyncApiFacade();
        $facade->setFactory($factoryStub);

        $buildFromOpenApiConsole = new OpenApiCodeGenerateConsole();
        $buildFromOpenApiConsole->setFacade($facade);

        return $buildFromOpenApiConsole;
    }

    /**
     * Used to test only the GlueResourceMethodResponseCommandRunner.
     *
     * @return \SprykerSdk\SyncApi\Console\OpenApiCodeGenerateConsole
     */
    public function getOpenApiBuilderGlueResourceMethodResponseConsoleMock(): OpenApiCodeGenerateConsole
    {
        $commandRunnerStub = Stub::make(
            CommandRunner::class,
            [
                'runProcess' => Expected::atLeastOnce(),
            ],
        );
        $transferCommandStub = Stub::make(
            TransferCommand::class,
            [
                'build' => function (
                    string $sprykMode,
                    OpenApi $openApi,
                    OpenApiRequestTransfer $openApiRequestTransfer,
                    OpenApiResponseTransfer $openApiResponseTransfer
                ) {
                    return $openApiResponseTransfer;
                },
            ],
        );

        $factoryStub = Stub::make(SyncApiFactory::class, [
            'createCommandRunner' => $commandRunnerStub,
            'createTransferCommandRunner' => $transferCommandStub,
        ]);

        $facade = new SyncApiFacade();
        $facade->setFactory($factoryStub);

        $buildFromOpenApiConsole = new OpenApiCodeGenerateConsole();
        $buildFromOpenApiConsole->setFacade($facade);

        return $buildFromOpenApiConsole;
    }
}
