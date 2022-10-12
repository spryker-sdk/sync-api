<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Console;

use Transfer\UpdateOpenApiRequestTransfer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
    public const OPTION_OPEN_API_FILE = 'openapi-file';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_FILE_SHORT = 'f';

    /**
     * @var string
     */
    public const OPTION_OPEN_API_FILE_DESCRIPTION = 'Path to target OpenAPI file. The openapi.yml template will be taken by default';

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
    public const OPTION_PROJECT_ROOT_DESCRIPTION = 'Project root directory. By default project root retrieved from getcwd() function.';

    /**
     * @var string
     */
    public const OPTION_NOT_VALIDATE_OPENAPI_DOC = 'not-validate';

    /**
     * @var string
     */
    public const OPTION_NOT_VALIDATE_OPENAPI_DOC_SHORT = 's';

    /**
     * @var string
     */
    public const OPTION_NOT_VALIDATE_OPENAPI_DOC_DESCRIPTION = 'You can disable validation of OpenAPI doc string. Usage of this option is not recommended.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('schema:openapi:update')
            ->setDescription('Updates an OpenAPI file specified in options with provided OpenAPI doc')
            ->addArgument(
                static::ARGUMENT_OPENAPI_DOC,
                InputArgument::REQUIRED,
                static::ARGUMENT_OPENAPI_DOC_DESCRIPTION
            )
            ->addOption(
                static::OPTION_OPEN_API_FILE,
                static::OPTION_OPEN_API_FILE_SHORT,
                InputOption::VALUE_REQUIRED,
                static::OPTION_OPEN_API_FILE_DESCRIPTION,
    )
            ->addOption(
                static::OPTION_PROJECT_ROOT,
                static::OPTION_PROJECT_ROOT_SHORT,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_PROJECT_ROOT_DESCRIPTION,
                getcwd()
            )
            ->addOption(
                static::OPTION_NOT_VALIDATE_OPENAPI_DOC,
                static::OPTION_NOT_VALIDATE_OPENAPI_DOC_SHORT,
                InputOption::VALUE_OPTIONAL,
                static::OPTION_NOT_VALIDATE_OPENAPI_DOC_DESCRIPTION,
                false
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
            ->setOpenApiFile($input->getOption(static::OPTION_OPEN_API_FILE))
            ->setProjectRoot($input->getOption(static::OPTION_PROJECT_ROOT))
            ->setIsValidate(($input->getOption(static::OPTION_NOT_VALIDATE_OPENAPI_DOC) !== null));

        $openApiResponseTransfer = $this->getFacade()->updateOpenApi($updateOpenApiRequestTransfer);

        if ($openApiResponseTransfer->getErrors()->count() === 0) {
            $this->printMessages($output, $openApiResponseTransfer->getMessages());

            return static::CODE_SUCCESS;
        }

        $this->printMessages($output, $openApiResponseTransfer->getErrors());

        return static::CODE_ERROR;
    }
}
