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
 * @group PathsMergerTest
 */
class PathsMergerTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testPathMergedSuccessfully(): void
    {
        // Arrange
        $pathMerger = $this->tester->getFactory()->createPathsMerger();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/target_openapi.yml');
        $sourceOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/source_openapi.yml');
        $expectedOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/expected_openapi.yml');

        // Act
        $actualOpenApi = $pathMerger->merge($targetOpenApi, $sourceOpenApi);

        // Assert
        $this->assertSame(Writer::writeToYaml($expectedOpenApi), Writer::writeToYaml($actualOpenApi));
    }
}