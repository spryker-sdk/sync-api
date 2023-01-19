<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Doctrine\Inflector\Inflector;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\SyncApiConfig;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;

class OpenApiCodeBuilder implements OpenApiCodeBuilderInterface
{
    /**
     * @var \Transfer\OpenApiResponseTransfer
     */
    protected OpenApiResponseTransfer $openApiResponseTransfer;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected SyncApiConfig $config;

    /**
     * @var \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    protected MessageBuilderInterface $messageBuilder;

    /**
     * @var \Doctrine\Inflector\Inflector
     */
    protected Inflector $inflector;

    /**
     * @var array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandInterface>
     */
    protected array $commandRunner;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \Doctrine\Inflector\Inflector $inflector
     * @param array $commandRunner
     */
    public function __construct(SyncApiConfig $config, MessageBuilderInterface $messageBuilder, Inflector $inflector, array $commandRunner)
    {
        $this->config = $config;
        $this->messageBuilder = $messageBuilder;
        $this->inflector = $inflector;
        $this->commandRunner = $commandRunner;
    }

    /**
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function build(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer
    {
        $openApi = $this->load($openApiRequestTransfer->getTargetFileOrFail());

        $sprykMode = $this->getSprykMode($openApiRequestTransfer);

        $openApiResponseTransfer = new OpenApiResponseTransfer();

        foreach ($this->commandRunner as $commandRunner) {
            $openApiResponseTransfer = $commandRunner->build($sprykMode, $openApi, $openApiRequestTransfer, $openApiResponseTransfer);
        }

        if ($openApiResponseTransfer->getErrors()->count() > 0) {
            $openApiResponseTransfer->addError(
                $this->messageBuilder->buildMessage(
                    SyncApiError::couldNotGenerateCodeFromOpenApi($openApiRequestTransfer->getTargetFileOrFail()),
                ),
            );
        }

        if ($openApiResponseTransfer->getErrors()->count() === 0) {
            $openApiResponseTransfer->addMessage($this->messageBuilder->buildMessage(SyncApiInfo::generatedCodeFromOpenApiSchema()));
        }

        return $openApiResponseTransfer;
    }

    /**
     * @param string $openApiFilePath
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function load(string $openApiFilePath): OpenApi
    {
        return Reader::readFromYamlFile((string)realpath($openApiFilePath));
    }

    /**
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return string
     */
    protected function getSprykMode(OpenApiRequestTransfer $openApiRequestTransfer): string
    {
        if ($openApiRequestTransfer->getOrganizationOrFail() === 'Spryker') {
            return 'core'; // Set sprykMode based on organization
        }

        return 'project';
    }
}
