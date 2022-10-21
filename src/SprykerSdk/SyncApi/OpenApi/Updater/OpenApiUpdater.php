<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\Writer;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\SyncApiConfig;
use Throwable;
use Transfer\OpenApiResponseTransfer;
use Transfer\UpdateOpenApiRequestTransfer;

class OpenApiUpdater implements OpenApiUpdaterInterface
{
    /**
     * @var \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    protected MessageBuilderInterface $messageBuilder;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected SyncApiConfig $syncApiConfig;

    /**
     * @var array<\SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface>
     */
    protected array $mergerCollection;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\SyncApiConfig $syncApiConfig
     * @param array<\SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface> $mergerCollection
     */
    public function __construct(
        MessageBuilderInterface $messageBuilder,
        SyncApiConfig $syncApiConfig,
        array $mergerCollection
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->syncApiConfig = $syncApiConfig;
        $this->mergerCollection = $mergerCollection;
    }

    /**
     * @param \Transfer\UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): OpenApiResponseTransfer
    {
        try {
            if (!$this->isJsonValid($updateOpenApiRequestTransfer->getOpenApiDocOrFail())) {
                return $this->createValidationErrorMessage('Provided JSON is invalid');
            }

            $sourceOpenApi = Reader::readFromJson($updateOpenApiRequestTransfer->getOpenApiDocOrFail());

            $syncApiTargetFilepath = $this->getSyncApiTargetFilepath($updateOpenApiRequestTransfer);

            $targetOpenApi = $this->merge($this->getTargetOpenApi($syncApiTargetFilepath), $sourceOpenApi);

            $this->saveTargetOpenApi($syncApiTargetFilepath, $targetOpenApi);
        } catch (Throwable $throwable) {
            return $this->createErrorResponse($throwable->getMessage());
        }

        return $this->createSuccessResponse($syncApiTargetFilepath);
    }

    /**
     * @param string $getOpenApiDocOrFail
     *
     * @return bool
     */
    protected function isJsonValid(string $getOpenApiDocOrFail): bool
    {
        json_decode($getOpenApiDocOrFail);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * @param \Transfer\UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer
     *
     * @return string
     */
    protected function getSyncApiTargetFilepath(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): string
    {
        return $this->getFilePath(
            $updateOpenApiRequestTransfer->getProjectRootOrFail(),
            $updateOpenApiRequestTransfer->getOpenApiFileOrFail(),
        );
    }

    /**
     * @param string $syncApiTargetFilepath
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function getTargetOpenApi(string $syncApiTargetFilepath): OpenApi
    {
        if (is_file($syncApiTargetFilepath)) {
             return Reader::readFromYamlFile($syncApiTargetFilepath, OpenApi::class, false);
        }

        return Reader::readFromYamlFile(
            $this->getFilePath(
                $this->syncApiConfig->getPackageRootPath(),
                $this->syncApiConfig->getDefaultRelativePathToOpenApiFile(),
            ),
            OpenApi::class,
            false,
        );
    }

    /**
     * @param string $rootDirectory
     * @param string $fileName
     *
     * @return string
     */
    protected function getFilePath(string $rootDirectory, string $fileName): string
    {
        return $rootDirectory . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function merge(OpenApi $targetOpenApi, OpenApi $sourceOpenApi): OpenApi
    {
        foreach ($this->mergerCollection as $merger) {
            $targetOpenApi = $merger->merge($targetOpenApi, $sourceOpenApi);
        }

        return $targetOpenApi;
    }

    /**
     * @param string $syncApiTargetFilepath
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     *
     * @return void
     */
    protected function saveTargetOpenApi(string $syncApiTargetFilepath, OpenApi $targetOpenApi): void
    {
        $this->createFileIfNotExists($syncApiTargetFilepath);

        Writer::writeToYamlFile($targetOpenApi, $syncApiTargetFilepath);
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    protected function createFileIfNotExists(string $fileName): void
    {
        if (!is_dir(dirname($fileName))) {
            mkdir(dirname($fileName), 0755, true);
        }

        file_put_contents($fileName, '');
    }

    /**
     * @param string $errorMessage
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function createValidationErrorMessage(string $errorMessage): OpenApiResponseTransfer
    {
        return (new OpenApiResponseTransfer())->addError(
            $this->messageBuilder->buildMessage(
                SyncApiError::openApiDataIsInvalid(
                    $errorMessage,
                ),
            ),
        );
    }

    /**
     * @param string $errorMessage
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function createErrorResponse(string $errorMessage): OpenApiResponseTransfer
    {
        return (new OpenApiResponseTransfer())->addError(
            $this->messageBuilder->buildMessage(
                SyncApiError::couldNotUpdateOpenApiFile(
                    $errorMessage,
                ),
            ),
        );
    }

    /**
     * @param string $syncApiTargetFilepath
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function createSuccessResponse(string $syncApiTargetFilepath): OpenApiResponseTransfer
    {
        return (new OpenApiResponseTransfer())->addMessage(
            $this->messageBuilder->buildMessage(
                SyncApiInfo::openApiFileUpdated($syncApiTargetFilepath),
            ),
        );
    }
}
