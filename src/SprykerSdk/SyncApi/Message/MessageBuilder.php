<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Message;

use Transfer\MessageTransfer;

class MessageBuilder implements MessageBuilderInterface
{
    /**
     * @param string $message
     *
     * @return \Transfer\MessageTransfer
     */
    public function buildMessage(string $message): MessageTransfer
    {
        return (new MessageTransfer())->setMessage($message);
    }
}
