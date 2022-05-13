<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Validator\Rules;

use Generated\Shared\Transfer\ValidateResponseTransfer;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface;

class OpenApiComponentsValidatorRule implements ValidatorRuleInterface
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
     * Validates the schema for existence of components.
     *
     * @param array $openApi
     * @param string $openApiFileName
     * @param \Generated\Shared\Transfer\ValidateResponseTransfer $validateResponseTransfer
     * @param array|null $context
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    public function validate(
        array $openApi,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer,
        ?array $context = null
    ): ValidateResponseTransfer {
        return $this->validateAtLeastOneComponentExists($openApi, $validateResponseTransfer);
    }

    /**
     * @param array $openApi
     * @param \Generated\Shared\Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    protected function validateAtLeastOneComponentExists(array $openApi, ValidateResponseTransfer $validateResponseTransfer): ValidateResponseTransfer
    {
        if (!isset($openApi['components'])) {
            $validateResponseTransfer->addError($this->messageBuilder->buildMessage(SyncApiError::openApiDoesNotDefineAnyComponents()));
        }

        return $validateResponseTransfer;
    }
}
