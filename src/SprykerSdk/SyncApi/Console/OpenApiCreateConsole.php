<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiTransfer;

class OpenApiCreateConsole extends AbstractConsole
{
    /**
     * @var string
     */
    public const ARGUMENT_TITLE = 'title';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_FILE = 'openapi-file';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_FILE_SHORT = 'f';

    /**
     * @var string
     */
    public const OPTION_PROJECT_ROOT = 'project-root';

    /**
     * @var string
     */
    public const OPTION_PROJECT_ROOT_SHORT = 'r';

    /**
     * @var string
     */
    public const OPTION_API_VERSION = 'api-version';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('schema:openapi:create')
            ->setDescription('Adds an OpenAPI file to the specified Open API schema file path.')
            ->addArgument(static::ARGUMENT_TITLE, InputArgument::REQUIRED, 'The name of the App.')
            ->addOption(static::OPTION_PROJECT_ROOT, static::OPTION_PROJECT_ROOT_SHORT, InputOption::VALUE_REQUIRED, '', getcwd())
            ->addOption(static::OPTION_OPEN_API_FILE, static::OPTION_OPEN_API_FILE_SHORT, InputOption::VALUE_REQUIRED, '', $this->getConfig()->getDefaultRelativePathToOpenApiFile())
            ->addOption(static::OPTION_API_VERSION, null, InputOption::VALUE_REQUIRED, 'Version number of the OpenAPI schema. Defaults to 0.1.0', '0.1.0');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $openApiTransfer = new OpenApiTransfer();
        $openApiTransfer
            ->setTitle($input->getArgument(static::ARGUMENT_TITLE))
            ->setVersion($input->getOption(static::OPTION_API_VERSION));

        $targetFile = $input->getOption(static::OPTION_PROJECT_ROOT) . DIRECTORY_SEPARATOR . $input->getOption(static::OPTION_OPEN_API_FILE);

        $openApiRequestTransfer = new OpenApiRequestTransfer();
        $openApiRequestTransfer
            ->setTargetFile($targetFile)
            ->setProjectRoot($input->getOption(static::OPTION_PROJECT_ROOT))
            ->setOpenApi($openApiTransfer);

        $openApiResponseTransfer = $this->getFacade()->createOpenApi($openApiRequestTransfer);

        if ($openApiResponseTransfer->getErrors()->count() === 0) {
            $this->printMessages($output, $openApiResponseTransfer->getMessages());

            return static::CODE_SUCCESS;
        }

        $this->printMessages($output, $openApiResponseTransfer->getErrors());

        return static::CODE_ERROR;
    }
}
