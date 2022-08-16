<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

use Generated\Shared\Transfer\UpdateOpenApiRequestTransfer;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;
use Transfer\ValidateRequestTransfer;
use Transfer\ValidateResponseTransfer;

interface SyncApiFacadeInterface
{
    /**
     * Specification:
     * - Reads an OpenAPI file and builds code that is required.
     *
     * @api
     *
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function buildFromOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer;

    /**
     * Specification:
     * - Reads an OpenAPI file and validates it.
     *
     * @api
     *
     * @param \Transfer\ValidateRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    public function validateOpenApi(ValidateRequestTransfer $openApiRequestTransfer): ValidateResponseTransfer;

    /**
     * Specification:
     * - Adds an OpenAPI file.
     *
     * @api
     *
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function createOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer;

    /**
     * Specification:
     * - Updates on OpenAPI file with provided JSON-ed OpenAPI schema.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UpdateOpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer;
}
