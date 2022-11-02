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
 * @group ComponentsMergerTest
 */
class ComponentsMergerTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testComponentsMergedSuccessFully(): void
    {
        // Arrange
        $componentsMerger = $this->tester->getFactory()->createComponentsMerger();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/components/target_openapi.yml');
        $sourceOpenApi = $this->tester->loadOpenApiFromYaml('merger/components/source_openapi.yml');
        $expectedOpenApi = $this->tester->loadOpenApiFromYaml('merger/components/expected_openapi.yml');

        // Act
        $actualOpenApi = $componentsMerger->merge($targetOpenApi, $sourceOpenApi);

        // Assert
        $this->assertSame(Writer::writeToYaml($expectedOpenApi), Writer::writeToYaml($actualOpenApi));
    }
}
