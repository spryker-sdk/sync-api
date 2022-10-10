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
use SprykerSdk\SyncApi\OpenApi\Builder\Document\ComponentsBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\ComponentsBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\DocumentBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\DocumentBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\InfoBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\InfoBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\ParameterBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\ParameterBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\PathsBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\PathsBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriProtocolsBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriProtocolsBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\RefsFinder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\RefsFinderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\SchemaBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\SchemaBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\ServersBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\ServersBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Converter\ComponentsToArrayConverter;
use SprykerSdk\SyncApi\OpenApi\Converter\ComponentsToArrayConverterInterface;
use SprykerSdk\SyncApi\OpenApi\Converter\OpenApiDocumentToArrayConverter;
use SprykerSdk\SyncApi\OpenApi\Converter\OpenApiDocumentToArrayConverterInterface;
use SprykerSdk\SyncApi\OpenApi\Converter\PathsToArrayConverter;
use SprykerSdk\SyncApi\OpenApi\Converter\PathsToArrayConverterInterface;
use SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface;
use SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocJsonDecoder;
use SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManager;
use SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface;
use SprykerSdk\SyncApi\OpenApi\Merger\Strategy\MergerStrategyInterface;
use SprykerSdk\SyncApi\OpenApi\Merger\Strategy\PathsMergerStrategy;
use SprykerSdk\SyncApi\OpenApi\Merger\Strategy\ReplaceRecursiveContentsMergerStrategy;
use SprykerSdk\SyncApi\OpenApi\Merger\Strategy\ReplaceValueMergerStrategy;
use SprykerSdk\SyncApi\OpenApi\Merger\Strategy\ServersMergerStrategy;
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
        return new OpenApiBuilder(
            $this->createMessageBuilder(),
            $this->createOpenApiFileManager(),
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    public function createMessageBuilder(): MessageBuilderInterface
    {
        return new MessageBuilder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface
     */
    public function createFilepathBuilder(): FilepathBuilderInterface
    {
        return new FilepathBuilder($this->getConfig()->getProjectRootPath());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface
     */
    public function createOpenApiDocDecoder(): OpenApiDocDecoderInterface
    {
        return new OpenApiDocJsonDecoder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface
     */
    public function createOpenApiFileManager(): OpenApiFileManagerInterface
    {
        return new OpenApiFileManager();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Updater\OpenApiUpdaterInterface
     */
    public function createOpenApiUpdater(): OpenApiUpdaterInterface
    {
        return new OpenApiUpdater(
            $this->createMessageBuilder(),
            $this->createFilepathBuilder(),
            $this->createOpenApiDocDecoder(),
            $this->createOpenApiValidator(),
            $this->createOpenApiFileManager(),
            $this->getConfig(),
            $this->createOpenApiDocumentBuilder(),
            $this->getMergeStrategyCollection(),
            $this->createOpenApiDocumentToArrayConverter()
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\DocumentBuilderInterface
     */
    public function createOpenApiDocumentBuilder(): DocumentBuilderInterface
    {
        return new DocumentBuilder(
            $this->createOpenApiDocumentInfoBuilder(),
            $this->createOpenApiDocumentServersBuilder(),
            $this->createOpenApiDocumentPathsBuilder(),
            $this->createOpenApiDocumentComponentsBuilder(),
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\InfoBuilderInterface
     */
    public function createOpenApiDocumentInfoBuilder(): InfoBuilderInterface
    {
        return new InfoBuilder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\ServersBuilderInterface
     */
    public function createOpenApiDocumentServersBuilder(): ServersBuilderInterface
    {
        return new ServersBuilder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathsBuilderInterface
     */
    public function createOpenApiDocumentPathsBuilder(): PathsBuilderInterface
    {
        return new PathsBuilder($this->createOpenApiDocumentPathUriBuilder());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\ComponentsBuilderInterface
     */
    public function createOpenApiDocumentComponentsBuilder(): ComponentsBuilderInterface
    {
        return new ComponentsBuilder(
            $this->createOpenApiDocumentParameterBuilder(),
            $this->createOpenApiDocumentSchemaBuilder(),
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriBuilderInterface
     */
    public function createOpenApiDocumentPathUriBuilder(): PathUriBuilderInterface
    {
        return new PathUriBuilder(
            $this->createOpenApiDocumentPathUriProtocolBuilder(),
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\ParameterBuilderInterface
     */
    public function createOpenApiDocumentParameterBuilder(): ParameterBuilderInterface
    {
        return new ParameterBuilder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\SchemaBuilderInterface
     */
    public function createOpenApiDocumentSchemaBuilder(): SchemaBuilderInterface
    {
        return new SchemaBuilder($this->createRefsFinder());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriProtocolsBuilderInterface
     */
    public function createOpenApiDocumentPathUriProtocolBuilder(): PathUriProtocolsBuilderInterface
    {
        return new PathUriProtocolsBuilder($this->createRefsFinder());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\RefsFinderInterface
     */
    public function createRefsFinder(): RefsFinderInterface
    {
        return new RefsFinder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\Strategy\MergerStrategyInterface
     */
    public function createReplaceStrategy(): MergerStrategyInterface
    {
        return new ReplaceValueMergerStrategy();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\Strategy\MergerStrategyInterface
     */
    public function createReplaceRecursiveMergeStrategy(): MergerStrategyInterface
    {
        return new ReplaceRecursiveContentsMergerStrategy();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\Strategy\MergerStrategyInterface
     */
    public function createServersMergeStrategy(): MergerStrategyInterface
    {
        return new ServersMergerStrategy();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Merger\Strategy\MergerStrategyInterface
     */
    public function createPathMergeStrategy(): MergerStrategyInterface
    {
        return new PathsMergerStrategy();
    }

    /**
     * @return array<string, MergerStrategyInterface>
     */
    public function getMergeStrategyCollection(): array
    {
        return [
            SyncApiConfig::STRATEGY_REPLACE => $this->createReplaceStrategy(),
            SyncApiConfig::STRATEGY_REPLACE_RECURSIVE => $this->createReplaceRecursiveMergeStrategy(),
            SyncApiConfig::STRATEGY_SERVERS_MERGE => $this->createServersMergeStrategy(),
            SyncApiConfig::STRATEGY_PATHS_MERGE => $this->createPathMergeStrategy(),
        ];
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Converter\OpenApiDocumentToArrayConverterInterface
     */
    public function createOpenApiDocumentToArrayConverter(): OpenApiDocumentToArrayConverterInterface
    {
        return new OpenApiDocumentToArrayConverter(
            $this->createOpenApiDocumentPathsToArrayConverter(),
            $this->createOpenApiDocumentComponentsToArrayConverter(),
        );
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Converter\PathsToArrayConverterInterface
     */
    public function createOpenApiDocumentPathsToArrayConverter(): PathsToArrayConverterInterface
    {
        return new PathsToArrayConverter();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Converter\ComponentsToArrayConverterInterface
     */
    public function createOpenApiDocumentComponentsToArrayConverter(): ComponentsToArrayConverterInterface
    {
        return new ComponentsToArrayConverter();
    }
}
