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
 * @group ComponentsCleanerTest
 */
class ComponentsCleanerTest extends Unit
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
        $componentsCleaner = $this->tester->getFactory()->createComponentsCleaner();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/cleaner/target_openapi.yml');
        $expectedOpenApi = $this->tester->loadOpenApiFromYaml('merger/cleaner/expected_openapi.yml');

        // Act
        $actualOpenApi = $componentsCleaner->cleanUnused($targetOpenApi);

        // Assert
        $this->assertSame(Writer::writeToYaml($expectedOpenApi), Writer::writeToYaml($actualOpenApi));
    }
}
