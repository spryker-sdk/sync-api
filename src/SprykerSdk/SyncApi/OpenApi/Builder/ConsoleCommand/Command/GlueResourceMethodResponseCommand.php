<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use Doctrine\Inflector\Inflector;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments;
use SprykerSdk\SyncApi\SyncApiConfig;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;

class GlueResourceMethodResponseCommand implements CommandInterface
{
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
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunnerInterface
     */
    protected CommandRunnerInterface $commandRunner;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \Doctrine\Inflector\Inflector $inflector
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunnerInterface $commandRunner
     */
    public function __construct(SyncApiConfig $config, MessageBuilderInterface $messageBuilder, Inflector $inflector, CommandRunnerInterface $commandRunner)
    {
        $this->config = $config;
        $this->messageBuilder = $messageBuilder;
        $this->inflector = $inflector;
        $this->commandRunner = $commandRunner;
    }

    /**
     * @param string $sprykMode
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function build(
        string $sprykMode,
        OpenApi $openApi,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer
    ): OpenApiResponseTransfer {
        return $this->generateResourceMethodResponses($sprykMode, $openApiRequestTransfer, $openApiResponseTransfer, $openApi);
    }

    /**
     * @param string $sprykMode
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function generateResourceMethodResponses(
        string $sprykMode,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer,
        OpenApi $openApi
    ): OpenApiResponseTransfer {
        if (!isset($openApi->paths) || empty($openApi->paths)) {
            $openApiResponseTransfer->addError(
                $this->messageBuilder->buildMessage(
                    SyncApiError::openApiDoesNotDefineAnyPath($openApiRequestTransfer->getTargetFileOrFail()),
                ),
            );

            return $openApiResponseTransfer;
        }

        /** @var \cebe\openapi\spec\PathItem $pathItem */
        foreach ($openApi->paths->getPaths() as $path => $pathItem) {
            $glueResourceMethodResponseArguments = new GlueResourceMethodResponseArguments();
            $glueResourceMethodResponseArguments->setSprykMode($sprykMode);
            $glueResourceMethodResponseArguments->setResource($path);
            $glueResourceMethodResponseArguments->setExtensions($pathItem->getExtensions());

            $openApiResponseTransfer = $this->getHttpMethodsWithHttpResponseCodes($pathItem, $glueResourceMethodResponseArguments, $openApiRequestTransfer, $openApiResponseTransfer);
        }

        return $openApiResponseTransfer;
    }

    /**
     * @param \cebe\openapi\spec\PathItem $pathItem
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function getHttpMethodsWithHttpResponseCodes(
        PathItem $pathItem,
        GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer
    ): OpenApiResponseTransfer {
        $httpMethods = $pathItem->getOperations();

        /** @var \cebe\openapi\spec\Operation $operation */
        foreach ($httpMethods as $httpMethod => $operation) {
            $glueResourceMethodResponseArguments->setHttpMethod($httpMethod);
            $glueResourceMethodResponseArguments->setExtensions(array_replace_recursive($glueResourceMethodResponseArguments->getExtensions(), $operation->getExtensions()));

            $openApiResponseTransfer = $this->getHttpResponseCodesForOperationWithApiType($operation, $glueResourceMethodResponseArguments, $openApiRequestTransfer, $openApiResponseTransfer);
        }

        return $openApiResponseTransfer;
    }

    /**
     * @param \cebe\openapi\spec\Operation $operation
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function getHttpResponseCodesForOperationWithApiType(
        Operation $operation,
        GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer
    ): OpenApiResponseTransfer {
        /** @var iterable $responses */
        $responses = $operation->responses;

        /** @var \cebe\openapi\spec\Response $response */
        foreach ($responses as $httpResponseCode => $response) {
            if (!is_int($httpResponseCode)) {
                continue;
            }

            $contentType = array_key_first($response->content);

            $apiType = $contentType === 'application/vnd.api+json' ? 'JSON' : 'REST';

            $glueResourceMethodResponseArguments->setApiType($apiType);
            $glueResourceMethodResponseArguments->setHttpResponseCode($httpResponseCode);
            $glueResourceMethodResponseArguments->setExtensions(array_replace_recursive($glueResourceMethodResponseArguments->getExtensions(), $response->getExtensions()));

            $openApiResponseTransfer = $this->runResourceMethodResponseCodeSpryk($glueResourceMethodResponseArguments, $openApiRequestTransfer, $openApiResponseTransfer);
        }

        return $openApiResponseTransfer;
    }

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function runResourceMethodResponseCodeSpryk(
        GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer
    ): OpenApiResponseTransfer {
        $glueResourceMethodResponseArguments->setOrganization(
            $openApiRequestTransfer->getOrganizationOrFail(),
        );

        $this->commandRunner->runCommands([
            $this->createCommandForResourceHttpMethodAndHttpResponseCode($glueResourceMethodResponseArguments),
        ]);

        return $openApiResponseTransfer;
    }

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments
     *
     * @return array<string>
     */
    protected function createCommandForResourceHttpMethodAndHttpResponseCode(
        GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments
    ): array {
        $commandLine = $glueResourceMethodResponseArguments->getConsoleCommandArguments();
        array_unshift($commandLine, $this->config->getSprykRunExecutablePath() . '/vendor/bin/spryk-run');

        return $commandLine;
    }
}
