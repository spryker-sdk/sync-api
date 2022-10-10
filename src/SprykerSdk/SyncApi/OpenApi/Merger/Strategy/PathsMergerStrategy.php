<?php

namespace SprykerSdk\SyncApi\OpenApi\Merger\Strategy;

use ArrayObject;
use Generated\Shared\Transfer\OpenApiDocumentParameterTransfer;
use Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer;
use Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer;
use Generated\Shared\Transfer\OpenApiDocumentSchemaTransfer;
use Generated\Shared\Transfer\OpenApiDocumentTransfer;
use SprykerSdk\SyncApi\OpenApi\Exception\ParameterConflictException;
use SprykerSdk\SyncApi\OpenApi\Exception\SchemaConflictException;

class PathsMergerStrategy implements MergerStrategyInterface
{
    /**
     * @var string
     */
    protected const EXCEPTION_MESSAGE_PARAMETER_CONFLICTS = 'Parameter "%s" has conflicts with new version. Please review it manually.';

    /**
     * @var string
     */
    protected const EXCEPTION_MESSAGE_SCHEMA_CONFLICTS = 'Schema "%s" has conflicts with new version. Please review it manually.';

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
                $parameters->offsetUnset($key);

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

        if ($this->refExistsInPathUriProtocols($targetOpenApiDocumentTransfer, $ref)) {
            return;
        }

        foreach ($schemas as $key => $schema) {
            if ($schema->getName() === $schemaName) {
                $schemaRefs = $schema->getRefs();
                $schemas->offsetUnset($key);

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

                $protocols->offsetUnset($key);
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

        $pathUriTransfer = $this->findPathUriTransfer($pathUriTransfers, $uri);

        if ($pathUriTransfer !== null) {
            $pathUriTransfer->addProtocol($sourcePathUriProtocolTransfer);
        } else {
            $pathUriTransfers->append(
                (new OpenApiDocumentPathUriTransfer())
                    ->setUri($uri)
                    ->addProtocol($sourcePathUriProtocolTransfer)
            );
        }

        $sourceRefs = $sourcePathUriProtocolTransfer->getRefs();

        foreach ($sourceRefs as $sourceRef) {
            $this->addRef($targetOpenApiDocumentTransfer, $sourceOpenApiDocumentTransfer, $sourceRef);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param string $sourceRef
     *
     * @return void
     */
    protected function addRef(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        string $sourceRef
    ): void {
        if ($this->isParameter($sourceRef)) {
            $this->addParameterByRef($targetOpenApiDocumentTransfer, $sourceOpenApiDocumentTransfer, $sourceRef);
        }

        if ($this->isSchema($sourceRef)) {
            $this->addSchemaByRef($targetOpenApiDocumentTransfer, $sourceOpenApiDocumentTransfer, $sourceRef);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param string $sourceRef
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function addParameterByRef(
        OpenApiDocumentTransfer  $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        string $sourceRef
    ): void {
        $targetParameter = $this->getParameterByRef($targetOpenApiDocumentTransfer, $sourceRef);
        $sourceParameter = $this->getParameterByRef($sourceOpenApiDocumentTransfer, $sourceRef);

        if ($targetParameter === null) {
            $this->addParameter($targetOpenApiDocumentTransfer, $sourceParameter);
            return;
        }

        if ($targetParameter && !$this->isIdenticalParamContents($targetParameter, $sourceParameter)) {
            throw new ParameterConflictException(
                sprintf(
                    static::EXCEPTION_MESSAGE_PARAMETER_CONFLICTS,
                    $targetParameter->getName()
                )
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocumentTransfer
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer
     * @param string $sourceRef
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function addSchemaByRef(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
        OpenApiDocumentTransfer $sourceOpenApiDocumentTransfer,
        string $sourceRef
    ): void {
        $targetSchema = $this->getSchemaByRef($targetOpenApiDocumentTransfer, $sourceRef);
        $sourceSchema = $this->getSchemaByRef($sourceOpenApiDocumentTransfer, $sourceRef);

        if ($targetSchema === null) {
            $this->addSchema($targetOpenApiDocumentTransfer, $sourceSchema);
            return;
        }

        if (!$this->isIdenticalSchemaContents($targetSchema, $sourceSchema)) {
            throw new SchemaConflictException(
                sprintf(
                    static::EXCEPTION_MESSAGE_SCHEMA_CONFLICTS,
                    $targetSchema->getName()
                )
            );
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

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer[] $pathUriTransfers
     * @param string $uri
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer|null
     */
    protected function findPathUriTransfer(ArrayObject $pathUriTransfers, string $uri): ?OpenApiDocumentPathUriTransfer
    {
        foreach ($pathUriTransfers as $pathUriTransfer) {
            if ($pathUriTransfer->getUri() === $uri) {
                return $pathUriTransfer;
            }
        }

        return null;
    }
}
