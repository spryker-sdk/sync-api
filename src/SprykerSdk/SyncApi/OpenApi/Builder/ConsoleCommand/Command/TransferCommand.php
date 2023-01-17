<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command;

use cebe\openapi\spec\MediaType;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Reference;
use cebe\openapi\spec\Schema;
use Doctrine\Inflector\Inflector;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\Message\SyncApiError;
use SprykerSdk\SyncApi\Message\SyncApiInfo;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ArgumentResolverInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments;
use SprykerSdk\SyncApi\SyncApiConfig;
use Transfer\OpenApiRequestTransfer;
use Transfer\OpenApiResponseTransfer;

class TransferCommand implements CommandInterface
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
     * @param \Doctrine\Inflector\Inflector $inflector
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ArgumentResolverInterface $moduleNameArgumentResolver
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunnerInterface $commandRunner
     */
    public function __construct(
        SyncApiConfig $config,
        MessageBuilderInterface $messageBuilder,
        Inflector $inflector,
        ArgumentResolverInterface $moduleNameArgumentResolver,
        CommandRunnerInterface $commandRunner
    ) {
        $this->config = $config;
        $this->messageBuilder = $messageBuilder;
        $this->inflector = $inflector;
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
        return $this->buildTransfers($openApi, $sprykMode, $openApiRequestTransfer, $openApiResponseTransfer);
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param string $sprykMode
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function buildTransfers(
        OpenApi $openApi,
        string $sprykMode,
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApiResponseTransfer $openApiResponseTransfer
    ): OpenApiResponseTransfer {
        if (!isset($openApi->paths) || empty($openApi->paths)) {
            $openApiResponseTransfer->addError(
                $this->messageBuilder->buildMessage(
                    SyncApiError::openApiDoesNotDefineAnyPath($openApiRequestTransfer->getTargetFileOrFail()),
                ),
            );

            return $openApiResponseTransfer;
        }

        $transferArgumentsCollection = $this->getTransferArgumentsCollection($openApi, $openApiRequestTransfer, $sprykMode);
        $transferCommands = $this->buildTransferCommandsFromTransferArgumentsCollection($transferArgumentsCollection, $openApiRequestTransfer->getIsVerboseOrFail());
        $openApiResponseTransfer = $this->addInfoMessages($openApiResponseTransfer, $transferArgumentsCollection);

        $this->commandRunner->runCommands($transferCommands);

        return $openApiResponseTransfer;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param string $sprykMode
     *
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments>
     */
    protected function getTransferArgumentsCollection(OpenApi $openApi, OpenApiRequestTransfer $openApiRequestTransfer, string $sprykMode): array
    {
        $transferArgumentsCollection = [];

        /** @var \cebe\openapi\spec\PathItem $pathItem */
        foreach ($openApi->paths->getPaths() as $path => $pathItem) {
            $transferArgumentsCollection = $this->addTransferArgumentsForPathItem($sprykMode, $openApiRequestTransfer, $path, $pathItem, $transferArgumentsCollection);
        }

        return $transferArgumentsCollection;
    }

    /**
     * @param string $sprykMode
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param string $path
     * @param \cebe\openapi\spec\PathItem $pathItem
     * @param array $transferArgumentsCollection
     *
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments>
     */
    protected function addTransferArgumentsForPathItem(
        string $sprykMode,
        OpenApiRequestTransfer $openApiRequestTransfer,
        string $path,
        PathItem $pathItem,
        array $transferArgumentsCollection
    ): array {
        /** @var \cebe\openapi\spec\Operation $operation */
        foreach ($pathItem->getOperations() as $operation) {
            $transferArgumentsCollection = $this->addTransferArgumentsForOperation($sprykMode, $openApiRequestTransfer, $path, $operation, $pathItem, $transferArgumentsCollection);
        }

        return $transferArgumentsCollection;
    }

    /**
     * @param string $sprykMode
     * @param \Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param string $path
     * @param \cebe\openapi\spec\Operation $operation
     * @param \cebe\openapi\spec\PathItem $pathItem
     * @param array $transferArgumentsCollection
     *
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments>
     */
    protected function addTransferArgumentsForOperation(
        string $sprykMode,
        OpenApiRequestTransfer $openApiRequestTransfer,
        string $path,
        Operation $operation,
        PathItem $pathItem,
        array $transferArgumentsCollection
    ) {
        $organization = $openApiRequestTransfer->getOrganizationOrFail();
        $moduleName = $this->moduleNameArgumentResolver->resolve($path, $pathItem, $operation, $openApiRequestTransfer->getApplicationTypeOrFail());

        if ($operation->requestBody) {
            $transferArgumentsCollection = $this->addTransferArgumentsForRequestBodyPropertiesFromOperation($sprykMode, $organization, $moduleName, $operation, $transferArgumentsCollection);
        }

        return $this->addTransferArgumentsForResponsePropertiesFromOperation($sprykMode, $organization, $moduleName, $operation, $transferArgumentsCollection);
    }

    /**
     * @param string $sprykMode
     * @param string $organization
     * @param string $moduleName
     * @param \cebe\openapi\spec\Operation $operation
     * @param array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments> $transferArgumentsCollection
     *
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments>
     */
    protected function addTransferArgumentsForRequestBodyPropertiesFromOperation(
        string $sprykMode,
        string $organization,
        string $moduleName,
        Operation $operation,
        array $transferArgumentsCollection
    ): array {
        /** @var \cebe\openapi\spec\MediaType $mediaType */
        foreach ($this->getRequestBodyFromOperation($operation) as $applicationType => $mediaType) {
            if (!$this->acceptApplicationType($applicationType)) {
                continue;
            }

            /** @var \cebe\openapi\spec\Schema $schema */
            $schema = $mediaType->schema;

            $transferName = $this->getTransferNameFromSchemaOrReference($schema);
            $requestBodyProperties = $this->getRequestBodyPropertiesFromSchemaOrReference($schema);
            $transferArgumentsCollection = $this->addTransferToCollection($transferArgumentsCollection, $sprykMode, $moduleName, $organization, $transferName, $requestBodyProperties);
        }

        return $transferArgumentsCollection;
    }

    /**
     * @param string $applicationType
     *
     * @return bool
     */
    protected function acceptApplicationType(string $applicationType): bool
    {
        if (
            in_array($applicationType, [
            'application/json',
            'application/vnd.api+json',
            'application/xml',
            ])
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @return iterable
     */
    protected function getRequestBodyFromOperation(Operation $operation): iterable
    {
        return isset($operation->requestBody) && isset($operation->requestBody->content) ? $operation->requestBody->content : [];
    }

    /**
     * @param \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference $schemaOrReference
     *
     * @return array<int|string, mixed>
     */
    protected function getRequestBodyPropertiesFromSchemaOrReference($schemaOrReference): array
    {
        foreach ($this->getPropertiesFromSchemaOrReference($schemaOrReference) as $schemaOrReferenceObject) {
            if (isset($schemaOrReferenceObject->properties) && !empty($schemaOrReferenceObject->properties) && ($schemaOrReferenceObject instanceof Schema || $schemaOrReferenceObject instanceof Reference)) {
                return $this->getRequestBodyPropertiesFromSchemaOrReference($schemaOrReferenceObject);
            }
        }

        return $this->prepareRequestBodyProperties($this->getPropertiesFromSchemaOrReference($schemaOrReference));
    }

    /**
     * @param \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference $schemaOrReference
     *
     * @return iterable
     */
    protected function getPropertiesFromSchemaOrReference($schemaOrReference): iterable
    {
        return $schemaOrReference->properties ?? [];
    }

    /**
     * @param iterable $properties
     *
     * @return array<int|string, mixed>
     */
    protected function prepareRequestBodyProperties(iterable $properties): array
    {
        $requestBodyProperties = [];

        foreach ($properties as $key => $schemaOrReferenceObject) {
            if (isset($schemaOrReferenceObject->type) && $schemaOrReferenceObject->type !== 'array') {
                $requestBodyProperties[$key] = $schemaOrReferenceObject->type;
            }
        }

        return $requestBodyProperties;
    }

    /**
     * @param \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference $schemaOrReference
     *
     * @return string
     */
    protected function getTransferNameFromSchemaOrReference($schemaOrReference): string
    {
        if (isset($schemaOrReference->type) && isset($schemaOrReference->items) && $schemaOrReference->type === 'array') {
            return $this->getTransferNameFromSchemaOrReference($schemaOrReference->items);
        }

        $referencePathName = '';

        if ($schemaOrReference->getDocumentPosition()) {
            $referencePath = $schemaOrReference->getDocumentPosition()->getPath();
            $referencePathName = end($referencePath);
        }

        return $referencePathName;
    }

    /**
     * @param string $sprykMode
     * @param string $organization
     * @param string $moduleName
     * @param \cebe\openapi\spec\Operation $operation
     * @param array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments> $transferArgumentsCollection
     *
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments>
     */
    protected function addTransferArgumentsForResponsePropertiesFromOperation(
        string $sprykMode,
        string $organization,
        string $moduleName,
        Operation $operation,
        array $transferArgumentsCollection
    ): array {
        /** @var \cebe\openapi\spec\Response|\cebe\openapi\spec\Reference $content */
        foreach ($this->getResponsesFromOperation($operation) as $content) {
            if (isset($content->content) && !empty($content->content)) {
                $transferArgumentsCollection = $this->getPropertiesFromOperationContent($sprykMode, $organization, $moduleName, $content->content, $transferArgumentsCollection);
            }
        }

        return $transferArgumentsCollection;
    }

    /**
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @return iterable
     */
    protected function getResponsesFromOperation(Operation $operation): iterable
    {
        return ($operation->responses ?? []);
    }

    /**
     * @param string $sprykMode
     * @param string $organization
     * @param string $moduleName
     * @param array $contents
     * @param array $transferArgumentsCollection
     *
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments>
     */
    protected function getPropertiesFromOperationContent(
        string $sprykMode,
        string $organization,
        string $moduleName,
        array $contents,
        array $transferArgumentsCollection
    ): array {
        foreach ($contents as $response) {
            $transferName = $this->getTransferNameFromSchemaOrReference($response->schema);
            $responseProperties = (array)$this->getResponseProperties($response);
            $transferArgumentsCollection = $this->addTransferToCollection($transferArgumentsCollection, $sprykMode, $moduleName, $organization, $transferName, $responseProperties);
        }

        return $transferArgumentsCollection;
    }

    /**
     * @param array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments> $transferArgumentsCollection
     * @param string $sprykMode
     * @param string $moduleName
     * @param string $organization
     * @param string $transferName
     * @param array<string> $properties
     *
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments>
     */
    protected function addTransferToCollection(
        array $transferArgumentsCollection,
        string $sprykMode,
        string $moduleName,
        string $organization,
        string $transferName,
        array $properties
    ): array {
        if (count($properties) === 0) {
            return $transferArgumentsCollection;
        }

        $transferArgumentsProperties = $this->propertiesToTransferArgumentsProperties($properties);

        $transferKey = sprintf('%s.%s', $transferName, $moduleName);

        if (isset($transferArgumentsCollection[$transferKey])) {
            /** @var \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments $transferArguments */
            $transferArguments = $transferArgumentsCollection[$transferKey];
            $transferArguments->setProperties($transferArguments->getProperties() + $transferArgumentsProperties);
            $transferArgumentsCollection[$transferKey] = $transferArguments;

            return $transferArgumentsCollection;
        }

        $transferArguments = new TransferArguments();
        $transferArguments->setSprykMode($sprykMode);
        $transferArguments->setModuleName($moduleName);
        $transferArguments->setOrganization($organization);
        $transferArguments->setTransferName($transferName);
        $transferArguments->setProperties($transferArgumentsProperties);
        $transferArgumentsCollection[$transferKey] = $transferArguments;

        return $transferArgumentsCollection;
    }

    /**
     * @param array $properties
     *
     * @return array
     */
    protected function propertiesToTransferArgumentsProperties(array $properties): array
    {
        $transferArgumentsProperties = [];

        foreach ($properties as $propertyName => $propertyDefinition) {
            $transferArgumentsProperties[] = sprintf('%s:%s', lcfirst($propertyName), $propertyDefinition);
        }

        return $transferArgumentsProperties;
    }

    /**
     * @param \cebe\openapi\spec\MediaType $response
     *
     * @return array|null
     */
    protected function getResponseProperties(MediaType $response): ?array
    {
        if (isset($response->schema)) {
            $responseProperties = $this->getResponsePropertiesFromSchemaOrReference($response->schema, []);
            if ($responseProperties) {
                return $responseProperties;
            }
        }

        return null;
    }

    /**
     * @param \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference $schemaOrReference
     * @param array $rootType
     *
     * @return array
     */
    protected function getResponsePropertiesFromSchemaOrReference($schemaOrReference, array $rootType): array
    {
        foreach ($this->getPropertiesFromSchemaOrReference($schemaOrReference) as $schemaOrReferenceObject) {
            if (isset($schemaOrReferenceObject->properties) && !empty($schemaOrReferenceObject->properties) && ($schemaOrReferenceObject instanceof Schema || $schemaOrReferenceObject instanceof Reference)) {
                $rootType[] = false;

                return $this->getResponsePropertiesFromSchemaOrReference($schemaOrReferenceObject, $rootType);
            }

            if (isset($schemaOrReferenceObject->items->properties) && !empty($schemaOrReferenceObject->items->properties) && ($schemaOrReferenceObject->items instanceof Schema || $schemaOrReferenceObject->items instanceof Reference)) {
                $rootType[] = true;

                return $this->getResponsePropertiesFromSchemaOrReference($schemaOrReferenceObject->items, $rootType);
            }
        }
        if (current($rootType) === true) {
            return $this->generateArrayOfClassInstance($this->getTransferNameFromSchemaOrReference($schemaOrReference));
        }

        return $this->prepareResponseProperties($this->getPropertiesFromSchemaOrReference($schemaOrReference));
    }

    /**
     * @param \cebe\openapi\spec\Schema|\cebe\openapi\spec\Reference $schemaOrReference
     *
     * @return string
     */
    protected function generateArrayOfDataType($schemaOrReference): string
    {
        return (isset($schemaOrReference->type) && !empty($schemaOrReference->type)) ? 'array[]:' . $schemaOrReference->type : '';
    }

    /**
     * @param string $className
     *
     * @return array <string, string>
     */
    protected function generateArrayOfClassInstance(string $className): array
    {
        $className = str_replace('Attributes', '', $className);

        return [$this->inflector->pluralize($className) => $className . '[]:' . $this->inflector->camelize($className)];
    }

    /**
     * @param iterable $properties
     *
     * @return array<int|string, mixed>
     */
    protected function prepareResponseProperties(iterable $properties): array
    {
        $response = [];

        foreach ($properties as $key => $schemaOrReferenceObject) {
            if (isset($schemaOrReferenceObject->type) && $schemaOrReferenceObject->type !== 'array') {
                $response[$key] = $schemaOrReferenceObject->type;
            }

            if (isset($schemaOrReferenceObject->items)) {
                $response[$key] = $this->generateArrayOfDataType($schemaOrReferenceObject->items);
            }
        }

        return $response;
    }

    /**
     * @param \Transfer\OpenApiResponseTransfer $openApiResponseTransfer
     * @param array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments> $transferArgumentsCollection
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    protected function addInfoMessages(OpenApiResponseTransfer $openApiResponseTransfer, array $transferArgumentsCollection): OpenApiResponseTransfer
    {
        foreach ($transferArgumentsCollection as $transferArguments) {
            $messageTransfer = $this->messageBuilder->buildMessage(SyncApiInfo::addedTransfer((string)$transferArguments->getTransferName(), (string)$transferArguments->getModuleName()));
            $openApiResponseTransfer->addMessage($messageTransfer);
        }

        return $openApiResponseTransfer;
    }

    /**
     * @param array $transferArgumentsCollection
     * @param bool $isVerbose
     *
     * @return array
     */
    protected function buildTransferCommandsFromTransferArgumentsCollection(array $transferArgumentsCollection, bool $isVerbose): array
    {
        $commandLines = [];

        foreach ($transferArgumentsCollection as $transferArguments) {
            $commandLines = $this->addTransferGenerateCommand($transferArguments, $commandLines, $isVerbose);
        }

        return $commandLines;
    }

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\TransferArguments $transferArgument
     * @param array $commandLines
     * @param bool $isVerbose
     *
     * @return array
     */
    protected function addTransferGenerateCommand(TransferArguments $transferArgument, array $commandLines, bool $isVerbose): array
    {
        $commandLine = $transferArgument->getConsoleCommandArguments();
        array_unshift($commandLine, $this->config->getSprykRunExecutablePath() . '/vendor/bin/spryk-run');

        $commandLine[] = '-n';

        if ($isVerbose) {
            $commandLine[] = '-v';
        }

        $commandLines[] = $commandLine;

        return $commandLines;
    }
}
