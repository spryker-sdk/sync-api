<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder;

use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;

class OpenApiBuilder implements OpenApiBuilderInterface
{
    /**
     * @var \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    protected $messageBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface
     */
    protected $openApiFileManager;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface $openApiFileManager
     */
    public function __construct(
        MessageBuilderInterface $messageBuilder,
        OpenApiFileManagerInterface $openApiFileManager
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->openApiFileManager = $openApiFileManager;
    }

    /**
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function createOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer
    {
        $openApiResponseTransfer = new OpenApiResponseTransfer();

        $openApi = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => $openApiRequestTransfer->getOpenApiOrFail()->getTitleOrFail(),
                'version' => $openApiRequestTransfer->getOpenApiOrFail()->getVersionOrFail(),
            ],
        ];

        $targetFile = $openApiRequestTransfer->getTargetFileOrFail();

        if (file_exists($targetFile)) {
            $openApiResponseTransfer->addError($this->messageBuilder->buildMessage(SyncApiError::openApiFileAlreadyExists($targetFile)));

            return $openApiResponseTransfer;
        }

        $result = $this->openApiFileManager->saveOpenApiFileFromArray($targetFile, $openApi);

        if ($result) {
            $openApiResponseTransfer->addMessage($this->messageBuilder->buildMessage(SyncApiInfo::openApiFileCreated($targetFile)));
        }

        return $openApiResponseTransfer;
    }
}
