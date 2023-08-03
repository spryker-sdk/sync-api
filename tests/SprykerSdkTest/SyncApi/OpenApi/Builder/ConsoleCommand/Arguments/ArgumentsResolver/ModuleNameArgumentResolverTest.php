<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentsResolver;

use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use Codeception\Stub;
use Codeception\Test\Unit;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ModuleNameArgumentResolver;

/**
 * @group SprykerSdkTest
 * @group SyncApi
 * @group OpenApi
 * @group Builder
 * @group ConsoleCommand
 * @group Arguments
 * @group ArgumentsResolver
 * @group ModuleNameArgumentResolverTest
 */
class ModuleNameArgumentResolverTest extends Unit
{
    /**
     * @return array<array<\string>>
     */
    public function resourceToModuleNames(): array
    {
        return [
            ['/cat-face', 'CatFaceBackendApi', 'backend'],
            ['/cat-face_dog-nose', 'CatFaceDogNoseBackendApi', 'backend'],
            ['/cat-face', 'CatFaceStorefrontApi', 'frontend'],
            ['/cat-face_dog-nose', 'CatFaceDogNoseStorefrontApi', 'frontend'],
        ];
    }

    /**
     * @dataProvider resourceToModuleNames
     *
     * @param string $resource
     * @param string $expectedModuleName
     * @param string $applicationType
     *
     * @return void
     */
    public function testsConvertsResourceToModuleNameExtractedFromPath(string $resource, string $expectedModuleName, string $applicationType): void
    {
        $moduleNameArgumentResolver = new ModuleNameArgumentResolver();
        $pathItem = new PathItem([]);
        $operation = new Operation([]);

        $moduleName = $moduleNameArgumentResolver->resolve($resource, $pathItem, $operation, $applicationType);

        $this->assertSame($expectedModuleName, $moduleName);
    }

    /**
     * @return array<array<\string>>
     */
    public function pathExtensionToModuleNames(): array
    {
        return [
            ['CatFaceBackendApi', 'CatFace', 'backend'],
            ['CatFaceBackendApi', 'CatFaceBackendApi', 'backend'],
            ['CatFaceStorefrontApi', 'CatFace', 'frontend'],
            ['CatFaceStorefrontApi', 'CatFaceStorefrontApi', 'frontend'],
        ];
    }

    /**
     * @dataProvider pathExtensionToModuleNames
     *
     * @param string $expectedModuleName
     * @param string $extensionModuleName
     * @param string $applicationType
     *
     * @return void
     */
    public function testsConvertsExtensionInPathToModuleName(string $expectedModuleName, string $extensionModuleName, string $applicationType): void
    {
        $moduleNameArgumentResolver = new ModuleNameArgumentResolver();
        $pathItem = Stub::make(PathItem::class, ['getExtensions' => ['x-spryker' => ['module' => $extensionModuleName]]]);
        $operation = new Operation([]);

        $moduleName = $moduleNameArgumentResolver->resolve('', $pathItem, $operation, $applicationType);

        $this->assertSame($expectedModuleName, $moduleName);
    }

    /**
     * @dataProvider pathExtensionToModuleNames
     *
     * @param string $expectedModuleName
     * @param string $extensionModuleName
     * @param string $applicationType
     *
     * @return void
     */
    public function testsConvertsExtensionInOperationToModuleName(string $expectedModuleName, string $extensionModuleName, string $applicationType): void
    {
        $moduleNameArgumentResolver = new ModuleNameArgumentResolver();
        $pathItem = Stub::make(PathItem::class, ['getExtensions' => ['x-spryker' => ['module' => 'Foo']]]);
        $operation = Stub::make(Operation::class, ['getExtensions' => ['x-spryker' => ['module' => $extensionModuleName]]]);

        $moduleName = $moduleNameArgumentResolver->resolve('', $pathItem, $operation, $applicationType);

        $this->assertSame($expectedModuleName, $moduleName);
    }
}
