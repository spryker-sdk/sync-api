<?php

namespace SprykerSdkTest\SyncApi\Message;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\Message\SyncApiMessageFormatter;

/**
 * @group SyncApiMessageFormatterTest
 */
class SyncApiMessageFormatterTest extends Unit
{
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
        if ($this->isWindows()) {
            return static::MESSAGE;
        }

        return "\033[32mThe text \033[0m\033[33mplaceholder\033[0m\033[32m rest of the text";
    }

    /**
     * @return bool
     */
    protected function isWindows(): bool
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
    }
}
