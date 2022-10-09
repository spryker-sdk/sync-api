<?php

namespace SprykerSdk\SyncApi\OpenApi\Merge\Strategy;

use Exception;
use Generated\Shared\Transfer\OpenApiDocumentParameterTransfer;
use Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer;
use Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer;
use Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer;
use Generated\Shared\Transfer\OpenApiDocumentTransfer;

class PathsMergerStrategy implements MergeStrategyInterface
{
    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param string|null $fieldToMerge
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentTransfer
     */
    public function merge(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        string $fieldToMerge = null
    ): OpenApiDocumentTransfer {
        $sourcePathTransfers = $sourceOpenApiDocumentTransfer->getPaths();

        foreach ($sourcePathTransfers->getPathUris() as $sourcePathUriTransfer) {
            foreach($sourcePathUriTransfer->getProtocols() as $sourcePathUriProtocolTransfer) {
                $targetPathUriProtocolTransfer = $this->findTargetPathUriProtocol(
                    $targetOpenApiDocumentTransfer,
                    $sourcePathUriTransfer->getUri(),
                    $sourcePathUriProtocolTransfer->getProtocol()
                );

                if ($targetPathUriProtocolTransfer !== null) {
                    $refsToRemove = $targetPathUriProtocolTransfer->getRefs();

                    $this->deletePathUriProtocolTransfer(
                        $targetOpenApiDocumentTransfer,
                        $sourcePathUriTransfer->getUri(),
                        $sourcePathUriProtocolTransfer->getProtocol()
                    );

                    $this->deleteComponentsByRefs($targetOpenApiDocumentTransfer, $refsToRemove);
                }

                $this->addPathUriProtocol(
                    $targetOpenApiDocumentTransfer,
                    $sourceOpenApiDocumentTransfer,
                    $sourcePathUriProtocolTransfer,
                    $sourcePathUriTransfer->getUri()
                );
            }
        }

        return $targetOpenApiDocumentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param string $uri
     * @param string $protocol
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer|null
     */
    protected function findTargetPathUriProtocol(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        string $uri,
        string $protocol
    ): ?OpenApiDocumentPathUriProtocolTransfer {
        $targetPathTransfers = $targetOpenApiDocumentTransfer->getPaths();

        foreach ($targetPathTransfers->getPathUris() as $targetPathUriTransfer) {
            if ($targetPathUriTransfer->getUri() !== $uri) {
                continue;
            }

            foreach ($targetPathUriTransfer->getProtocols() as $targetPathUriProtocolTransfer) {
                if ($targetPathUriProtocolTransfer->getProtocol() !== $protocol) {
                    continue;
                }

                return $targetPathUriProtocolTransfer;
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param array $refs
     *
     * @return void
     */
    protected function deleteComponentsByRefs(OpenApiDocumentTransfer $targetOpenApiDocumentTransfer, array $refs): void
    {
        foreach ($refs as $ref) {
            if ($this->isParameter($ref)) {
                $this->removeParameterByRef($targetOpenApiDocumentTransfer, $ref);
            }

            if ($this->isSchema($ref)) {
                $this->removeSchemaByRef($targetOpenApiDocumentTransfer, $ref);
            }
        }
    }

    /**
     * @param string $ref
     *
     * @return bool
     */
    protected function isParameter(string $ref): bool
    {
        return strpos($ref, '/parameters/') !== false;
    }

    /**
     * @param string $ref
     *
     * @return bool
     */
    protected function isSchema(string $ref): bool
    {
        return strpos($ref, '/schemas/') !== false;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param string $ref
     *
     * @return void
     */
    protected function removeParameterByRef(OpenApiDocumentTransfer $targetOpenApiDocumentTransfer, string $ref): void
    {
        $parameterName = $this->getObjectName($ref);

        $parameters = $targetOpenApiDocumentTransfer->getComponents()->getParameters();

        foreach ($parameters as $key => $parameter) {
            if ($parameter->getName() === $parameterName) {
                unset($parameters[$key]);
                $targetOpenApiDocumentTransfer->getComponents()->setParameters($parameters);

                break;
            }
        }


    }

    /**
     * @param string $ref
     *
     * @return string
     */
    protected function getObjectName(string $ref): string
    {
        $refParts = explode('/', $ref);

        return end($refParts);
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param string $ref
     *
     * @return void
     */
    private function removeSchemaByRef(OpenApiDocumentTransfer $targetOpenApiDocumentTransfer, string $ref): void
    {
        $schemaName = $this->getObjectName($ref);

        $schemas = $targetOpenApiDocumentTransfer->getComponents()->getSchemas();

        if (!$this->refExistsInPathUriProtocols($targetOpenApiDocumentTransfer, $ref)) {
            return;
        }

        foreach ($schemas as $key => $schema) {
            if ($schemas->getName() === $schemaName) {
                $schemaRefs = $schema->getRefs();
                unset($schemas[$key]);
                $targetOpenApiDocumentTransfer->getComponents()->setSchemas($schemas);

                foreach ($schemaRefs as $schemaRef)  {
                    $this->removeSchemaByRef($targetOpenApiDocumentTransfer, $schemaRef);
                }

                break;
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param string $uri
     * @param string $protocol
     *
     * @return void
     */
    protected function deletePathUriProtocolTransfer(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        string $uri,
        string $protocol
    ): void {
        $targetPathTransfers = $targetOpenApiDocumentTransfer->getPaths();

        foreach ($targetPathTransfers->getPathUris() as $targetPathUriTransfer) {
            if ($targetPathUriTransfer->getUri() !== $uri) {
                continue;
            }

            $protocols = $targetPathUriTransfer->getProtocols();

            foreach ($protocols as $key => $targetPathUriProtocolTransfer) {
                if ($targetPathUriProtocolTransfer->getProtocol() !== $protocol) {
                    continue;
                }

                unset($protocols[$key]);

                $targetPathUriTransfer->setProtocols($protocols);
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer $sourcePathUriProtocolTransfer
     * @param string $uri
     *
     * @return void
     */
    protected function addPathUriProtocol(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        OpenApiDocumentPathUriProtocolTransfer $sourcePathUriProtocolTransfer,
        string $uri
    ) {
        $pathUriTransfers = $targetOpenApiDocumentTransfer->getPaths()->getPathUris();

        $targetPathUriTransfer = (new OpenApiDocumentPathUriTransfer())->setUri($uri);

        foreach ($pathUriTransfers as $key => $pathUriTransfer) {
            if ($pathUriTransfer->getUri() === $uri) {
                $pathUriTransfer->addProtocol($sourcePathUriProtocolTransfer);
                break;
            }
        }

        $pathUriTransfers[$key] = $targetPathUriTransfer;

        $sourceRefs = $sourcePathUriProtocolTransfer->getRefs();

        foreach ($sourceRefs as $sourceRef) {
            $this->addRef($targetOpenApiDocumentTransfer, $sourceOpenApiDocumentTransfer, $sourcePathUriProtocolTransfer, $sourceRef);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer $sourcePathUriProtocolTransfer
     * @param string $sourceRef
     *
     * @return void
     */
    protected function addRef(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        OpenApiDocumentPathUriProtocolTransfer $sourcePathUriProtocolTransfer,
        string $sourceRef
    ): void {
        if ($this->isParameter($sourceRef)) {
            $targetParameter = $this->getParameterByRef($targetOpenApiDocumentTransfer, $sourceRef);
            $sourceParameter = $this->getParameterByRef($sourceOpenApiDocumentTransfer, $sourceRef);

            if ($targetParameter === null) {
                $this->addParameter($targetOpenApiDocumentTransfer, $sourceParameter);
                return;
            }

            if (!$this->isIdenticalParamContents($targetParameter, $sourceParameter)) {
                throw new Exception('Parameter contents conflict');
            }
        }

        if ($this->isSchema($sourceRef)) {
            $targetSchemaContents = $this->getSchemaByRef($targetOpenApiDocumentTransfer, $sourceRef);
            $sourceSchemaContents = $this->getSchemaByRef($sourceOpenApiDocumentTransfer, $sourceRef);

            if ($targetSchemaContents === null) {
                $this->addSchema($targetOpenApiDocumentTransfer, $sourceSchemaContents);
            }

            if (!$this->isIdenticalSchemaContents($targetSchemaContents, $sourceSchemaContents)) {
                throw new Exception('Schema contents conflict');
            }
        }
    }


    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param string $ref
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentParameterTransfer|null
     */
    protected function getParameterByRef(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        string $ref
    ): ?OpenApiDocumentParameterTransfer {
        $objectName = $this->getObjectName($ref);

        foreach($targetOpenApiDocumentTransfer->getComponents()->getParameters() as $parameter) {
            if ($parameter->getName() === $objectName) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $openApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentParameterTransfer $parameterTransfer
     *
     * @return void
     */
    protected function addParameter(
        OpenApiDocumentTransfer $openApiDocumentTransfer,
        OpenApiDocumentParameterTransfer $parameterTransfer
    ) {
        $openApiDocumentTransfer->getComponents()->addParameter($parameterTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentParameterTransfer $targetParameter
     * @param \Generated\Shared\Transfer\OpenApiDocumentParameterTransfer $sourceParameter
     *
     * @return bool
     */
    protected function isIdenticalParamContents(
        OpenApiDocumentParameterTransfer $targetParameter,
        OpenApiDocumentParameterTransfer $sourceParameter
    ): bool {
        return $targetParameter->getContents() === $sourceParameter->getContents();
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param string $ref
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer|null
     */
    protected function getSchemaByRef(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        string $ref
    ): ?OpenApiDocumentSchemaTransfer {
        $objectName = $this->getObjectName($ref);

        foreach($targetOpenApiDocumentTransfer->getComponents()->getSchemas() as $schema) {
            if ($schema->getName() === $objectName) {
                return $schema;
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $documentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer $sourceSchemaContents
     *
     * @return void
     */
    private function addSchema(
        OpenApiDocumentTransfer $documentTransfer,
        OpenApiDocumentSchemaTransfer $sourceSchemaContents
    ): void {
        $documentTransfer->getComponents()->addSchema($sourceSchemaContents);
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $documentTransfer
     * @param string $ref
     *
     * @return bool
     */
    protected function refExistsInPathUriProtocols(
        OpenApiDocumentTransfer $documentTransfer,
        string $ref
    ): bool {
        foreach ($documentTransfer->getPaths()->getPathUris() as $pathUri) {
            foreach($pathUri->getProtocols() as $protocol) {
                if (in_array($ref, $protocol->getRefs())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer $targetSchemaContents
     * @param \Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer $sourceSchemaContents
     *
     * @return bool
     */
    protected function isIdenticalSchemaContents(
        OpenApiDocumentSchemaTransfer $targetSchemaContents,
        OpenApiDocumentSchemaTransfer $sourceSchemaContents
    ): bool {
        return $targetSchemaContents->getContents() === $sourceSchemaContents->getContents();
    }
}
