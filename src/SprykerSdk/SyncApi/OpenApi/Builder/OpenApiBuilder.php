<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder;

use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use Symfony\Component\Yaml\Yaml;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;

class OpenApiBuilder implements OpenApiBuilderInterface
{
    /**
     * @var \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    protected MessageBuilderInterface $messageBuilder;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     */
    public function __construct(MessageBuilderInterface $messageBuilder)
    {
        $this->messageBuilder = $messageBuilder;
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

        $result = $this->writeToFile($targetFile, $openApi);

        if ($result) {
            $openApiResponseTransfer->addMessage($this->messageBuilder->buildMessage(SyncApiInfo::openApiFileCreated($targetFile)));
        }

        return $openApiResponseTransfer;
    }

    /**
     * @param string $targetFile
     * @param array $openApi
     *
     * @return bool
     */
    protected function writeToFile(string $targetFile, array $openApi): bool
    {
        // inline: 100 is set as we want to allow deep nesting, a lower value would break the YML style we want.
        $openApiSchemaYaml = Yaml::dump($openApi, 100);

        $dirname = dirname($targetFile);

        if (!is_dir($dirname)) {
            // permissions: 0770 is an agreed value don't change this
            mkdir($dirname, 0770, true);
        }

        return (bool)file_put_contents($targetFile, $openApiSchemaYaml);
    }
}
