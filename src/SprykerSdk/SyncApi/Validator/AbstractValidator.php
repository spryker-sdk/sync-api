<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Validator;

use Generated\Shared\Transfer\ValidateResponseTransfer;
use SprykerSdk\SyncApi\SyncApiConfig;
use SprykerSdk\SyncApi\Validator\Finder\FinderInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected SyncApiConfig $config;

    /**
     * @var \SprykerSdk\SyncApi\Validator\Finder\FinderInterface
     */
    protected FinderInterface $finder;

    /**
     * @var array<\SprykerSdk\SyncApi\Validator\FileValidatorInterface>
     */
    protected array $fileValidators;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     * @param \SprykerSdk\SyncApi\Validator\Finder\FinderInterface $finder
     * @param array $fileValidators
     */
    public function __construct(SyncApiConfig $config, FinderInterface $finder, array $fileValidators = [])
    {
        $this->config = $config;
        $this->finder = $finder;
        $this->fileValidators = $fileValidators;
    }

    /**
     * @param array $fileData
     * @param string $fileName
     * @param \Generated\Shared\Transfer\ValidateResponseTransfer $validateResponseTransfer
     * @param array|null $context
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    protected function validateFileData(
        array $fileData,
        string $fileName,
        ValidateResponseTransfer $validateResponseTransfer,
        ?array $context = null
    ): ValidateResponseTransfer {
        foreach ($this->fileValidators as $fileValidator) {
            $validateResponseTransfer = $fileValidator->validate($fileData, $fileName, $validateResponseTransfer, $context);
        }

        return $validateResponseTransfer;
    }
}
