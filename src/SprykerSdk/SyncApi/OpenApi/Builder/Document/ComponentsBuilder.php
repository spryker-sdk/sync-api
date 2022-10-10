<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use ArrayObject;
use Generated\Shared\Transfer\OpenApiDocumentComponentsTransfer;

class ComponentsBuilder implements ComponentsBuilderInterface
{
    /**
     * @var string
     */
    protected const FIELD_COMPONENTS = 'components';

    /**
     * @var string
     */
    protected const FIELD_PARAMETERS = 'parameters';

    /**
     * @var string
     */
    protected const FIELD_SCHEMAS = 'schemas';

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\ParameterBuilderInterface
     */
    protected $parameterBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\SchemaBuilderInterface
     */
    protected $schemaBuilder;

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\ParameterBuilderInterface $parameterBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\SchemaBuilderInterface $schemaBuilder
     */
    public function __construct(
        ParameterBuilderInterface $parameterBuilder,
        SchemaBuilderInterface $schemaBuilder
    ) {
        $this->parameterBuilder = $parameterBuilder;
        $this->schemaBuilder = $schemaBuilder;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentComponentsTransfer
     */
    public function build(array $openApiYamlAsArray): OpenApiDocumentComponentsTransfer
    {
        $components = new OpenApiDocumentComponentsTransfer();

        if ($this->parametersExist($openApiYamlAsArray)) {
            $components->setParameters($this->buildParameters($openApiYamlAsArray));
        }

        if ($this->schemasExist($openApiYamlAsArray)) {
            $components->setSchemas($this->buildSchemas($openApiYamlAsArray));
        }

        return $components;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return bool
     */
    protected function parametersExist(array $openApiYamlAsArray): bool
    {
        return isset($openApiYamlAsArray[static::FIELD_COMPONENTS][static::FIELD_PARAMETERS])
            && is_array($openApiYamlAsArray[static::FIELD_COMPONENTS][static::FIELD_PARAMETERS]);
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return bool
     */
    protected function schemasExist(array $openApiYamlAsArray): bool
    {
        return isset($openApiYamlAsArray[static::FIELD_COMPONENTS][static::FIELD_SCHEMAS])
            && is_array($openApiYamlAsArray[static::FIELD_COMPONENTS][static::FIELD_SCHEMAS]);
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return array
     */
    protected function getParametersArray(array $openApiYamlAsArray): array
    {
        return $openApiYamlAsArray[static::FIELD_COMPONENTS][static::FIELD_PARAMETERS];
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return array
     */
    protected function getSchemasArray(array $openApiYamlAsArray): array
    {
        return $openApiYamlAsArray[static::FIELD_COMPONENTS][static::FIELD_SCHEMAS];
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\OpenApiDocumentParameterTransfer[]
     */
    protected function buildParameters(array $openApiYamlAsArray): ArrayObject
    {
        $parameterTransfers = new \ArrayObject();

        foreach ($this->getParametersArray($openApiYamlAsArray) as $parameterName => $parameterAsArray) {
            $parameterTransfers->append($this->parameterBuilder->build($parameterName, $parameterAsArray));
        }

        return $parameterTransfers;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer[]
     */
    protected function buildSchemas(array $openApiYamlAsArray): ArrayObject
    {
        $schemaTransfers = new ArrayObject();

        foreach ($this->getSchemasArray($openApiYamlAsArray) as $schemaName => $schemaAsArray) {
            $schemaTransfers->append($this->schemaBuilder->build($schemaName, $schemaAsArray));
        }

        return $schemaTransfers;
    }
}
