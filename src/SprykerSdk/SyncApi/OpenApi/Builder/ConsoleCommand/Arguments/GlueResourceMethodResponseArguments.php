<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments;

use ReflectionClass;

class GlueResourceMethodResponseArguments implements ArgumentsInterface
{
    /**
     * @var string
     */
    protected string $sprykName = 'AddGlueResourceMethodResponse';

    /**
     * @var string
     */
    protected string $sprykMode = 'project';

    /**
     * @var string
     */
    protected string $apiType = 'Backend';

    /**
     * @var string|null
     */
    protected ?string $organization;

    /**
     * @var string|null
     */
    protected ?string $resource;

    /**
     * @var string|null
     */
    protected ?string $httpMethod;

    /**
     * @var int|null
     */
    protected ?int $httpResponseCode;

    /**
     * @var array<string>
     */
    protected array $extensions = [];

    /**
     * @param string $sprykMode
     *
     * @return void
     */
    public function setSprykMode(string $sprykMode): void
    {
        $this->sprykMode = $sprykMode;
    }

    /**
     * @param string $apiType
     *
     * @return void
     */
    public function setApiType(string $apiType): void
    {
        $this->apiType = $apiType;
    }

    /**
     * @param string $resource
     *
     * @return void
     */
    public function setResource(string $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @param string $httpMethod
     *
     * @return void
     */
    public function setHttpMethod(string $httpMethod): void
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * @param int $httpResponseCode
     *
     * @return void
     */
    public function setHttpResponseCode(int $httpResponseCode): void
    {
        $this->httpResponseCode = $httpResponseCode;
    }

    /**
     * @param string $organization
     *
     * @return void
     */
    public function setOrganization(string $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * @param array $extensions
     *
     * @return void
     */
    public function setExtensions(array $extensions): void
    {
        $this->extensions = $extensions;
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @return array<string>
     */
    public function getConsoleCommandArguments(): array
    {
        $arguments = [
            $this->sprykName,
        ];

        // Add arguments from extensions first
        foreach ($this->getExtensions() as $extensionName => $extensionValue) {
            if ($extensionName !== 'x-spryker') {
                continue;
            }

            foreach ($extensionValue as $key => $value) {
                $arguments[] = sprintf('--%s', $key);
                $arguments[] = $value;
            }
        }

        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $argumentName = sprintf('--%s', $property->getName());

            if ($property->getValue($this) === null || $property->getName() === 'extensions' || $property->getName() === 'sprykName' || in_array($argumentName, $arguments)) {
                continue;
            }

            $arguments[] = $argumentName;
            $arguments[] = $property->getValue($this);
        }

        return $arguments;
    }
}
