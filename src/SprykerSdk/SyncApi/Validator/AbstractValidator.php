<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Validator;

use Generated\Shared\Transfer\ValidateResponseTransfer;
use SprykerSdk\SyncApi\SyncApiConfig;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected SyncApiConfig $config;

    /**
     * @var array<\SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface>
     */
    protected array $validatorRules;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     * @param array $fileValidators
     */
    public function __construct(SyncApiConfig $config, array $fileValidators = [])
    {
        $this->config = $config;
        $this->validatorRules = $fileValidators;
    }

    /**
     * @param array $fileData
     * @param string $fileName
     * @param \Generated\Shared\Transfer\ValidateResponseTransfer $validateResponseTransfer
     * @param array|null $context
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    protected function executeValidatorRules(
        array $fileData,
        string $fileName,
        ValidateResponseTransfer $validateResponseTransfer,
        ?array $context = null
    ): ValidateResponseTransfer {
        foreach ($this->validatorRules as $validatorRule) {
            $validateResponseTransfer = $validatorRule->validate($fileData, $fileName, $validateResponseTransfer, $context);
        }

        return $validateResponseTransfer;
    }
}
