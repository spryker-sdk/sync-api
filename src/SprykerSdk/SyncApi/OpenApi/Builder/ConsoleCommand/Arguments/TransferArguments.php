<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments;

use ReflectionClass;

class TransferArguments implements ArgumentsInterface
{
    /**
     * @var string
     */
    protected string $sprykName = 'AddSharedTransferProperty';

    /**
     * @var string
     */
    protected string $sprykMode = 'project';

    /**
     * @var string|null
     */
    protected ?string $organization;

    /**
     * @var string|null
     */
    protected ?string $moduleName;

    /**
     * @var string|null
     */
    protected ?string $transferName;

    /**
     * @var array<string>
     */
    protected array $properties = [];

    /**
     * @var array<string, string|array>
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
     * @param string|null $organization
     *
     * @return void
     */
    public function setOrganization(?string $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * @param string|null $moduleName
     *
     * @return void
     */
    public function setModuleName(?string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return string|null
     */
    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    /**
     * @param string|null $transferName
     *
     * @return void
     */
    public function setTransferName(?string $transferName): void
    {
        $this->transferName = $transferName;
    }

    /**
     * @return string|null
     */
    public function getTransferName(): ?string
    {
        return $this->transferName;
    }

    /**
     * @param array $properties
     *
     * @return void
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @return array<string>
     */
    public function getConsoleCommandArguments(): array
    {
        $arguments = [
            $this->sprykName,
        ];

        if ($this->organization) {
            $arguments[] = '--organization';
            $arguments[] = $this->organization;
        }

        if ($this->moduleName) {
            $arguments[] = '--module';
            $arguments[] = $this->moduleName;
        }

        if ($this->transferName) {
            $arguments[] = '--name';
            $arguments[] = $this->moduleName;
        }

        if ($this->properties) {
            $arguments[] = '--propertyName';
            $arguments[] = implode(',', $this->properties);
        }

        return $arguments;
    }
}
