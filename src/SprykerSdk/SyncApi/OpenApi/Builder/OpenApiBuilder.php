<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\OpenApiRequestTransfer;
use Generated\Shared\Transfer\OpenApiResponseTransfer;
use Symfony\Component\Yaml\Yaml;

class OpenApiBuilder implements OpenApiBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OpenApiResponseTransfer
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

        $targetFilePath = $openApiRequestTransfer->getTargetFileOrFail();

        if (file_exists($targetFilePath)) {
            $openApiResponseTransfer->addError((new MessageTransfer())->setMessage(sprintf('File "%s" already exists.', $targetFilePath)));

            return $openApiResponseTransfer;
        }

        $this->writeToFile($targetFilePath, $openApi);

        return $openApiResponseTransfer;
    }

    /**
     * @param string $targetFile
     * @param array $openApi
     *
     * @return void
     */
    protected function writeToFile(string $targetFile, array $openApi): void
    {
        $openApiSchemaYaml = Yaml::dump($openApi, 100);

        $dirname = dirname($targetFile);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0770, true);
        }

        file_put_contents($targetFile, $openApiSchemaYaml);
    }
}
