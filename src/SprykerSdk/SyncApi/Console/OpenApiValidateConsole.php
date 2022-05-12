<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Console;

use Generated\Shared\Transfer\ValidateRequestTransfer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OpenApiValidateConsole extends AbstractConsole
{
    /**
     * @var string
     */
    public const OPTION_OPEN_API_FILE = 'openapi-file';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_FILE_SHORT = 'o';

    /**
     * @var string
     */
    public const OPTION_PROJECT_ROOT = 'project-root';

    /**
     * @var string
     */
    public const OPTION_PROJECT_ROOT_SHORT = 'r';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('schema:openapi:validate')
            ->setDescription('Validates an OpenAPI file.')
            ->addOption(static::OPTION_PROJECT_ROOT, static::OPTION_PROJECT_ROOT_SHORT, InputOption::VALUE_REQUIRED, '', getcwd())
            ->addOption(static::OPTION_OPEN_API_FILE, static::OPTION_OPEN_API_FILE_SHORT, InputOption::VALUE_REQUIRED, '', $this->getConfig()->getDefaultRelativePathToOpenApiFile());
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $openApiFile = $input->getOption(static::OPTION_PROJECT_ROOT) . DIRECTORY_SEPARATOR . $input->getOption(static::OPTION_OPEN_API_FILE);
        $validateRequestTransfer = new ValidateRequestTransfer();
        $validateRequestTransfer->setOpenApiFile($openApiFile);

        $validateResponseTransfer = $this->getFacade()->validateOpenApi($validateRequestTransfer);

        if ($validateResponseTransfer->getErrors()->count() === 0) {
            $this->printMessages($output, $validateResponseTransfer->getMessages());

            return static::CODE_SUCCESS;
        }

        $this->printMessages($output, $validateResponseTransfer->getErrors());

        return static::CODE_ERROR;
    }
}
