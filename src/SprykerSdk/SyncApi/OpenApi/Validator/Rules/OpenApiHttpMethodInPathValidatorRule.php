<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Validator\Rules;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ValidateResponseTransfer;
use SprykerSdk\SyncApi\Messages\SyncApiMessages;
use SprykerSdk\SyncApi\SyncApiConfig;
use SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface;

class OpenApiHttpMethodInPathValidatorRule implements ValidatorRuleInterface
{
    /**
     * @var array
     */
    protected const HTTP_METHODS = [
        'get',
        'post',
        'patch',
        'delete',
    ];

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected SyncApiConfig $config;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     */
    public function __construct(SyncApiConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Validates the schema for existence of HTTP methods in paths.
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
        return $this->validateEachPathHasAtLeastOneHttpMethod($openApi, $validateResponseTransfer);
    }

    /**
     * @param array $openApi
     * @param \Generated\Shared\Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    protected function validateEachPathHasAtLeastOneHttpMethod(array $openApi, ValidateResponseTransfer $validateResponseTransfer): ValidateResponseTransfer
    {
        if (!isset($openApi['paths'])) {
            return $validateResponseTransfer;
        }

        foreach ($openApi['paths'] as $path => $pathDefinition) {
            foreach ($pathDefinition as $httpMethod => $methodDefinition) {
                if (!in_array($httpMethod, static::HTTP_METHODS)) {
                    $validateResponseTransfer->addError((new MessageTransfer())->setMessage(SyncApiMessages::validationErrorInvalidHttpMethodInPath($path, $httpMethod)));
                }
            }
        }

        return $validateResponseTransfer;
    }
}
