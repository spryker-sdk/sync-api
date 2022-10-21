<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\OpenApi\Merger;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Server;

class ServersMerger implements MergerInterface
{
    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\OpenApi $sourceOpenApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    public function merge(OpenApi $targetOpenApi, OpenApi $sourceOpenApi): OpenApi
    {
        if ($sourceOpenApi->servers) {
            foreach ($sourceOpenApi->servers as $sourceServer) {
                $targetServer = $this->findTargetServer($targetOpenApi, $sourceServer);

                if ($targetServer !== null) {
                    $targetServer->description = $sourceServer->description;
                } else {
                    $targetOpenApi->servers = array_merge($targetOpenApi->servers, [$sourceServer]);
                }
            }
        }

        return $this->cleanupDefaultEmptyServer($targetOpenApi);
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     * @param \cebe\openapi\spec\Server $sourceServer
     *
     * @return \cebe\openapi\spec\Server|null
     */
    protected function findTargetServer(OpenApi $targetOpenApi, Server $sourceServer): ?Server
    {
        foreach ($targetOpenApi->servers as $targetServer) {
            if ($sourceServer->url === $targetServer->url) {
                return $targetServer;
            }
        }

        return null;
    }

    /**
     * Description: This action is necessary due to openapi lib bug/feature. It adds a default server with url="/" and
     *              without description when OpenApi created from schema without servers.
     *
     * @param \cebe\openapi\spec\OpenApi $targetOpenApi
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function cleanupDefaultEmptyServer(OpenApi $targetOpenApi): OpenApi
    {
        $servers = [];

        foreach ($targetOpenApi->servers as $server) {
            if ($server->url === '/' && !$server->description) {
                continue;
            }

            $servers[] = $server;
        }

        $targetOpenApi->servers = $servers;

        return $targetOpenApi;
    }
}
