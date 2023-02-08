<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\Message;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Message\SyncApiMessageFormatter;

/**
 * @group SyncApiMessageFormatterTest
 */
class SyncApiMessageFormatterTest extends Unit
{
    /**
     * @var string
     */
    protected const MESSAGE = 'The text "placeholder" rest of the text';

    /**
     * @return void
     */
    public function testFormatterFormatsMessageTextGreenAndPlaceholderValueInYellowOnNonWindowsCli(): void
    {
        $this->assertSame($this->getExpectedFormattedMessage(), SyncApiMessageFormatter::format(static::MESSAGE));
    }

    /**
     * @return string
     */
    protected function getExpectedFormattedMessage(): string
    {
        if (!$this->supportsColoring()) {
            return static::MESSAGE;
        }

        return "\033[32mThe text \033[0m\033[33mplaceholder\033[0m\033[32m rest of the text";
    }

    /**
     * @return bool
     */
    protected function supportsColoring(): bool
    {
        return getenv('TERM') === 'xterm-color' || getenv('TERM') === 'xterm-256color' || strtolower(substr(PHP_OS, 0, 3)) !== 'win';
    }
}
