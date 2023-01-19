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
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ArgumentResolverInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ModuleNameArgumentResolver;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunner;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunnerInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\GlueResourceMethodResponseCommand;
use SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\TransferCommand;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilder;
use SprykerSdk\SyncApi\OpenApi\Builder\OpenApiCodeBuilderInterface;
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
        return new OpenApiCodeBuilder($this->getConfig(), $this->createMessageBuilder(), $this->getInflector(), $this->getCommandRunner());
    }

    /**
     * @return array<\SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandInterface>
     */
    public function getCommandRunner(): array
    {
        return [
            $this->createGlueResourceMethodResponseCommandRunner(),
            $this->createTransferCommandRunner(),
        ];
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandInterface
     */
    public function createGlueResourceMethodResponseCommandRunner(): CommandInterface
    {
        return new GlueResourceMethodResponseCommand($this->getConfig(), $this->createMessageBuilder(), $this->createModuleNameArgumentResolver(), $this->createCommandRunner());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Arguments\ArgumentResolver\ArgumentResolverInterface
     */
    public function createModuleNameArgumentResolver(): ArgumentResolverInterface
    {
        return new ModuleNameArgumentResolver();
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandInterface
     */
    public function createTransferCommandRunner(): CommandInterface
    {
        return new TransferCommand($this->getConfig(), $this->createMessageBuilder(), $this->getInflector(), $this->createModuleNameArgumentResolver(), $this->createCommandRunner());
    }

    /**
     * @return \SprykerSdk\SyncApi\OpenApi\Builder\ConsoleCommand\Command\CommandRunnerInterface
     */
    public function createCommandRunner(): CommandRunnerInterface
    {
        return new CommandRunner($this->getConfig());
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
}
