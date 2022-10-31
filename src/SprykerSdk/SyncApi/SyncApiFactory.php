<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use SprykerSdk\SyncApi\Message\MessageBuilder;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Merger\ComponentMerger;
use SprykerSdk\SyncApi\OpenApi\Merger\ComponentsCleaner;
use SprykerSdk\SyncApi\OpenApi\Merger\ComponentsCleanerInterface;
use SprykerSdk\SyncApi\OpenApi\Merger\InfoMerger;
use SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface;
use SprykerSdk\SyncApi\OpenApi\Merger\OpenApiMerger;
use SprykerSdk\SyncApi\OpenApi\Merger\PathsMerger;
use SprykerSdk\SyncApi\OpenApi\Merger\ServersMerger;
use SprykerSdk\SyncApi\OpenApi\Reader\OpenApiReader;
use SprykerSdk\SyncApi\OpenApi\Reader\OpenApiReaderInterface;
use SprykerSdk\SyncApi\OpenApi\Updater\OpenApiUpdater;
use SprykerSdk\SyncApi\OpenApi\Updater\OpenApiUpdaterInterface;
use SprykerSdk\SyncApi\OpenApi\Validator\OpenApiValidator;
use SprykerSdk\SyncApi\OpenApi\Validator\Rules\OpenApiComponentsValidatorRule;
use SprykerSdk\SyncApi\OpenApi\Validator\Rules\OpenApiHttpMethodInPathValidatorRule;
use SprykerSdk\SyncApi\OpenApi\Validator\Rules\OpenApiPathValidatorRule;
use SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface;

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
        return new OpenApiCodeBuilder($this->getConfig(), $this->createMessageBuilder(), $this->getInflector());
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
            $this->createMessageBuilder(),
            $this->getValidatorRules(),
        );
    }

    /**
     * @return array
     */
    public function getValidatorRules(): array
    {
        return [
            $this->createOpenApiPathValidator(),
            $this->createOpenApiComponentsValidator(),
            $this->createOpenApiHttpMethodInPathValidator(),
        ];
    }

    /**
     * @return \SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface
     */
    public function createOpenApiPathValidator(): ValidatorRuleInterface
    {
        return new OpenApiPathValidatorRule($this->createMessageBuilder());
    }

    /**
     * @return \SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface
     */
    public function createOpenApiComponentsValidator(): ValidatorRuleInterface
    {
        return new OpenApiComponentsValidatorRule($this->createMessageBuilder());
    }

    /**
     * @return \SprykerSdk\SyncApi\Validator\Rule\ValidatorRuleInterface
     */
    public function createOpenApiHttpMethodInPathValidator(): ValidatorRuleInterface
    {
        return new OpenApiHttpMethodInPathValidatorRule($this->createMessageBuilder());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilderInterface
     */
    public function createOpenApiBuilder(): OpenApiBuilderInterface
    {
        return new OpenApiBuilder($this->createMessageBuilder());
    }

    /**
     * @return \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    public function createMessageBuilder(): MessageBuilderInterface
    {
        return new MessageBuilder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Updater\OpenApiUpdaterInterface
     */
    public function createOpenApiUpdater(): OpenApiUpdaterInterface
    {
        return new OpenApiUpdater(
            $this->createMessageBuilder(),
            $this->getConfig(),
            $this->createOpenApiMerger(),
            $this->createOpenApiReader(),
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface
     */
    public function createOpenApiMerger(): MergerInterface
    {
        return new OpenApiMerger(
            $this->getMergerCollection(),
            $this->createComponentsCleaner(),
        );
    }

    public function createOpenApiReader(): OpenApiReaderInterface
    {
        return new OpenApiReader();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface
     */
    public function createInfoMerger(): MergerInterface
    {
        return new InfoMerger();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface
     */
    public function createServersMerger(): MergerInterface
    {
        return new ServersMerger();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface
     */
    public function createPathsMerger(): MergerInterface
    {
        return new PathsMerger($this->getConfig());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface
     */
    public function createComponentsMerger(): MergerInterface
    {
        return new ComponentMerger();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\ComponentsCleanerInterface
     */
    public function createComponentsCleaner(): ComponentsCleanerInterface
    {
        return new ComponentsCleaner();
    }

    /**
     * @return array<\SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface>
     */
    public function getMergerCollection(): array
    {
        return [
            $this->createInfoMerger(),
            $this->createServersMerger(),
            $this->createPathsMerger(),
            $this->createComponentsMerger(),
        ];
    }
}
