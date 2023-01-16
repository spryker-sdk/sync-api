<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ArgumentResolverInterface;
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
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ArgumentResolverInterface
     */
    protected ArgumentResolverInterface $moduleNameArgumentResolver;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunnerInterface
     */
    protected CommandRunnerInterface $commandRunner;

    /**
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ArgumentResolverInterface $moduleNameArgumentResolver
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunnerInterface $commandRunner
     */
    public function __construct(
        SyncApiConfig $config,
        MessageBuilderInterface $messageBuilder,
        ArgumentResolverInterface $moduleNameArgumentResolver,
        CommandRunnerInterface $commandRunner
    ) {
        $this->config = $config;
        $this->messageBuilder = $messageBuilder;
        $this->moduleNameArgumentResolver = $moduleNameArgumentResolver;
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

            $openApiResponseTransfer = $this->generateHttpMethodsWithHttpResponseCodes($pathItem, $glueResourceMethodResponseArguments, $openApiRequestTransfer, $openApiResponseTransfer);
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
    protected function generateHttpMethodsWithHttpResponseCodes(
        PathItem $pathItem,
        GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer
    ): OpenApiResponseTransfer {
        $httpMethods = $pathItem->getOperations();

        /** @var \cebe\openapi\spec\Operation $operation */
        foreach ($httpMethods as $httpMethod => $operation) {
            $moduleName = $this->moduleNameArgumentResolver->resolve((string)$glueResourceMethodResponseArguments->getResource(), $pathItem, $operation);

            $glueResourceMethodResponseArguments->setModuleName($moduleName);
            $glueResourceMethodResponseArguments->setHttpMethod($httpMethod);
            $glueResourceMethodResponseArguments->setExtensions(array_replace_recursive($glueResourceMethodResponseArguments->getExtensions(), $operation->getExtensions()));

            $openApiResponseTransfer = $this->generateHttpResponseCodesForOperationWithApiType($operation, $glueResourceMethodResponseArguments, $openApiRequestTransfer, $openApiResponseTransfer);
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
    protected function generateHttpResponseCodesForOperationWithApiType(
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
            $this->createCommandForResourceHttpMethodAndHttpResponseCode($glueResourceMethodResponseArguments, (bool)$openApiRequestTransfer->getIsVerbose()),
        ]);

        $openApiResponseTransfer->addMessage($this->messageBuilder->buildMessage(SyncApiInfo::addedGlueResourceMethodResponse((string)$glueResourceMethodResponseArguments->getResource(), (string)$glueResourceMethodResponseArguments->getModuleName())));

        return $openApiResponseTransfer;
    }

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments
     * @param bool $isVerbose
     *
     * @return array<string>
     */
    protected function createCommandForResourceHttpMethodAndHttpResponseCode(
        GlueResourceMethodResponseArguments $glueResourceMethodResponseArguments,
        bool $isVerbose
    ): array {
        $commandLine = $glueResourceMethodResponseArguments->getConsoleCommandArguments();
        array_unshift($commandLine, $this->config->getSprykRunExecutablePath() . '/vendor/bin/spryk-run');

        $commandLine[] = '-n';

        if ($isVerbose) {
            $commandLine[] = '-v';
        }

        return $commandLine;
    }
}
