<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Validator;

use Transfer\ValidateRequestTransfer;
use Transfer\ValidateResponseTransfer;

interface ValidatorInterface
{
    /**
     * @param \Transfer\ValidateRequestTransfer $validateRequestTransfer
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    public function validate(
        ValidateRequestTransfer $validateRequestTransfer
    ): ValidateResponseTransfer;
}
