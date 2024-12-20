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

class OpenApiHttpMethodInPathValidatorRule implements ValidatorRuleInterface
{
    /**
     * @var array<int, string>
     */
    protected const HTTP_METHODS = [
        'get',
        'post',
        'patch',
        'delete',
        'put',
    ];

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
     * Validates the schema for existence of HTTP methods in paths.
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
        return $this->validateEachPathHasAtLeastOneHttpMethod($openApi, $validateResponseTransfer);
    }

    /**
     * @param array $openApi
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    protected function validateEachPathHasAtLeastOneHttpMethod(array $openApi, ValidateResponseTransfer $validateResponseTransfer): ValidateResponseTransfer
    {
        if (!isset($openApi['paths'])) {
            return $validateResponseTransfer;
        }

        foreach ($openApi['paths'] as $path => $pathDefinition) {
            foreach ($pathDefinition as $httpMethod => $methodDefinition) {
                if (!in_array($httpMethod, static::HTTP_METHODS)) {
                    $validateResponseTransfer->addError($this->messageBuilder->buildMessage(SyncApiError::openApiContainsInvalidHttpMethodForPath($httpMethod, $path)));
                }
            }
        }

        return $validateResponseTransfer;
    }
}
