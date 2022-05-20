<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Validator;

use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\SyncApiConfig;
use Transfer\ValidateResponseTransfer;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected SyncApiConfig $config;

    /**
     * @var \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    protected MessageBuilderInterface $messageBuilder;

    /**
     * @var array<\SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface>
     */
    protected array $validatorRules;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param array $fileValidators
     */
    public function __construct(SyncApiConfig $config, MessageBuilderInterface $messageBuilder, array $fileValidators = [])
    {
        $this->config = $config;
        $this->messageBuilder = $messageBuilder;
        $this->validatorRules = $fileValidators;
    }

    /**
     * @param array $fileData
     * @param string $fileName
     * @param \Transfer\ValidateResponseTransfer $validateResponseTransfer
     * @param array|null $context
     *
     * @return \Transfer\ValidateResponseTransfer
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
