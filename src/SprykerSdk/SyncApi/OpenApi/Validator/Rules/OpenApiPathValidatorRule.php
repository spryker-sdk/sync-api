<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Validator\Rules;

use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface;
use Transfer\ValidateResponseTransfer;

class OpenApiPathValidatorRule implements ValidatorRuleInterface
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
     * Validates the schema for existence of paths.
     *
     * @param array<string, string|array<string, string>> $openApi
     * @param string $openApiFileName
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     * @param array<string, string>|null $context
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    public function validate(
        array $openApi,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer,
        ?array $context = null
    ): ValidateResponseTransfer {
        return $this->validateAtLeastOnePathExists($openApi, $openApiFileName, $validateResponseTransfer);
    }

    /**
     * @param array<string, string|array<string, string>> $openApi
     * @param string $openApiFileName
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    protected function validateAtLeastOnePathExists(
        array $openApi,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer
    ): ValidateResponseTransfer {
        if (!isset($openApi['paths'])) {
            $validateResponseTransfer->addError(
                $this->messageBuilder->buildMessage(SyncApiError::openApiDoesNotDefineAnyPath($openApiFileName)),
            );
        }

        return $validateResponseTransfer;
    }
}
