<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Reference;
use cebe\openapi\spec\Schema;
use Doctrine\Inflector\Inflector;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\OpenApiRequestTransfer;
use Generated\Shared\Transfer\OpenApiResponseTransfer;
use Symfony\Component\Process\Process;

class OpenApiCodeBuilder implements OpenApiCodeBuilderInterface
{
    /**
     * @var \Generated\Shared\Transfer\OpenApiResponseTransfer
     */
    protected OpenApiResponseTransfer $openApiResponseTransfer;

    /**
     * @var \Doctrine\Inflector\Inflector
     */
    protected Inflector $inflector;

    /**
     * @var string
     */
    protected string $sprykMode = 'project';

    /**
     * @param \Doctrine\Inflector\Inflector $inflector
     */
    public function __construct(Inflector $inflector)
    {
        $this->inflector = $inflector;
        $this->openApiResponseTransfer = new OpenApiResponseTransfer();
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OpenApiResponseTransfer
     */
    public function build(OpenApiRequestTransfer $openApiRequestTransfer): OpenApiResponseTransfer
    {
        $openApi = $this->load($openApiRequestTransfer->getTargetFileOrFail());

        $this->setSprykerMode($openApiRequestTransfer);

        $this->generateTransfers($openApiRequestTransfer, $openApi);
        $this->generateResourceMethodResponse($openApiRequestTransfer, $openApi);

        if ($this->openApiResponseTransfer->getErrors()->count() > 0) {
            $messageTransfer = new MessageTransfer();
            $messageTransfer->setMessage('OpenAPI has invalid schema.');
            $this->openApiResponseTransfer->addError($messageTransfer);
        }

        return $this->openApiResponseTransfer;
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
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return void
     */
    protected function setSprykerMode(OpenApiRequestTransfer $openApiRequestTransfer): void
    {
        if ($openApiRequestTransfer->getOrganizationOrFail() === 'Spryker') {
            $this->sprykMode = 'core'; //Set sprykMode based on organization
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return void
     */
    protected function generateTransfers(
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApi $openApi
    ): void {
        $transferDefinitions = $this->getTransferDefinitions($openApi);

        if ($this->openApiResponseTransfer->getErrors()->count() === 0) {
            $transferBuildSprykCommands = $this->getTransferDefinitionSprykCommands($openApiRequestTransfer->getOrganizationOrFail(), $transferDefinitions);
            $this->runCommands($transferBuildSprykCommands, $openApiRequestTransfer->getProjectRootOrFail());
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiRequestTransfer $openApiRequestTransfer
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return void
     */
    protected function generateResourceMethodResponse(
        OpenApiRequestTransfer $openApiRequestTransfer,
        OpenApi $openApi
    ): void {
        $resourceHttpMethodsWithHttpResponseCodes = [];

        if (!isset($openApi->paths) || empty($openApi->paths)) {
            $messageTransfer = new MessageTransfer();
            $messageTransfer->setMessage('OpenAPI has does not have path uri.');
            $this->openApiResponseTransfer->addError($messageTransfer);

            return;
        }

        /** @var \cebe\openapi\spec\PathItem $pathItem */
        foreach ($openApi->paths->getPaths() as $path => $pathItem) {
            $resourceHttpMethodsWithHttpResponseCodes[$path] = $this->getHttpMethodsWithHttpResponseCodes($pathItem);
        }

        $this->runResourceMethodResponseCodeSpryk($resourceHttpMethodsWithHttpResponseCodes, $openApiRequestTransfer->getOrganizationOrFail(), $openApiRequestTransfer->getProjectRootOrFail());
    }

    /**
     * @param \cebe\openapi\spec\PathItem $pathItem
     *
     * @return array<string, array<int, int>>
     */
    protected function getHttpMethodsWithHttpResponseCodes(PathItem $pathItem): array
    {
        $httpMethods = $pathItem->getOperations();
        $httpMethodsHttpResponses = [];

        /** @var \cebe\openapi\spec\Operation $operation */
        foreach ($httpMethods as $httpMethod => $operation) {
            $httpMethodsHttpResponses[$httpMethod] = $this->getHttpResponseCodesForOperation($operation);
        }

        return $httpMethodsHttpResponses;
    }

    /**
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @return array<int, int>
     */
    protected function getHttpResponseCodesForOperation(Operation $operation): array
    {
        $httpResponseCodes = [];

        if (!is_iterable($operation->responses)) {
            return $httpResponseCodes;
        }

        /** @var \cebe\openapi\spec\Response $response */
        foreach ($operation->responses as $httpResponseCode => $response) {
            if (!is_int($httpResponseCode)) {
                continue;
            }

            $httpResponseCodes[$httpResponseCode] = $httpResponseCode;
        }

        return $httpResponseCodes;
    }

    /**
     * @param array<string, array<string, array<int, int>>> $resourceHttpMethodsWithHttpResponseCodes
     * @param string $organization
     * @param string $projectRootPath
     *
     * @return void
     */
    protected function runResourceMethodResponseCodeSpryk(array $resourceHttpMethodsWithHttpResponseCodes, string $organization, string $projectRootPath): void
    {
        $commands = [];

        foreach ($resourceHttpMethodsWithHttpResponseCodes as $resource => $httpMethodsWithHttpResponseCodes) {
            if (strpos($resource, '{') !== false) {
                $messageTransfer = new MessageTransfer();
                $messageTransfer->setMessage(sprintf('Can\'t handle resources with placeholder at the moment. Resource "%s" can\'t be used to auto generate code.', $resource));
                $this->openApiResponseTransfer->addMessage($messageTransfer);

                continue;
            }
            $commands = $this->createCommandsForResourceHttpMethodsWithHttpResponseCodes($resource, $httpMethodsWithHttpResponseCodes, $organization, $commands);
        }

        $this->runCommands($commands, $projectRootPath);
    }

    /**
     * @param string $resource
     * @param array<string, array<int, int>> $httpMethodsWithHttpResponseCodes
     * @param string $organization
     * @param array<array> $commands
     *
     * @return array<array>
     */
    protected function createCommandsForResourceHttpMethodsWithHttpResponseCodes(
        string $resource,
        array $httpMethodsWithHttpResponseCodes,
        string $organization,
        array $commands
    ): array {
        foreach ($httpMethodsWithHttpResponseCodes as $httpMethod => $httpResponseCodes) {
            $commands = $this->createCommandsForResourceHttpMethodAndHttpResponseCodes($resource, $httpMethod, $httpResponseCodes, $organization, $commands);
        }

        return $commands;
    }

    /**
     * @param string $resource
     * @param string $httpMethod
     * @param array<int, int> $httpResponseCodes
     * @param string $organization
     * @param array<array> $commands
     *
     * @return array<array>
     */
    protected function createCommandsForResourceHttpMethodAndHttpResponseCodes(
        string $resource,
        string $httpMethod,
        array $httpResponseCodes,
        string $organization,
        array $commands
    ): array {
        foreach ($httpResponseCodes as $httpResponseCode) {
            $commands = $this->createCommandsForResourceHttpMethodAndHttpResponseCode($resource, $httpMethod, $httpResponseCode, $organization, $commands);
        }

        return $commands;
    }

    /**
     * @param string $resource
     * @param string $httpMethod
     * @param int $httpResponseCode
     * @param string $organization
     * @param array<array> $commands
     *
     * @return array<array>
     */
    protected function createCommandsForResourceHttpMethodAndHttpResponseCode(
        string $resource,
        string $httpMethod,
        int $httpResponseCode,
        string $organization,
        array $commands
    ): array {
        $commands[] = [
            'vendor/bin/spryk-run',
            'AddGlueResourceMethodResponse',
            '--mode', $this->sprykMode,
            '--organization', $organization,
            '--resource', $resource,
            '--httpMethod', $httpMethod,
            '--httpResponseCode', $httpResponseCode,
        ];

        return $commands;
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $openApi
     *
     * @return array
     */
    protected function getTransferDefinitions(OpenApi $openApi): array
    {
        $transferDefinitions = [];

        if (isset($openApi->paths) && !empty($openApi->paths)) {
            /** @var \cebe\openapi\spec\PathItem $pathItem */
            foreach ($openApi->paths->getPaths() as $path => $pathItem) {
                $transferDefinitions[$path] = $this->getTransferDefinitionFromPathItem($path, $pathItem);
            }
        } else {
            $messageTransfer = new MessageTransfer();
            $messageTransfer->setMessage('OpenAPI has does not have path uri.');
            $this->openApiResponseTransfer->addError($messageTransfer);
        }

        return $transferDefinitions;
    }

    /**
     * @param string $path
     * @param \cebe\openapi\spec\PathItem $pathItem
     *
     * @return array <string, array>
     */
    protected function getTransferDefinitionFromPathItem(string $path, PathItem $pathItem): array
    {
        $transferDefinitions = [];

        /** @var \cebe\openapi\spec\Operation $operation */
        foreach ($pathItem->getOperations() as $method => $operation) {
            $controllerName = $this->getControllerName($path, $operation);
            $moduleName = $this->getModuleName($path, $operation);
            if ($controllerName !== '' && $moduleName !== '') {
                $transferDefinitions[$method]['controllerName'] = $controllerName;

                $transferDefinitions[$method]['moduleName'] = $moduleName;

                if ($operation->requestBody) {
                    $transferDefinitions[$method]['requestBody'] = $this->getRequestBodyPropertiesFromOperation($operation);
                }

                $transferDefinitions[$method]['responses'] = $this->getResponsePropertiesFromOperation($operation);
            }
        }

        return $transferDefinitions;
    }

    /**
     * @param string $path
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @return string
     */
    protected function getControllerName(string $path, Operation $operation): string
    {
        if (isset($operation->operationId)) {
            $operationId = explode('.', $operation->operationId);

            return $this->inflector->classify(sprintf(end($operationId) . '%s', 'Controller'));
        }

        $pathFragments = explode('/', trim($path, '/'));

        foreach (array_reverse($pathFragments) as $key => $resource) {
            if ($resource === '') {
                continue;
            }

            if ($key === (count($pathFragments) - 1)) {
                $resource = sprintf($resource . '%s', 'Resource');
            }

            if (strpos($resource, '{') === false) {
                return $this->inflector->classify(sprintf("{$resource}%s", 'Controller'));
            }
        }

        $messageTransfer = new MessageTransfer();
        $messageTransfer->setMessage('Controller name not found for path');
        $this->openApiResponseTransfer->addError($messageTransfer);

        return '';
    }

    /**
     * @param string $path
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @return string
     */
    protected function getModuleName(string $path, Operation $operation): string
    {
        if (isset($operation->operationId)) {
            $operationId = explode('.', $operation->operationId);

            return $this->inflector->classify(current($operationId));
        }

        $path = trim($path, '/');

        if ($path === '') {
            $messageTransfer = new MessageTransfer();
            $messageTransfer->setMessage('Module name not found for path');
            $this->openApiResponseTransfer->addError($messageTransfer);

            return '';
        }
        $pathFragments = explode('/', trim($path, '/'));

        return sprintf(ucwords(current($pathFragments)) . '%s', 'Api');
    }

    /**
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @return array <string, array>
     */
    protected function getRequestBodyPropertiesFromOperation(Operation $operation): array
    {
        $requestBodyProperties = [];
        /** @var \cebe\openapi\spec\RequestBody $mediaType */
        foreach ($this->getRequestBodyFromOperation($operation) as $mediaType) {
            if (isset($mediaType->schema)) {
                $requestBodyProperties[$this->getTransferNameFromSchemaOrReference($mediaType->schema)] = $this->getRequestBodyPropertiesFromSchemaOrReference($mediaType->schema);
            }
        }

        return $requestBodyProperties;
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

            if (isset($schemaOrReferenceObject->items) && $schemaOrReferenceObject->items !== null) {
                $requestBodyProperties[$key] = $this->generateArrayOfDataType($schemaOrReferenceObject->items);
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
        $referencePathName = '';
        if ($schemaOrReference->getDocumentPosition()) {
            $referencePath = $schemaOrReference->getDocumentPosition()->getPath();
            $referencePathName = end($referencePath);
        }

        return $referencePathName;
    }

    /**
     * @param \cebe\openapi\spec\Operation $operation
     *
     * @return array <string, string>
     */
    protected function getResponsePropertiesFromOperation(Operation $operation): array
    {
        $responses = [];

        /** @var \cebe\openapi\spec\Response|\cebe\openapi\spec\Reference $content */
        foreach ($this->getResponsesFromOperation($operation) as $content) {
            if (isset($content->content) && !empty($content->content)) {
                $responses = $this->getPropertiesFromOperationContent($content->content, $responses);
            }
        }

        return $responses;
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
     * @param array $contents
     * @param array $responses
     *
     * @return array <string, string>
     */
    protected function getPropertiesFromOperationContent(array $contents, array $responses): array
    {
        foreach ($contents as $response) {
            if (isset($response->schema)) {
                $responses[$this->getTransferNameFromSchemaOrReference($response->schema)] = $this->getResponsePropertiesFromSchemaOrReference($response->schema, []);
            }
        }

        return $responses;
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
     * @param string $organization
     * @param array $transferDefinitions
     *
     * @return array[
     *      0 => array,
     *      1 => array
     * ]
     */
    protected function getTransferDefinitionSprykCommands(string $organization, array $transferDefinitions): array
    {
        $commandLines = [];

        foreach ($transferDefinitions as $transferDefinition) {
            foreach ($transferDefinition as $data) {
                $this->generateTransferCommands($organization, ($data['requestBody'] ?? []), $data['moduleName'], $commandLines);
                $this->generateTransferCommands($organization, ($data['responses'] ?? []), $data['moduleName'], $commandLines);
            }
        }

        return array_values($commandLines);
    }

    /**
     * @param string $organization
     * @param array $transferDefinitions
     * @param string $moduleName
     * @param array $commandLines
     *
     * @return void
     */
    protected function generateTransferCommands(string $organization, array $transferDefinitions, string $moduleName, array &$commandLines): void
    {
        foreach ($transferDefinitions as $command => $transferDefinition) {
            $commandLines[$command] = $this->prepareTransferCommand($organization, $transferDefinition, $command, $moduleName);
        }
    }

    /**
     * @param string $organization
     * @param array $parameters
     * @param string $command
     * @param string $moduleName
     *
     * @return array[
     *      0 => array,
     *      1 => array
     * ]
     */
    protected function prepareTransferCommand(string $organization, array $parameters, string $command, string $moduleName): array
    {
        return $this->getTransferBuildCommand(
            $organization,
            $moduleName,
            $command,
            $this->getTransferPropertyName($parameters),
            $this->getTransferPropertyType($parameters),
            $this->getTransferPropertySingular($parameters),
        );
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    protected function getTransferPropertyName(array $parameters): string
    {
        if (count($parameters) === 1) {
            return array_key_first($parameters);
        }

        return implode(',', $this->preparePropertyNameForCommand($parameters));
    }

    /**
     * @param array $parameters
     *
     * @return array <int, string>
     */
    protected function preparePropertyNameForCommand(array $parameters): array
    {
        $parsedProperties = [];
        foreach ($parameters as $key => $value) {
            $parsedProperties[] = "{$key}:{$value}";
        }

        return $parsedProperties;
    }

    /**
     * @param array $parameters
     *
     * @return string|null
     */
    protected function getTransferPropertyType(array $parameters)
    {
        if (count($parameters) === 1) {
            $propertyName = array_key_first($parameters);

            return current(explode(':', $parameters[$propertyName]));
        }

        return null;
    }

    /**
     * @param array $parameters
     *
     * @return string|null
     */
    protected function getTransferPropertySingular(array $parameters)
    {
        $propertyName = array_key_first($parameters);
        $propertyTypes = explode(':', $parameters[$propertyName]);
        if (count($parameters) !== 1 || count($propertyTypes) !== 1) {
            return end($propertyTypes);
        }

        return null;
    }

    /**
     * @param string $organization
     * @param string $moduleName
     * @param string $transferName
     * @param string $propertyName
     * @param string|null $propertyType
     * @param string|null $singular
     *
     * @return array[
     *      0 => string,
     *      1 => string
     * ]
     */
    protected function getTransferBuildCommand(
        string $organization,
        string $moduleName,
        string $transferName,
        string $propertyName,
        ?string $propertyType,
        ?string $singular
    ): array {
        $commandData = [
            'vendor/bin/spryk-run',
            'AddSharedTransferProperty',
            '--mode', $this->sprykMode,
            '--organization', $organization,
            '--module', $moduleName,
            '--name', $transferName,
            '--propertyName', $propertyName,
        ];

        if (($propertyType !== null)) {
            $commandData[] = '--propertyType';
            $commandData[] = $propertyType;
        }

        if (($singular !== null)) {
            $commandData[] = '--singular';
            $commandData[] = $singular;
        }

        $commandData[] = '-n';
        $commandData[] = '-v';

        return $commandData;
    }

    /**
     * @param array<array> $commands
     * @param string $projectRootPath
     *
     * @return void
     */
    protected function runCommands(array $commands, string $projectRootPath): void
    {
        foreach ($commands as $command) {
            $this->runProcess($command, $projectRootPath);
        }

        $this->openApiResponseTransfer->addMessage((new MessageTransfer())->setMessage('Executed commands.'));
    }

    /**
     * @codeCoverageIgnore
     *
     * @param array $command
     * @param string $projectRootPath
     *
     * @return void
     */
    protected function runProcess(array $command, string $projectRootPath): void
    {
        $process = new Process($command, $projectRootPath);
        $process->run(function ($a, $buffer) {
            echo $buffer;
            // For debugging purposes, set a breakpoint here to see issues.
        });
    }
}
