<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;
use Transfer\UpdateOpenApiRequestTransfer;
use Transfer\ValidateRequestTransfer;
use Transfer\ValidateResponseTransfer;

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
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
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
     * @param \Transfer\ValidateRequestTransfer $validateRequestTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
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
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function createOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer
    {
        return $this->getFactory()->createOpenApiBuilder()->createOpenApi($openApiRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Transfer\UpdateOpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer
    {
        return $this->getFactory()->createOpenApiUpdater()->updateOpenApi($openApiRequestTransfer);
    }
}
