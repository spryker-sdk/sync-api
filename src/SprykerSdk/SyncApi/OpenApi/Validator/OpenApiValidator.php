<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Validator;

use Exception;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\Validator\AbstractValidator;
use Symfony\Component\Yaml\Yaml;
use Transfer\ValidateRequestTransfer;
use Transfer\ValidateResponseTransfer;

class OpenApiValidator extends AbstractValidator
{
    /**
     * @param \Transfer\ValidateRequestTransfer $validateRequestTransfer
     * @param \Transfer\ValidateResponseTransfer|null $validateResponseTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
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
