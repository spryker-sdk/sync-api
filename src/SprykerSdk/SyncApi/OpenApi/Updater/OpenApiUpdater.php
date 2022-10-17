<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use ArrayObject;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\Writer;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface;
use SprykerSdk\SyncApi\SyncApiConfig;
use Throwable;
use Transfer\OpenApiResponseTransfer;
use Transfer\UpdateOpenApiRequestTransfer;

class OpenApiUpdater implements OpenApiUpdaterInterface
{
    /**
     * @var \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    protected $messageBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface
     */
    protected $filepathBuilder;

    /**
     * @var array<\SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface>
     */
    protected $mergerCollection;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected $syncApiConfig;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface $filepathBuilder
     * @param \SprykerSdk\SyncApi\SyncApiConfig $syncApiConfig
     * @param array<\SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface> $mergerCollection
     */
    public function __construct(
        MessageBuilderInterface $messageBuilder,
        FilepathBuilderInterface $filepathBuilder,
        SyncApiConfig $syncApiConfig,
        array $mergerCollection
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->filepathBuilder = $filepathBuilder;
        $this->mergerCollection = $mergerCollection;
        $this->syncApiConfig = $syncApiConfig;
    }

    /**
     * @param \Transfer\UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): OpenApiResponseTransfer
    {
        try {
            $sourceOpenApi = Reader::readFromJson($updateOpenApiRequestTransfer->getOpenApiDocOrFail());
        } catch (Throwable $throwable) {
            return (new OpenApiResponseTransfer())
                ->addError($this->messageBuilder->buildMessage($throwable->getMessage()));
        }

        if (!$sourceOpenApi->validate()) {
            return (new OpenApiResponseTransfer())->setErrors(
                new ArrayObject($sourceOpenApi->getErrors()),
            );
        }

        $syncApiTargetFilepath = $this->filepathBuilder->buildSyncApiFilepath(
            $updateOpenApiRequestTransfer->getOpenApiFileOrFail(),
            $updateOpenApiRequestTransfer->getProjectRootOrFail(),
        );

        try {
            if (is_file($syncApiTargetFilepath)) {
                $targetOpenApi = Reader::readFromYamlFile($syncApiTargetFilepath, OpenApi::class, false);
            } else {
                $targetOpenApi = Reader::readFromYamlFile(
                    $this->syncApiConfig->getPackageRootPath() . '/' .
                    $this->syncApiConfig->getDefaultRelativePathToOpenApiFile(),
                    OpenApi::class,
                    false,
                );
            }

            $targetOpenApi = $this->merge($targetOpenApi, $sourceOpenApi);

            $this->createFileIfNotExists($syncApiTargetFilepath);
            Writer::writeToYamlFile($targetOpenApi, $syncApiTargetFilepath);
        } catch (Throwable $throwable) {
            return (new OpenApiResponseTransfer())
                ->addError($this->messageBuilder->buildMessage($throwable->getMessage()));
        }

        return (new OpenApiResponseTransfer())->addMessage($this->messageBuilder->buildMessage('Success!'));
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function merge(
        OpenApi $targetOpenApi,
        OpenApi $sourceOpenApi
    ): OpenApi {
        foreach ($this->mergerCollection as $merger) {
            $targetOpenApi = $merger->merge($targetOpenApi, $sourceOpenApi);
        }

        return $targetOpenApi;
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    protected function createFileIfNotExists(string $fileName): void
    {
        if (!is_file($fileName)) {
            if (!is_dir(dirname($fileName))) {
                mkdir(dirname($fileName), 0755, true);
            }

            file_put_contents($fileName, '');
        }
    }
}
