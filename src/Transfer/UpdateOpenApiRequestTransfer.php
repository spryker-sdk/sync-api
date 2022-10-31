<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Transfer;

/**
 * !!! THIS FILE IS AUTO-GENERATED, EVERY CHANGE WILL BE LOST WITH THE NEXT RUN OF TRANSFER GENERATOR
 * !!! DO NOT CHANGE ANYTHING IN THIS FILE
 */
class UpdateOpenApiRequestTransfer extends AbstractTransfer
{
    /**
     * @var string
     */
    public const OPEN_API_FILE = 'openApiFile';

    /**
     * @var string
     */
    public const OPEN_API_DOC_FILE = 'openApiDocFile';

    /**
     * @var string
     */
    public const OPEN_API_DOC = 'openApiDoc';

    /**
     * @var string
     */
    public const PROJECT_ROOT = 'projectRoot';

    /**
     * @var string|null
     */
    protected $openApiFile;

    /**
     * @var string|null
     */
    protected $openApiDocFile;

    /**
     * @var string|null
     */
    protected $openApiDoc;

    /**
     * @var string|null
     */
    protected $projectRoot;

    /**
     * @var array<string, string>
     */
    protected $transferPropertyNameMap = [
        'open_api_file' => 'openApiFile',
        'openApiFile' => 'openApiFile',
        'OpenApiFile' => 'openApiFile',
        'open_api_doc_file' => 'openApiDocFile',
        'openApiDocFile' => 'openApiDocFile',
        'OpenApiDocFile' => 'openApiDocFile',
        'open_api_doc' => 'openApiDoc',
        'openApiDoc' => 'openApiDoc',
        'OpenApiDoc' => 'openApiDoc',
        'project_root' => 'projectRoot',
        'projectRoot' => 'projectRoot',
        'ProjectRoot' => 'projectRoot',
    ];

    /**
     * @var array<string, array<string, mixed>>
     */
    protected $transferMetadata = [
        self::OPEN_API_FILE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'open_api_file',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::OPEN_API_DOC_FILE => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'open_api_doc_file',
            'is_collection' => false,
            'is_transfer' => false,
            'is_value_object' => false,
            'rest_request_parameter' => 'no',
            'is_associative' => false,
            'is_nullable' => false,
            'is_strict' => false,
        ],
        self::OPEN_API_DOC => [
            'type' => 'string',
            'type_shim' => null,
            'name_underscore' => 'open_api_doc',
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
     * @module Syncapi
     *
     * @param string|null $openApiFile
     *
     * @return $this
     */
    public function setOpenApiFile($openApiFile)
    {
        $this->openApiFile = $openApiFile;
        $this->modifiedProperties[self::OPEN_API_FILE] = true;

        return $this;
    }

    /**
     * @module Syncapi
     *
     * @return string|null
     */
    public function getOpenApiFile()
    {
        return $this->openApiFile;
    }

    /**
     * @module Syncapi
     *
     * @param string|null $openApiFile
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return $this
     */
    public function setOpenApiFileOrFail($openApiFile)
    {
        if ($openApiFile === null) {
            $this->throwNullValueException(static::OPEN_API_FILE);
        }

        return $this->setOpenApiFile($openApiFile);
    }

    /**
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return string
     */
    public function getOpenApiFileOrFail()
    {
        if ($this->openApiFile === null) {
            $this->throwNullValueException(static::OPEN_API_FILE);
        }

        return $this->openApiFile;
    }

    /**
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireOpenApiFile()
    {
        $this->assertPropertyIsSet(self::OPEN_API_FILE);

        return $this;
    }

    /**
     * @module Syncapi
     *
     * @param string|null $openApiDocFile
     *
     * @return $this
     */
    public function setOpenApiDocFile($openApiDocFile)
    {
        $this->openApiDocFile = $openApiDocFile;
        $this->modifiedProperties[self::OPEN_API_DOC_FILE] = true;

        return $this;
    }

    /**
     * @module Syncapi
     *
     * @return string|null
     */
    public function getOpenApiDocFile()
    {
        return $this->openApiDocFile;
    }

    /**
     * @module Syncapi
     *
     * @param string|null $openApiDocFile
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return $this
     */
    public function setOpenApiDocFileOrFail($openApiDocFile)
    {
        if ($openApiDocFile === null) {
            $this->throwNullValueException(static::OPEN_API_DOC_FILE);
        }

        return $this->setOpenApiDocFile($openApiDocFile);
    }

    /**
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return string
     */
    public function getOpenApiDocFileOrFail()
    {
        if ($this->openApiDocFile === null) {
            $this->throwNullValueException(static::OPEN_API_DOC_FILE);
        }

        return $this->openApiDocFile;
    }

    /**
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireOpenApiDocFile()
    {
        $this->assertPropertyIsSet(self::OPEN_API_DOC_FILE);

        return $this;
    }

    /**
     * @module Syncapi
     *
     * @param string|null $openApiDoc
     *
     * @return $this
     */
    public function setOpenApiDoc($openApiDoc)
    {
        $this->openApiDoc = $openApiDoc;
        $this->modifiedProperties[self::OPEN_API_DOC] = true;

        return $this;
    }

    /**
     * @module Syncapi
     *
     * @return string|null
     */
    public function getOpenApiDoc()
    {
        return $this->openApiDoc;
    }

    /**
     * @module Syncapi
     *
     * @param string|null $openApiDoc
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return $this
     */
    public function setOpenApiDocOrFail($openApiDoc)
    {
        if ($openApiDoc === null) {
            $this->throwNullValueException(static::OPEN_API_DOC);
        }

        return $this->setOpenApiDoc($openApiDoc);
    }

    /**
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return string
     */
    public function getOpenApiDocOrFail()
    {
        if ($this->openApiDoc === null) {
            $this->throwNullValueException(static::OPEN_API_DOC);
        }

        return $this->openApiDoc;
    }

    /**
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException
     *
     * @return $this
     */
    public function requireOpenApiDoc()
    {
        $this->assertPropertyIsSet(self::OPEN_API_DOC);

        return $this;
    }

    /**
     * @module Syncapi
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
     * @module Syncapi
     *
     * @return string|null
     */
    public function getProjectRoot()
    {
        return $this->projectRoot;
    }

    /**
     * @module Syncapi
     *
     * @param string|null $projectRoot
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return $this
     */
    public function setProjectRootOrFail($projectRoot)
    {
        if ($projectRoot === null) {
            $this->throwNullValueException(static::PROJECT_ROOT);
        }

        return $this->setProjectRoot($projectRoot);
    }

    /**
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
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
     * @module Syncapi
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException
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
                case 'openApiFile':
                case 'openApiDocFile':
                case 'openApiDoc':
                case 'projectRoot':
                    $this->$normalizedPropertyName = $value;
                    $this->modifiedProperties[$normalizedPropertyName] = true;

                    break;
                default:
                    if (!$ignoreMissingProperty) {
                        throw new \InvalidArgumentException(sprintf('Missing property `%s` in `%s`', $property, static::class));
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
     * @param array<string, mixed>|\ArrayObject<string, mixed> $value
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
     * @param array<string, mixed>|\ArrayObject<string, mixed> $value
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
                case 'openApiFile':
                case 'openApiDocFile':
                case 'openApiDoc':
                case 'projectRoot':
                    $values[$arrayKey] = $value;

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
                case 'openApiFile':
                case 'openApiDocFile':
                case 'openApiDoc':
                case 'projectRoot':
                    $values[$arrayKey] = $value;

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
            'openApiFile' => $this->openApiFile,
            'openApiDocFile' => $this->openApiDocFile,
            'openApiDoc' => $this->openApiDoc,
            'projectRoot' => $this->projectRoot,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayNotRecursiveNotCamelCased(): array
    {
        return [
            'open_api_file' => $this->openApiFile,
            'open_api_doc_file' => $this->openApiDocFile,
            'open_api_doc' => $this->openApiDoc,
            'project_root' => $this->projectRoot,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveNotCamelCased(): array
    {
        return [
            'open_api_file' => $this->openApiFile instanceof AbstractTransfer ? $this->openApiFile->toArray(true, false) : $this->openApiFile,
            'open_api_doc_file' => $this->openApiDocFile instanceof AbstractTransfer ? $this->openApiDocFile->toArray(true, false) : $this->openApiDocFile,
            'open_api_doc' => $this->openApiDoc instanceof AbstractTransfer ? $this->openApiDoc->toArray(true, false) : $this->openApiDoc,
            'project_root' => $this->projectRoot instanceof AbstractTransfer ? $this->projectRoot->toArray(true, false) : $this->projectRoot,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArrayRecursiveCamelCased(): array
    {
        return [
            'openApiFile' => $this->openApiFile instanceof AbstractTransfer ? $this->openApiFile->toArray(true, true) : $this->openApiFile,
            'openApiDocFile' => $this->openApiDocFile instanceof AbstractTransfer ? $this->openApiDocFile->toArray(true, true) : $this->openApiDocFile,
            'openApiDoc' => $this->openApiDoc instanceof AbstractTransfer ? $this->openApiDoc->toArray(true, true) : $this->openApiDoc,
            'projectRoot' => $this->projectRoot instanceof AbstractTransfer ? $this->projectRoot->toArray(true, true) : $this->projectRoot,
        ];
    }
}
