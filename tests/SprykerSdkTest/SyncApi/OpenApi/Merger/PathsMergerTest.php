<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\OpenApi\Merger;

use cebe\openapi\Writer;
use Codeception\Test\Unit;
use SprykerSdk\SyncApi\OpenApi\Merger\Exception\ParameterNotFoundInSourceOpenApiException;
use SprykerSdk\SyncApi\OpenApi\Merger\Exception\SchemaNotFoundInSourceOpenApiException;
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
        $pathMerger = $this->tester->getFactory()->createPathMerger();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/target_openapi.yml');
        $sourceOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/source_openapi.yml');
        $expectedOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/expected_openapi.yml');

        // Act
        $actualOpenApi = $pathMerger->merge($targetOpenApi, $sourceOpenApi);

        // Assert
        $this->assertSame(Writer::writeToYaml($expectedOpenApi), Writer::writeToYaml($actualOpenApi));
    }

    /**
     * @return void
     */
    public function testPathMergerThrowsExceptionWhenParameterIsMissedInSourceOpenApi(): void
    {
        // Arrange
        $pathMerger = $this->tester->getFactory()->createPathMerger();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/target_openapi.yml');
        $sourceOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/source_openapi_with_parameter_missing.yml');

        // Assert
        $this->expectException(ParameterNotFoundInSourceOpenApiException::class);

        // Act
        $pathMerger->merge($targetOpenApi, $sourceOpenApi);
    }

    /**
     * @return void
     */
    public function testPathMergerThrowsExceptionWhenSchemaIsMissedInSourceOpenApi(): void
    {
        // Arrange
        $pathMerger = $this->tester->getFactory()->createPathMerger();

        $targetOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/target_openapi.yml');
        $sourceOpenApi = $this->tester->loadOpenApiFromYaml('merger/paths/source_openapi_with_schema_missing.yml');

        // Assert
        $this->expectException(SchemaNotFoundInSourceOpenApiException::class);

        // Act
        $pathMerger->merge($targetOpenApi, $sourceOpenApi);
    }
}
