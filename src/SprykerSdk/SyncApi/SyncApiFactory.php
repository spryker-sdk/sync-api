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
use SprykerSdk\SyncApi\OpenApi\DataModifier\DataModifierHandlerInterface;
use SprykerSdk\SyncApi\OpenApi\DataModifier\DataSimpleRecursiveReplacer;
use SprykerSdk\SyncApi\OpenApi\DataModifier\SyncApiHeaderSetter;
use SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface;
use SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocJsonDecoder;
use SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManager;
use SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface;
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
        return new FilepathBuilder(
            $this->getConfig()->getProjectRootPath(),
            $this->getConfig()->getSyncApiDirPath(),
        );
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
     * @return \SprykerSdk\SyncApi\OpenApi\DataModifier\DataModifierHandlerInterface
     */
    public function createSyncApiUpdateDataModifier(): DataModifierHandlerInterface
    {
        return new DataSimpleRecursiveReplacer(
            new SyncApiHeaderSetter(null),
        );
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
            $this->createSyncApiUpdateDataModifier(),
            $this->getConfig()->getDefaultAbsolutePathToOpenApiFile()
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
        return new SchemaBuilder();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\Document\PathUriProtocolsBuilderInterface
     */
    public function createOpenApiDocumentPathUriProtocolBuilder(): PathUriProtocolsBuilderInterface
    {
        return new PathUriProtocolsBuilder();
    }
}
