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

interface SyncApiFacadeInterface
{
 /**
  * Specification:
  * - Reads an OpenAPI file and builds code that is required.
  *
  * @api
  *
  * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
  *
  * @return \Generated\Shared\Transfer\OpenApiResponseTransfer
  */
    public function buildFromOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer;

    /**
     * Specification:
     * - Reads an OpenAPI file and validates it.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ValidateRequestTransfer $openApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ValidateResponseTransfer
     */
    public function validateOpenApi(ValidateRequestTransfer $openApiRequestTransfer): ValidateResponseTransfer;

    /**
     * Specification:
     * - Adds an OpenAPI file.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OpenApiResponseTransfer
     */
    public function createOpenApi(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer;
}
