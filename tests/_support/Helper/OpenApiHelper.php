<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Helper;

use Codeception\Module;
use Codeception\Stub;
use Codeception\Stub\Expected;
use Doctrine\Inflector\InflectorFactory;
use SprykerSdk\SyncApi\Console\OpenApiCodeGenerateConsole;
use SprykerSdk\SyncApi\Message\MessageBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilder;
use SprykerSdk\SyncApi\SyncApiConfig;
use SprykerSdk\SyncApi\SyncApiFacade;
use SprykerSdk\SyncApi\SyncApiFactory;
use Symfony\Component\Yaml\Yaml;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiTransfer;
use Transfer\UpdateOpenApiRequestTransfer;

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
        $openApiCodeBuilderStub = Stub::construct(
            OpenApiCodeBuilder::class,
            [
                new SyncApiConfig(),
                new MessageBuilder(),
                InflectorFactory::create()->build(),
            ],
            [
                'runProcess' => Expected::atLeastOnce(),
            ],
        );
        $factoryStub = Stub::make(SyncApiFactory::class, [
            'createOpenApiCodeBuilder' => $openApiCodeBuilderStub,
        ]);
        $facade = new SyncApiFacade();
        $facade->setFactory($factoryStub);

        $buildFromOpenApiConsole = new OpenApiCodeGenerateConsole();
        $buildFromOpenApiConsole->setFacade($facade);

        return $buildFromOpenApiConsole;
    }

    /**
     * @return \Transfer\UpdateOpenApiRequestTransfer
     */
    public function haveUpdateExistedFileRequest(): UpdateOpenApiRequestTransfer
    {
        $updateOpenApiRequestTransfer = new UpdateOpenApiRequestTransfer();

        $config = $this->getSyncApiHelper()->getConfig();

        $updateOpenApiRequestTransfer
            ->setProjectRoot($config->getProjectRootPath())
            ->setOpenApiFile('resources/api/openapi.yml')
            ->setOpenApiDoc(json_encode(Yaml::parseFile(codecept_data_dir('api/valid/valid_openapi.yml'))));

        return $updateOpenApiRequestTransfer;
    }

    /**
     * @return \Transfer\UpdateOpenApiRequestTransfer
     */
    public function haveUpdateNotExistedFileRequest(): UpdateOpenApiRequestTransfer
    {
        $updateOpenApiRequestTransfer = new UpdateOpenApiRequestTransfer();

        $config = $this->getSyncApiHelper()->getConfig();

        $updateOpenApiRequestTransfer
            ->setProjectRoot($config->getProjectRootPath())
            ->setOpenApiFile('new_file.yml')
            ->setOpenApiDoc(
                json_encode(
                    Yaml::parseFile(codecept_data_dir('api/invalid/invalid_openapi.yml')),
                ),
            );

        return $updateOpenApiRequestTransfer;
    }
}
