<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Validator\OpenApiValidator;
use SprykerSdk\SyncApi\Validator\Finder\Finder;
use SprykerSdk\SyncApi\Validator\Finder\FinderInterface;

class SyncApiFactory
{
    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig|null
     */
    protected ?SyncApiConfig $config = null;

    /**
     * @codeCoverageIgnore
     *
     * @param \SprykerSdk\SyncApi\SyncApiConfig $config
     *
     * @return void
     */
    public function setConfig(SyncApiConfig $config): void
    {
        $this->config = $config;
    }

    /**
     * @return \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected function getConfig(): SyncApiConfig
    {
        if (!$this->config) {
            $this->config = new SyncApiConfig();
        }

        return $this->config;
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilderInterface
     */
    public function createOpenApiCodeBuilder(): OpenApiCodeBuilderInterface
    {
        return new OpenApiCodeBuilder($this->getInflector());
    }

    /**
     * @return \Doctrine\Inflector\Inflector
     */
    public function getInflector(): Inflector
    {
        return InflectorFactory::create()->build();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Validator\OpenApiValidator
     */
    public function createOpenApiValidator(): OpenApiValidator
    {
        return new OpenApiValidator(
            $this->getConfig(),
            $this->createFinder(),
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilderInterface
     */
    public function createOpenApiBuilder(): OpenApiBuilderInterface
    {
        return new OpenApiBuilder();
    }

    /**
     * @return \SprykerSdk\SyncApi\Validator\Finder\FinderInterface
     */
    protected function createFinder(): FinderInterface
    {
        return new Finder();
    }
}
