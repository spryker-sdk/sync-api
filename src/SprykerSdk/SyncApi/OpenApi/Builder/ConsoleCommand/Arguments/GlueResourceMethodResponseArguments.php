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
    public const SPRYK_NAME = 'AddGlueResourceMethodResponse';

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
    protected ?string $organization = null;

    /**
     * @var string|null
     */
    protected ?string $module = null;

    /**
     * @var string|null
     */
    protected ?string $resource = null;

    /**
     * @var string|null
     */
    protected ?string $httpMethod = null;

    /**
     * @var int|null
     */
    protected ?int $httpResponseCode = null;

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
     * @return string|null
     */
    public function getResource(): ?string
    {
        return $this->resource;
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
     * @return string|null
     */
    public function getHttpMethod(): ?string
    {
        return $this->httpMethod;
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
     * @return int|null
     */
    public function getHttpResponseCode(): ?int
    {
        return $this->httpResponseCode;
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
     * @param string $moduleName
     *
     * @return void
     */
    public function setModuleName(string $moduleName): void
    {
        $this->module = $moduleName;
    }

    /**
     * @return string|null
     */
    public function getModuleName(): ?string
    {
        return $this->module;
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
            static::SPRYK_NAME,
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
            $property->setAccessible(true);
            $argumentName = sprintf('--%s', $property->getName());
            if ($property->getValue($this) === null) {
                continue;
            }
            if ($property->getName() === 'extensions') {
                continue;
            }
            if ($property->getName() === 'sprykMode') {
                continue;
            }
            if (in_array($argumentName, $arguments)) {
                continue;
            }

            $arguments[] = $argumentName;
            $arguments[] = $property->getValue($this);
        }

        return $arguments;
    }
}
