<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Helper;

use Codeception\Module;
use Generated\Shared\Transfer\ValidateRequestTransfer;
use Generated\Shared\Transfer\ValidateResponseTransfer;

class ValidatorHelper extends Module
{
    use SyncApiHelperTrait;

    /**
     * @return void
     */
    public function haveValidConfigurations(): void
    {
        $structure = $this->getValidBaseStructure();

        $this->getSyncApiHelper()->mockDirectoryStructure($structure);
    }

    /**
     * @return array<array<array<\array>>>
     */
    protected function getValidBaseStructure(): array
    {
        return [
            'config' => [
                'api' => [
                    'asyncapi' => [
                        'asyncapi.yml' => file_get_contents(codecept_data_dir('api/asyncapi/valid/base_asyncapi.schema.yml')),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \Generated\Shared\Transfer\ValidateRequestTransfer
     */
    public function haveValidateRequest(): ValidateRequestTransfer
    {
        $config = $this->getSyncApiHelper()->getConfig();

        $validateRequest = new ValidateRequestTransfer();
        $validateRequest->setAsyncApiFile($config->getDefaultAsyncApiFile());

        return $validateRequest;
    }

    /**
     * @param \Generated\Shared\Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return array
     */
    public function getMessagesFromValidateResponseTransfer(ValidateResponseTransfer $validateResponseTransfer): array
    {
        $messages = [];

        foreach ($validateResponseTransfer->getErrors() as $messageTransfer) {
            $messages[] = $messageTransfer->getMessage();
        }

        return $messages;
    }
}
