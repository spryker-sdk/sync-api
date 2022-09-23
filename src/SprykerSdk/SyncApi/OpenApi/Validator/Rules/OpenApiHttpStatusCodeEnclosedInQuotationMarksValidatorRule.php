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
 * This rule ensures that OAS since v3 expect response codes to be enclosed in quotation marks.
 * We want to make sure that if any tool in the future decides to enforce this behavior our API schemas are still valid.
 *
 * Currently, none of the tools breaks but some already print warnings about missing quotes.
 */
class OpenApiHttpStatusCodeEnclosedInQuotationMarksValidatorRule implements ValidatorRuleInterface
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
     * Validates an Open API specification v3 schema has status codes enclosed in quotation marks.
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
        // Skip validation for version lower than 3.0.0
        if (!version_compare($openApi['openapi'], '3.0.0', '>=')) {
            return $validateResponseTransfer;
        }

        return $this->validateEachStatusCodeIsEnclosedInQuotationMarks($openApi, $openApiFileName, $validateResponseTransfer);
    }

    /**
     * @param array $openApi
     * @param array|string $openApiFileName
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    protected function validateEachStatusCodeIsEnclosedInQuotationMarks(
        array $openApi,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer
    ): ValidateResponseTransfer {
        if (!isset($openApi['paths'])) {
            return $validateResponseTransfer;
        }

        $fileContent = file_get_contents($openApiFileName);

        foreach ($openApi['paths'] as $path => $pathDefinition) {
            $validateResponseTransfer = $this->validatePathDefinition($path, $pathDefinition, $fileContent, $openApiFileName, $validateResponseTransfer);
        }

        return $validateResponseTransfer;
    }

    /**
     * @param string $path
     * @param array $pathDefinition
     * @param string $fileContent
     * @param string $openApiFileName
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    protected function validatePathDefinition(
        string $path,
        array $pathDefinition,
        string $fileContent,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer
    ): ValidateResponseTransfer {
        foreach ($pathDefinition as $httpMethod => $methodDefinition) {
            if (!isset($methodDefinition['responses'])) {
                continue;
            }

            $validateResponseTransfer = $this->validateResponseDefinition($path, $httpMethod, $methodDefinition, $fileContent, $openApiFileName, $validateResponseTransfer);
        }

        return $validateResponseTransfer;
    }

    /**
     * @param string $path
     * @param string $httpMethod
     * @param array $methodDefinition
     * @param string $openApiFileName
     * @param string $fileContent
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    protected function validateResponseDefinition(
        string $path,
        string $httpMethod,
        array $methodDefinition,
        string $fileContent,
        string $openApiFileName,
        ValidateResponseTransfer $validateResponseTransfer
    ): ValidateResponseTransfer {
        foreach ($methodDefinition['responses'] as $httpStatusCode => $responseDefinition) {
            // PHP converts numeric strings into an int when using it as array key.
            // Because of that, we can not use the parsed file to ensure that status codes enclosed in quotation
            // marks. The pattern is used to clearly identify the exact position where status codes are not in
            // quotation marks.
            $pattern = sprintf('@%s:(\s*)%s:(\s*)responses:(\s*)[0-9]{3}:@', $path, $httpMethod);

            if (preg_match($pattern, $fileContent)) {
                $validateResponseTransfer->addError($this->messageBuilder->buildMessage(SyncApiError::openApiHttpStatusCodeIsNotEnclosedInQuotationMarks($path, $httpStatusCode, $httpMethod, $openApiFileName)));
            }
        }

        return $validateResponseTransfer;
    }
}
