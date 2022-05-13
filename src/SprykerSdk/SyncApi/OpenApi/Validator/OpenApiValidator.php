<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Validator;

use Exception;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ValidateRequestTransfer;
use Generated\Shared\Transfer\ValidateResponseTransfer;
use SprykerSdk\SyncApi\Messages\SyncApiMessages;
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
            $validateResponseTransfer->addError((new MessageTransfer())->setMessage(SyncApiMessages::errorMessageOpenApiFileDoesNotExist($openApiFile)));

            return $validateResponseTransfer;
        }

        try {
            $openApi = Yaml::parseFile($openApiFile);
        } catch (Exception $e) {
            $validateResponseTransfer->addError((new MessageTransfer())->setMessage(SyncApiMessages::errorMessageCouldNotParseOpenApiFile($openApiFile)));

            return $validateResponseTransfer;
        }

        $validateResponseTransfer = $this->executeValidatorRules($openApi, $openApiFile, $validateResponseTransfer);

        if ($validateResponseTransfer->getErrors()->count() === 0) {
            $validateResponseTransfer->addMessage((new MessageTransfer())->setMessage(SyncApiMessages::VALIDATOR_MESSAGE_OPEN_API_SUCCESS));
        }

        return $validateResponseTransfer;
    }
}
