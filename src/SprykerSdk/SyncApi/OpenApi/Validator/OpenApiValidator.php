<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Validator;

use Exception;
use Generated\Shared\Transfer\ValidateRequestTransfer;
use Generated\Shared\Transfer\ValidateResponseTransfer;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\Validator\AbstractValidator;
use Symfony\Component\Yaml\Yaml;

class OpenApiValidator extends AbstractValidator
{
    /**
     * @param \Generated\Shared\Transfer\ValidateRequestTransfer $validateRequestTransfer
     * @param \Generated\Shared\Transfer\ValidateResponseTransfer|null $validateResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    public function validate(
        ValidateRequestTransfer $validateRequestTransfer,
        ?ValidateResponseTransfer $validateResponseTransfer = null
    ): ValidateResponseTransfer {
        $validateResponseTransfer ??= new ValidateResponseTransfer();
        $openApiFile = $validateRequestTransfer->getOpenApiFileOrFail();

        if (!is_file($openApiFile)) {
            $validateResponseTransfer->addError($this->messageBuilder->buildMessage(SyncApiError::couldNotFinOpenApi($openApiFile)));

            return $validateResponseTransfer;
        }

        try {
            $openApi = Yaml::parseFile($openApiFile);
        } catch (Exception $e) {
            $validateResponseTransfer->addError($this->messageBuilder->buildMessage(SyncApiError::couldNotParseOpenApi($openApiFile)));

            return $validateResponseTransfer;
        }

        $validateResponseTransfer = $this->executeValidatorRules($openApi, $openApiFile, $validateResponseTransfer);

        if ($validateResponseTransfer->getErrors()->count() === 0) {
            $validateResponseTransfer->addMessage($this->messageBuilder->buildMessage(SyncApiInfo::openApiSchemaFileIsValid()));
        }

        return $validateResponseTransfer;
    }
}
