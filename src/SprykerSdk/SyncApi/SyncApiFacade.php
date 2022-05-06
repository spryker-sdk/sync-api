<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

use Generated\Shared\Transfer\OpenApiRequestTransfer;
use Generated\Shared\Transfer\OpenApiResponseTransfer;
use Generated\Shared\Transfer\ValidateRequestTransfer;
use Generated\Shared\Transfer\ValidateResponseTransfer;

class SyncApiFacade implements SyncApiFacadeInterface
{
    /**
     * @var \SprykerSdk\SyncApi\SyncApiFactory|null
     */
    protected ?SyncApiFactory $factory = null;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiFactory $factory
     *
     * @return void
     */
    public function setFactory(SyncApiFactory $factory): void
    {
        $this->factory = $factory;
    }

    /**
     * @return \SprykerSdk\SyncApi\SyncApiFactory
     */
    protected function getFactory(): SyncApiFactory
    {
        if (!$this->factory) {
            $this->factory = new SyncApiFactory();
        }

        return $this->factory;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OpenApiResponseTransfer
     */
    public function buildFromOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer
    {
        return $this->getFactory()->createOpenApiCodeBuilder()->build($openApiRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ValidateRequestTransfer $validateRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    public function validateOpenApi(ValidateRequestTransfer $validateRequestTransfer): ValidateResponseTransfer
    {
        return $this->getFactory()->createOpenApiValidator()->validate($validateRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OpenApiResponseTransfer
     */
    public function createOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer
    {
        return $this->getFactory()->createOpenApiBuilder()->createOpenApi($openApiRequestTransfer);
    }
}
