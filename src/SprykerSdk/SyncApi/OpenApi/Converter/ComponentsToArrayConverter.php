<?php

namespace SprykerSdk\SyncApi\OpenApi\Converter;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class ComponentsToArrayConverter implements ComponentsToArrayConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $openApiDocumentTransfer
     *
     * @return array
     */
    public function convert(OpenApiDocumentTransfer $openApiDocumentTransfer): array
    {
        return [
            'parameters' => $this->convertParametersToArray($openApiDocumentTransfer),
            'schemas' => $this->convertSchemasToArray($openApiDocumentTransfer),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $openApiDocumentTransfer
     *
     * @return array
     */
    protected function convertParametersToArray(OpenApiDocumentTransfer $openApiDocumentTransfer): array
    {
        $parameters = [];

        foreach ($openApiDocumentTransfer->getComponents()->getParameters() as $parameterTransfer) {
            $parameters[$parameterTransfer->getName()] = $parameterTransfer->getContents();
        }

        return $parameters;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $openApiDocumentTransfer
     *
     * @return array
     */
    protected function convertSchemasToArray(OpenApiDocumentTransfer $openApiDocumentTransfer): array
    {
        $schemas = [];

        foreach ($openApiDocumentTransfer->getComponents()->getSchemas() as $schemaTransfer) {
            $schemas[$schemaTransfer->getName()] = $schemaTransfer->getContents();
        }

        return $schemas;
    }
}
