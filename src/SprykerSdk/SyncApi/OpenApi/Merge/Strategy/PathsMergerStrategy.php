<?php

namespace SprykerSdk\SyncApi\OpenApi\Merge\Strategy;

use Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer;
use Generated\Shared\Transfer\OpenApiDocumentPathUriTransfer;
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

        if (!$this->refExistsInPathUriProtocols($ref)) {
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
     * @param \Generated\Shared\Transfer\OpenApiDocumentPathUriProtocolTransfer $sourcePathUriProtocolTransfer
     * @param string $uri
     *
     * @return void
     */
    protected function addPathUriProtocol(
        OpenApiDocumentTransfer $targetOpenApiDocumentTransfer,
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

        // TODO complete

        $pathUriTransfers[$key] = $targetPathUriTransfer;

        $sourceRefs = $sourcePathUriProtocolTransfer->getRefs();

        foreach ($sourceRefs as $sourceRef) {
            $this->addRef($)
        }
    }
}
