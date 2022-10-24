<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\OpenApi\Merger;

use cebe\openapi\Writer;
use Codeception\Test\Unit;
use SprykerSdkTest\SyncApi\SyncApiTester;

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
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testInfoMergedSuccessfully(): void
    {
        // Arrange
        $serversMerger = $this->tester->getFactory()->createServersMerger();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/servers/target_openapi.yml');
        $sourceOpenApi = $this->tester->loadOpenApiFromYaml('merger/servers/source_openapi.yml');
        $expectedOpenApi = $this->tester->loadOpenApiFromYaml('merger/servers/expected_openapi.yml');

        // Act
        $actualOpenApi = $serversMerger->merge($targetOpenApi, $sourceOpenApi);

        // Assert
        $this->assertEquals(Writer::writeToJson($expectedOpenApi), Writer::writeToJson($actualOpenApi));
    }
}
