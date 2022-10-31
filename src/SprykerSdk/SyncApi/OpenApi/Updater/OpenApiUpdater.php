<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\Writer;
use SprykerSdk\SyncApi\Exception\OpenApiFileReadException;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface;
use SprykerSdk\SyncApi\OpenApi\Reader\OpenApiReaderInterface;
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
     * @var \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface
     */
    protected MergerInterface $openApiMerger;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Reader\OpenApiReaderInterface
     */
    protected OpenApiReaderInterface $openApiReader;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\SyncApiConfig $syncApiConfig
     * @param \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface $openApiMerger
     * @param \SprykerSdk\SyncApi\OpenApi\Reader\OpenApiReaderInterface $openApiReader
     */
    public function __construct(
        MessageBuilderInterface $messageBuilder,
        SyncApiConfig $syncApiConfig,
        MergerInterface $openApiMerger,
        OpenApiReaderInterface $openApiReader
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->syncApiConfig = $syncApiConfig;
        $this->openApiMerger = $openApiMerger;
        $this->openApiReader = $openApiReader;
    }

    /**
     * @param \Transfer\UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): OpenApiResponseTransfer
    {
        try {
            $openApiErrorResponseTransfer = $this->validateSourceOpenApi($updateOpenApiRequestTransfer);

            if ($openApiErrorResponseTransfer) {
                return $openApiErrorResponseTransfer;
            }

            $sourceOpenApi = $this->getSourceOpenApi($updateOpenApiRequestTransfer);

            $syncApiTargetFilepath = $this->getSyncApiTargetFilepath($updateOpenApiRequestTransfer);

            $targetOpenApi = $this->openApiMerger->merge($this->getTargetOpenApi($syncApiTargetFilepath), $sourceOpenApi);

            $this->saveTargetOpenApi($syncApiTargetFilepath, $targetOpenApi);
        } catch (Throwable $throwable) {
            return $this->createErrorResponse($throwable->getMessage());
        }

        return $this->createSuccessResponse($syncApiTargetFilepath);
    }

    /**
     * @param \Transfer\UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer|null
     */
    protected function validateSourceOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): ?OpenApiResponseTransfer
    {
        if (!$updateOpenApiRequestTransfer->getOpenApiDoc() && !$updateOpenApiRequestTransfer->getOpenApiDocFile()) {
            return $this->createValidationErrorMessage('No source OpenApi data provided');
        }

        if ($updateOpenApiRequestTransfer->getOpenApiDoc()) {
            if (!$this->isJsonValid($updateOpenApiRequestTransfer->getOpenApiDocOrFail())) {
                return $this->createValidationErrorMessage('Provided JSON data is invalid');
            }
        }

        if ($updateOpenApiRequestTransfer->getOpenApiDocFile()) {
            if (!file_exists($this->getFilePath(
                $updateOpenApiRequestTransfer->getProjectRootOrFail(),
                $updateOpenApiRequestTransfer->getOpenApiDocFileOrFail())
            )) {
                return $this->createValidationErrorMessage('Provided OpenAPI file does not exist');
            }
        }

        return null;
    }

    /**
     * @param \Transfer\UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function getSourceOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): OpenApi
    {
        if ($updateOpenApiRequestTransfer->getOpenApiDoc()) {
            return $this->openApiReader->readOpenApiFromJsonString($updateOpenApiRequestTransfer->getOpenApiDocOrFail());
        }

        if ($updateOpenApiRequestTransfer->getOpenApiDocFile()) {
            return $this->openApiReader->readOpenApiFromFile($updateOpenApiRequestTransfer->getOpenApiDocFileOrFail());
        }

        throw new OpenApiFileReadException('No OpenAPI data provided for update');
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
        if ($rootDirectory === '') {
            return $fileName;
        }

        return $rootDirectory . DIRECTORY_SEPARATOR . $fileName;
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
