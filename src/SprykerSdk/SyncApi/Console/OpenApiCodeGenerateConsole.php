<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Transfer\OpenApiRequestTransfer;

class OpenApiCodeGenerateConsole extends AbstractConsole
{
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
    public const OPTION_APPLICATION_TYPE = 'application-type';

    /**
     * @var string
     */
    public const OPTION_APPLICATION_TYPE_SHORT = 't';

    /**
     * @var string
     */
    public const OPTION_ORGANIZATION = 'organization';

    /**
     * @var string
     */
    public const OPTION_ORGANIZATION_SHORT = 'o';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('code:openapi:generate')
            ->setDescription('Generates code from an OpenAPI file definition.')
            ->addOption(static::OPTION_OPEN_API_FILE, static::OPTION_OPEN_API_FILE_SHORT, InputOption::VALUE_REQUIRED, '', $this->getConfig()->getDefaultRelativePathToOpenApiFile())
            ->addOption(static::OPTION_APPLICATION_TYPE, static::OPTION_APPLICATION_TYPE_SHORT, InputOption::VALUE_REQUIRED, '', 'backend')
            ->addOption(static::OPTION_ORGANIZATION, static::OPTION_ORGANIZATION_SHORT, InputOption::VALUE_REQUIRED, 'Namespace that should be used for the code builder. When set to Spryker code will be generated in the core modules.', 'App');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $openApiRequestTransfer = new OpenApiRequestTransfer();
        $openApiRequestTransfer
            ->setTargetFile($input->getOption(static::OPTION_OPEN_API_FILE))
            ->setApplicationType($input->getOption(static::OPTION_APPLICATION_TYPE))
            ->setOrganization($input->getOption(static::OPTION_ORGANIZATION))
            ->setIsVerbose($output->isVeryVerbose());

        $openApiResponseTransfer = $this->getFacade()->buildFromOpenApi($openApiRequestTransfer);

        if ($openApiResponseTransfer->getErrors()->count() === 0) {
            $this->printMessages($output, $openApiResponseTransfer->getMessages());

            return static::CODE_SUCCESS;
        }

        $this->printMessages($output, $openApiResponseTransfer->getErrors());

        return static::CODE_ERROR;
    }
}
