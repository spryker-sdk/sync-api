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
use Transfer\UpdateOpenApiRequestTransfer;

class OpenApiUpdateConsole extends AbstractConsole
{
    /**
     * @var string
     */
    public const ARGUMENT_OPENAPI_DOC = 'openapi-doc';

    /**
     * @var string
     */
    public const ARGUMENT_OPENAPI_DOC_DESCRIPTION = 'A JSON-ed string that contains a valid OpenAPI scheme';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_DOC_FILE = 'openapi-doc-file';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_DOC_FILE_SHORT = 'd';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_DOC_FILE_DESCRIPTION = 'Path to source OpenAPI file. Use this option if you want a provide a source file instead of source JSON. Supports both JSON and YAML files.';

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
    public const OPTION_OPEN_API_FILE_DESCRIPTION = 'Path to target OpenAPI file. Use this option only when your file is not in the default path or has a different name. Defaults to: resources/api/openapi.json';

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
    public const OPTION_PROJECT_ROOT_DESCRIPTION = 'Project root directory. By default project root is retrieved from getcwd() function.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('schema:openapi:update')
            ->setDescription('Updates an OpenAPI file specified in options with provided OpenAPI doc')
            ->addArgument(
                static::ARGUMENT_OPENAPI_DOC,
                InputArgument::OPTIONAL,
                static::ARGUMENT_OPENAPI_DOC_DESCRIPTION,
            )
            ->addOption(
                static::OPTION_OPEN_API_FILE,
                static::OPTION_OPEN_API_FILE_SHORT,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_OPEN_API_FILE_DESCRIPTION,
                $this->getConfig()->getDefaultRelativePathToOpenApiFile(),
            )
            ->addOption(
                static::OPTION_OPEN_API_DOC_FILE,
                static::OPTION_OPEN_API_DOC_FILE_SHORT,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_OPEN_API_DOC_FILE_DESCRIPTION,
            )
            ->addOption(
                static::OPTION_PROJECT_ROOT,
                static::OPTION_PROJECT_ROOT_SHORT,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_PROJECT_ROOT_DESCRIPTION,
                $this->getConfig()->getProjectRootPath(),
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $updateOpenApiRequestTransfer = (new UpdateOpenApiRequestTransfer())
            ->setOpenApiDoc($input->getArgument(static::ARGUMENT_OPENAPI_DOC))
            ->setOpenApiDocFile($input->getOption(static::OPTION_OPEN_API_DOC_FILE))
            ->setOpenApiFile($input->getOption(static::OPTION_OPEN_API_FILE))
            ->setProjectRoot($input->getOption(static::OPTION_PROJECT_ROOT));

        $openApiResponseTransfer = $this->getFacade()->updateOpenApi($updateOpenApiRequestTransfer);

        if ($openApiResponseTransfer->getErrors()->count() === 0) {
            $this->printMessages($output, $openApiResponseTransfer->getMessages());

            return static::CODE_SUCCESS;
        }

        $this->printMessages($output, $openApiResponseTransfer->getErrors());

        return static::CODE_ERROR;
    }
}
