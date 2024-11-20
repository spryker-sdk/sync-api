<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Console;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\Writer;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @method \App\Zed\Payone\Business\PayoneFacadeInterface getFacade()
 * @method \App\Zed\Payone\Communication\PayoneCommunicationFactory getFactory()
 * @method \App\Zed\Payone\Persistence\PayoneRepositoryInterface getRepository()
 */
class OpenApiSchemaMergerConsole extends Console
{
    /**
     * @var string
     */
    public const COMMAND_NAME = 'openapi:schema:merge';

    /**
     * @var string
     */
    public const DESCRIPTION = 'This command merges OpenApi Specs from several sources and a root definition into one.';

    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME);
        $this->setDescription(static::DESCRIPTION);

        $this->addArgument('source', InputArgument::REQUIRED, 'The root file of the OpenApi schema which will be used to merge with other schemas.');
        $this->addArgument('target', InputArgument::REQUIRED, 'The target file name that should be created after merge.');

        $this->addOption('additional-schemas', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The additional OpenApi schema files that should be merged with the root schema.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = sprintf('%s/%s', getcwd(), $input->getArgument('source'));
        $target = sprintf('%s/%s', getcwd(), $input->getArgument('target'));

        $additionalSchemas = $input->getOption('additional-schemas');

        $yaml = new Yaml();
        $sourceSchema = $yaml->parseFile($source);

        $paths = $sourceSchema['paths'] ?? [];
        $schemas = $sourceSchema['components']['schemas'] ?? [];
        $parameters = $sourceSchema['components']['parameters'] ?? [];

        foreach ($additionalSchemas as $additionalSchema) {
            $additionalSchema = sprintf('%s/%s', getcwd(), $additionalSchema);
            $coreSchema = $yaml->parseFile($additionalSchema);
            $paths = $this->recursiveMerge($coreSchema['paths'] ?? [], $paths);
            $schemas = $this->recursiveMerge($coreSchema['components']['schemas'] ?? [], $schemas);
            $parameters = $this->recursiveMerge($coreSchema['components']['parameters'] ?? [], $parameters);
        }

        $mergedOpenApi = new OpenApi([
            'openapi' => $sourceSchema['openapi'],
            'info' => $sourceSchema['info'],
            'servers' => $sourceSchema['servers'],
            'paths' => $paths,
            'components' => [
                'schemas' => $schemas,
                'parameters' => $parameters,
            ],
            'security' => $sourceSchema['security'] ?? [],
            'tags' => $sourceSchema['tags'] ?? [],
            'externalDocs' => $sourceSchema['externalDocs'] ?? [],
        ]);

        // Save the merged specification to a file
        Writer::writeToYamlFile($mergedOpenApi, $target);

        return static::CODE_SUCCESS;
    }

    protected function recursiveMerge(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                $array1[$key] = $this->recursiveMerge($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }
}
