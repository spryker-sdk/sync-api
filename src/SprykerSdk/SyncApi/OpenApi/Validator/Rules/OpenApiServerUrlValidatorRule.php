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

/**
 * Some tools are not replacing double quotes and result in issues when sending requests with double slashes to our application.
 */
class OpenApiServerUrlValidatorRule implements ValidatorRuleInterface
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
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     * @param array|null $context
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    public function validate(
        array $openApi,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer,
        ?array $context = null
    ): ValidateResponseTransfer {
        return $this->validateServerUrlHasNoTrailingSlash($openApi, $openApiFileName, $validateResponseTransfer);
    }

    /**
     * @param array $openApi
     * @param string $openApiFileName
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    protected function validateServerUrlHasNoTrailingSlash(
        array $openApi,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer
    ): ValidateResponseTransfer {
        if (!isset($openApi['servers'])) {
            return $validateResponseTransfer;
        }

        foreach ($openApi['servers'] as $serverDefinition) {
            if (isset($serverDefinition['url']) && rtrim($serverDefinition['url'], '/') !== $serverDefinition['url']) {
                $validateResponseTransfer->addError(
                    $this->messageBuilder->buildMessage(SyncApiError::openApiServerUrlMustNotHaveATrailingASlash($serverDefinition['url'], $openApiFileName)),
                );
            }
        }

        return $validateResponseTransfer;
    }
}
