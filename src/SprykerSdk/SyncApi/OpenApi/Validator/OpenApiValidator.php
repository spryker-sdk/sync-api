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
        if (!$this->finder->hasFiles($openApiFile)) {
            $messageTransfer = new MessageTransfer();
            $messageTransfer->setMessage('No OpenAPI file given, you need to pass a valid filename.');
            $validateResponseTransfer->addError($messageTransfer);

            return $validateResponseTransfer;
        }

        try {
            $openApi = Yaml::parseFile($openApiFile);
        } catch (Exception $e) {
            $messageTransfer = new MessageTransfer();
            $messageTransfer->setMessage('Could not parse OpenApi file.');
            $validateResponseTransfer->addError($messageTransfer);

            return $validateResponseTransfer;
        }

        return $this->validateFileData($openApi, $this->finder->getFile($openApiFile)->getFilename(), $validateResponseTransfer);
    }
}
