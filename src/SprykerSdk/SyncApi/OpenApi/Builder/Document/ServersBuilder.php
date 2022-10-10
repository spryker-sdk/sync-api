<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use ArrayObject;
use Generated\Shared\Transfer\OpenApiDocumentServerTransfer;

class ServersBuilder implements ServersBuilderInterface
{
    /**
     * @var string
     */
    protected const FIELD_SERVERS = 'servers';
    protected const FIELD_SERVER_DESCRIPTION = 'description';
    protected const FIELD_SERVER_URL = 'url';

    /**
     * @param array $openApiYamlAsArray
     *
     * @return ArrayObject|\Generated\Shared\Transfer\OpenApiDocumentServerTransfer[]
     */
    public function build(array $openApiYamlAsArray): ArrayObject
    {
        $servers = new ArrayObject();

        if ($this->serversExist($openApiYamlAsArray)) {
            foreach ($this->getServers($openApiYamlAsArray) as $serverArray) {
                $server = $this->buildServer($serverArray);

                $servers->append($server);
            }
        }

        return $servers;
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return bool
     */
    protected function serversExist(array $openApiYamlAsArray): bool
    {
        return isset($openApiYamlAsArray[static::FIELD_SERVERS]) && is_array($openApiYamlAsArray[static::FIELD_SERVERS]);
    }

    /**
     * @param array $openApiYamlAsArray
     *
     * @return array
     */
    protected function getServers(array $openApiYamlAsArray): array
    {
        return $openApiYamlAsArray[static::FIELD_SERVERS];
    }

    /**
     * @param array $serverArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentServerTransfer
     */
    protected function buildServer(array $serverArray): OpenApiDocumentServerTransfer
    {
        return (new OpenApiDocumentServerTransfer())
            ->setDescription($serverArray[static::FIELD_SERVER_DESCRIPTION] ?? '')
            ->setUrl($serverArray[static::FIELD_SERVER_URL] ?? '');
    }
}
