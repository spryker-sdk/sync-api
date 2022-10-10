<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentPathsTransfer;

class PathsBuilder implements PathsBuilderInterface
{
    /**
     * @var string
     */
    protected const FIELD_PATHS = 'paths';

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriBuilderInterface
     */
    protected $pathUriBuilder;

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriBuilderInterface $pathUriBuilder
     */
    public function __construct(PathUriBuilderInterface $pathUriBuilder)
    {
        $this->pathUriBuilder = $pathUriBuilder;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentPathsTransfer
     */
    public function build(array $openApiYamlAsArray): OpenApiDocumentPathsTransfer
    {
        $paths = new OpenApiDocumentPathsTransfer();

        if ($this->pathsExist($openApiYamlAsArray)) {
            foreach ($this->getPaths($openApiYamlAsArray) as $route => $pathUriProtocols) {
                $paths->addPathUri($this->pathUriBuilder->build($route, $pathUriProtocols));
            }
        }

        return $paths;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return bool
     */
    protected function pathsExist(array $openApiYamlAsArray): bool
    {
        return isset($openApiYamlAsArray[static::FIELD_PATHS]) && is_array($openApiYamlAsArray[static::FIELD_PATHS]);
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return array
     */
    private function getPaths(array $openApiYamlAsArray): array
    {
        return $openApiYamlAsArray[static::FIELD_PATHS];
    }
}
