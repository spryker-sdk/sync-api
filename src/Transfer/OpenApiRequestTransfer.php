<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Transfer;

use InvalidArgumentException;

/**
 * !!! THIS FILE IS AUTO-GENERATED, EVERY CHANGE WILL BE LOST WITH THE NEXT RUN OF TRANSFER GENERATOR
 * !!! DO NOT CHANGE ANYTHING IN THIS FILE
 */
class OpenApiRequestTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const TARGET_FILE = 'targetFile';

    /**
     * @var string
     */
    public const OPEN_API = 'openApi';

    /**
     * @var string
     */
    public const ORGANIZATION = 'organization';

    /**
     * @var string
     */
    public const APPLICATION_TYPE = 'applicationType';

    /**
     * @var string
     */
    public const PROJECT_ROOT = 'projectRoot';

    /**
     * @var string|null
     */
    protected $targetFile;

    /**
     * @var \Transfer\OpenApiTransfer|null
     */
    protected $openApi;

    /**
     * @var string|null
     */
    protected $organization;

    /**
     * @var string|null
     */
    protected $applicationType;

    /**
     * @var string|null
     */
    protected $projectRoot;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'target_file' => 'targetFile',
        'targetFile' => 'targetFile',
        'TargetFile' => 'targetFile',
        'open_api' => 'openApi',
        'openApi' => 'openApi',
        'OpenApi' => 'openApi',
        'organization' => 'organization',
        'Organization' => 'organization',
        'application_type' => 'applicationType',
        'applicationType' => 'applicationType',
        'ApplicationType' => 'applicationType',
        'project_root' => 'projectRoot',
        'projectRoot' => 'projectRoot',
        'ProjectRoot' => 'projectRoot',
    ];

    /**
     * @var array<string, array<string, mixed>>
     */
    protected $transferMetadata = [
        self::TARGET_FILE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'target_file',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::OPEN_API => [
            'type' => 'Transfer\OpenApiTransfer',
            'type_shim' => null,
            'name_underscore' => 'open_api',
            'is_collection' => false,
            'is_transfer' => true,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::ORGANIZATION => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'organization',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::APPLICATION_TYPE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'application_type',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::PROJECT_ROOT => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'project_root',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
    ];

    /**
     * @module SyncApi
     *
     * @param string|null $targetFile
     *
     * @return $this
     */
    public function setTargetFile($targetFile)
    {
        $this->targetFile = $targetFile;
        $this->modifiedProperties[self::TARGET_FILE] = true;

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @return string|null
     */
    public function getTargetFile()
    {
        return $this->targetFile;
    }

    /**
     * @module SyncApi
     *
     * @param string|null $targetFile
     *
     * @return $this
     */
    public function setTargetFileOrFail($targetFile)
    {
        if ($targetFile === null) {
            $this->throwNullValueException(static::TARGET_FILE);
        }

        return $this->setTargetFile($targetFile);
    }

    /**
     * @module SyncApi
     *
     * @return string
     */
    public function getTargetFileOrFail()
    {
        if ($this->targetFile === null) {
            $this->throwNullValueException(static::TARGET_FILE);
        }

        return $this->targetFile;
    }

    /**
     * @module SyncApi
     *
     * @return $this
     */
    public function requireTargetFile()
    {
        $this->assertPropertyIsSet(self::TARGET_FILE);

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @param \Transfer\OpenApiTransfer|null $openApi
     *
     * @return $this
     */
    public function setOpenApi(?OpenApiTransfer $openApi = null)
    {
        $this->openApi = $openApi;
        $this->modifiedProperties[self::OPEN_API] = true;

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @return \Transfer\OpenApiTransfer|null
     */
    public function getOpenApi()
    {
        return $this->openApi;
    }

    /**
     * @module SyncApi
     *
     * @param \Transfer\OpenApiTransfer $openApi
     *
     * @return $this
     */
    public function setOpenApiOrFail(OpenApiTransfer $openApi)
    {
        return $this->setOpenApi($openApi);
    }

    /**
     * @module SyncApi
     *
     * @return \Transfer\OpenApiTransfer
     */
    public function getOpenApiOrFail()
    {
        if ($this->openApi === null) {
            $this->throwNullValueException(static::OPEN_API);
        }

        return $this->openApi;
    }

    /**
     * @module SyncApi
     *
     * @return $this
     */
    public function requireOpenApi()
    {
        $this->assertPropertyIsSet(self::OPEN_API);

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @param string|null $organization
     *
     * @return $this
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
        $this->modifiedProperties[self::ORGANIZATION] = true;

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @return string|null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @module SyncApi
     *
     * @param string|null $organization
     *
     * @return $this
     */
    public function setOrganizationOrFail($organization)
    {
        if ($organization === null) {
            $this->throwNullValueException(static::ORGANIZATION);
        }

        return $this->setOrganization($organization);
    }

    /**
     * @module SyncApi
     *
     * @return string
     */
    public function getOrganizationOrFail()
    {
        if ($this->organization === null) {
            $this->throwNullValueException(static::ORGANIZATION);
        }

        return $this->organization;
    }

    /**
     * @module SyncApi
     *
     * @return $this
     */
    public function requireOrganization()
    {
        $this->assertPropertyIsSet(self::ORGANIZATION);

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @param string|null $applicationType
     *
     * @return $this
     */
    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
        $this->modifiedProperties[self::APPLICATION_TYPE] = true;

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @return string|null
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * @module SyncApi
     *
     * @param string|null $applicationType
     *
     * @return $this
     */
    public function setApplicationTypeOrFail($applicationType)
    {
        if ($applicationType === null) {
            $this->throwNullValueException(static::APPLICATION_TYPE);
        }

        return $this->setApplicationType($applicationType);
    }

    /**
     * @module SyncApi
     *
     * @return string
     */
    public function getApplicationTypeOrFail()
    {
        if ($this->applicationType === null) {
            $this->throwNullValueException(static::APPLICATION_TYPE);
        }

        return $this->applicationType;
    }

    /**
     * @module SyncApi
     *
     * @return $this
     */
    public function requireApplicationType()
    {
        $this->assertPropertyIsSet(self::APPLICATION_TYPE);

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @param string|null $projectRoot
     *
     * @return $this
     */
    public function setProjectRoot($projectRoot)
    {
        $this->projectRoot = $projectRoot;
        $this->modifiedProperties[self::PROJECT_ROOT] = true;

        return $this;
    }

    /**
     * @module SyncApi
     *
     * @return string|null
     */
    public function getProjectRoot()
    {
        return $this->projectRoot;
    }

    /**
     * @module SyncApi
     *
     * @param string|null $projectRoot
     *
     * @return $this
     */
    public function setProjectRootOrFail($projectRoot)
    {
        if ($projectRoot === null) {
            $this->throwNullValueException(static::PROJECT_ROOT);
        }

        return $this->setProjectRootOrFail($projectRoot);
    }

    /**
     * @module SyncApi
     *
     * @return string
     */
    public function getProjectRootOrFail()
    {
        if ($this->projectRoot === null) {
            $this->throwNullValueException(static::PROJECT_ROOT);
        }

        return $this->projectRoot;
    }

    /**
     * @module SyncApi
     *
     * @return $this
     */
    public function requireProjectRoot()
    {
        $this->assertPropertyIsSet(self::PROJECT_ROOT);

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     * @param bool $ignoreMissingProperty
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function fromArray(array $data, $ignoreMissingProperty = false)
    {
        foreach ($data as $property => $value) {
            $normalizedPropertyName = $this->transferPropertyNameMap[$property] ?? null;

            switch ($normalizedPropertyName) {
                case 'targetFile':
                case 'organization':
                case 'applicationType':
                case 'projectRoot':
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                case 'openApi':
                    if (is_array($value)) {
                        $type = $this->transferMetadata[$normalizedPropertyName]['type'];
                        /** @var \Transfer\TransferInterface $value */
                        $value = (new $type())->fromArray($value, $ignoreMissingProperty);
                    }

                    if ($value !== null && $this->isPropertyStrict($normalizedPropertyName)) {
                        $this->assertInstanceOfTransfer($normalizedPropertyName, $value);
                    }
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                default:
                    if (!$ignoreMissingProperty) {
                        throw new InvalidArgumentException(sprintf('Missing property `%s` in `%s`', $property, static::class));
                    }
            }
        }

        return $this;
    }

    /**
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array<string, mixed>
     */
    public function modifiedToArray($isRecursive = true, $camelCasedKeys = false): array
    {
        if ($isRecursive && !$camelCasedKeys) {
            return $this->modifiedToArrayRecursiveNotCamelCased();
        }
        if ($isRecursive && $camelCasedKeys) {
            return $this->modifiedToArrayRecursiveCamelCased();
        }
        if (!$isRecursive && $camelCasedKeys) {
            return $this->modifiedToArrayNotRecursiveCamelCased();
        }
        if (!$isRecursive && !$camelCasedKeys) {
            return $this->modifiedToArrayNotRecursiveNotCamelCased();
        }
    }

    /**
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array<string, mixed>
     */
    public function toArray($isRecursive = true, $camelCasedKeys = false): array
    {
        if ($isRecursive && !$camelCasedKeys) {
            return $this->toArrayRecursiveNotCamelCased();
        }
        if ($isRecursive && $camelCasedKeys) {
            return $this->toArrayRecursiveCamelCased();
        }
        if (!$isRecursive && !$camelCasedKeys) {
            return $this->toArrayNotRecursiveNotCamelCased();
        }
        if (!$isRecursive && $camelCasedKeys) {
            return $this->toArrayNotRecursiveCamelCased();
        }
    }

    /**
     * @param \ArrayObject<string, mixed>|array<string, mixed> $value
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array<string, mixed>
     */
    protected function addValuesToCollectionModified($value, $isRecursive, $camelCasedKeys): array
    {
        $result = [];
        foreach ($value as $elementKey => $arrayElement) {
            if ($arrayElement instanceof AbstractTransfer) {
                $result[$elementKey] = $arrayElement->modifiedToArray($isRecursive, $camelCasedKeys);

                continue;
            }
            $result[$elementKey] = $arrayElement;
        }

        return $result;
    }

    /**
     * @param \ArrayObject<string, mixed>|array<string, mixed> $value
     * @param bool $isRecursive
     * @param bool $camelCasedKeys
     *
     * @return array<string, mixed>
     */
    protected function addValuesToCollection($value, $isRecursive, $camelCasedKeys): array
    {
        $result = [];
        foreach ($value as $elementKey => $arrayElement) {
            if ($arrayElement instanceof AbstractTransfer) {
                $result[$elementKey] = $arrayElement->toArray($isRecursive, $camelCasedKeys);

                continue;
            }
            $result[$elementKey] = $arrayElement;
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function modifiedToArrayRecursiveCamelCased(): array
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $property;

            if ($value instanceof AbstractTransfer) {
                $values[$arrayKey] = $value->modifiedToArray(true, true);

                continue;
            }
            switch ($property) {
                case 'targetFile':
                case 'organization':
                case 'applicationType':
                    $values[$arrayKey] = $value;

                    break;
                case 'openApi':
                    $values[$arrayKey] = $value instanceof AbstractTransfer ? $value->modifiedToArray(true, true) : $value;

                    break;
            }
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
     */
    public function modifiedToArrayRecursiveNotCamelCased(): array
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $this->transferMetadata[$property]['name_underscore'];

            if ($value instanceof AbstractTransfer) {
                $values[$arrayKey] = $value->modifiedToArray(true, false);

                continue;
            }
            switch ($property) {
                case 'targetFile':
                case 'organization':
                case 'applicationType':
                    $values[$arrayKey] = $value;

                    break;
                case 'openApi':
                    $values[$arrayKey] = $value instanceof AbstractTransfer ? $value->modifiedToArray(true, false) : $value;

                    break;
            }
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
     */
    public function modifiedToArrayNotRecursiveNotCamelCased(): array
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $this->transferMetadata[$property]['name_underscore'];

            $values[$arrayKey] = $value;
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
     */
    public function modifiedToArrayNotRecursiveCamelCased(): array
    {
        $values = [];
        foreach ($this->modifiedProperties as $property => $_) {
            $value = $this->$property;

            $arrayKey = $property;

            $values[$arrayKey] = $value;
        }

        return $values;
    }

    /**
     * @return void
     */
    protected function initCollectionProperties(): void
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayNotRecursiveCamelCased(): array
    {
        return [
            'targetFile' => $this->targetFile,
            'organization' => $this->organization,
            'applicationType' => $this->applicationType,
            'openApi' => $this->openApi,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayNotRecursiveNotCamelCased(): array
    {
        return [
            'target_file' => $this->targetFile,
            'organization' => $this->organization,
            'application_type' => $this->applicationType,
            'open_api' => $this->openApi,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveNotCamelCased(): array
    {
        return [
            'target_file' => $this->targetFile instanceof AbstractTransfer ? $this->targetFile->toArray(true, false) : $this->targetFile,
            'organization' => $this->organization instanceof AbstractTransfer ? $this->organization->toArray(true, false) : $this->organization,
            'application_type' => $this->applicationType instanceof AbstractTransfer ? $this->applicationType->toArray(true, false) : $this->applicationType,
            'open_api' => $this->openApi instanceof AbstractTransfer ? $this->openApi->toArray(true, false) : $this->openApi,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'targetFile' => $this->targetFile instanceof AbstractTransfer ? $this->targetFile->toArray(true, true) : $this->targetFile,
            'organization' => $this->organization instanceof AbstractTransfer ? $this->organization->toArray(true, true) : $this->organization,
            'applicationType' => $this->applicationType instanceof AbstractTransfer ? $this->applicationType->toArray(true, true) : $this->applicationType,
            'openApi' => $this->openApi instanceof AbstractTransfer ? $this->openApi->toArray(true, true) : $this->openApi,
        ];
    }
}
