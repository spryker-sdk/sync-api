<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\SyncApi\Console;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\Writer;
use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @method \App\Zed\Payone\Business\PayoneFacadeInterface getFacade()
 * @method \App\Zed\Payone\Communication\PayoneCommunicationFactory getFactory()
 * @method \App\Zed\Payone\Persistence\PayoneRepositoryInterface getRepository()
 */
class OpenApiSchemaMergeConsole extends Console
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectSchemaPath = APPLICATION_ROOT_DIR . '/config/app/payone/api/openapi/payone.yml';
        $projectSchemaPath = APPLICATION_ROOT_DIR . '/config/app/payone/api/openapi/payone-full.yml';
        $generatedSchemaPath = APPLICATION_ROOT_DIR . '/resources/api/openapi.yml';

        $coreSchemaPaths = [
//            APPLICATION_ROOT_DIR . '/vendor/spryker/app-payment/resources/api/openapi.yml',
//            APPLICATION_ROOT_DIR . '/vendor/spryker/app-kernel/resources/api/openapi.yml',
            APPLICATION_ROOT_DIR . '/config/app/bazaarvoice/api/openapi/bazaarvoice.yml',
            APPLICATION_ROOT_DIR . '/config/app/usercentrics/api/openapi/usercentrics.yml',
        ];

        $yaml = new Yaml();
        $projectSchema = $yaml->parseFile($projectSchemaPath);

        $paths = $projectSchema['paths'] ?? [];
        $schemas = $projectSchema['components']['schemas'] ?? [];
        $parameters = $projectSchema['components']['parameters'] ?? [];

        foreach ($coreSchemaPaths as $item) {
            $coreSchema = $yaml->parseFile($item);
            $paths = $this->recursiveMerge($coreSchema['paths'] ?? [], $paths);
            $schemas = $this->recursiveMerge($coreSchema['components']['schemas'] ?? [], $schemas);
            $parameters = $this->recursiveMerge($coreSchema['components']['parameters'] ?? [], $parameters);
        }

        $mergedOpenApi = new OpenApi([
            'openapi' => $projectSchema['openapi'],
            'info' => $projectSchema['info'],
            'servers' => $projectSchema['servers'],
            'paths' => $paths,
            'components' => [
                'schemas' => $schemas,
                'parameters' => $parameters,
            ],
            'security' => $projectSchema['security'] ?? [],
            'tags' => $projectSchema['tags'] ?? [],
            'externalDocs' => $projectSchema['externalDocs'] ?? [],
        ]);

        // Save the merged specification to a file
        Writer::writeToYamlFile($mergedOpenApi, $generatedSchemaPath);

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
