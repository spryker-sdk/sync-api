<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi;

use Codeception\Test\Unit;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilderInterface;
use SprykerSdk\SyncApi\SyncApiFactory;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group SyncApiFactoryTest
 */
class SyncApiFactoryTest extends Unit
{
    /**
     * @var \SprykerSdkTest\SyncApi\SyncApiTester
     */
    protected SyncApiTester $tester;

    /**
     * @return void
     */
    public function testCreateOpenApiCodeBuilder(): void
    {
        $factory = new SyncApiFactory();
        $this->assertInstanceOf(OpenApiCodeBuilderInterface::class, $factory->createOpenApiCodeBuilder());
    }
}
