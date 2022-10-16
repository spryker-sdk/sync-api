<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\OpenApi\Merger;

use cebe\openapi\Writer;
use Codeception\Test\Unit;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @goupr OpenApi
 * @group Merger
 * @group ServersMergerTest
 */
class ServersMergerTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testInfoMergedSuccessfully(): void
    {
        // Arrange
        $serversMerger = $this->tester->getFactory()->createServersMerger();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/servers_target.yml');
        $sourceOpenApi = $this->tester->loadOpenApiFromYaml('merger/servers_source.yml');
        $expectedOpenApi = $this->tester->loadOpenApiFromYaml('merger/servers_expected.yml');

        // Act
        $actualOpenApi = $serversMerger->merge($targetOpenApi, $sourceOpenApi);

        // Assert
        $this->assertEquals(Writer::writeToJson($expectedOpenApi), Writer::writeToJson($actualOpenApi));
    }
}
