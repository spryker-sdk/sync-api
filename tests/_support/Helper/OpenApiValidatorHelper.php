<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Helper;

use Codeception\Module;

class OpenApiValidatorHelper extends Module
{
    use SyncApiHelperTrait;

    /**
     * @return void
     */
    public function haveValidOpenApiFile(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/openapi/valid/valid_openapi.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @return void
     */
    public function haveOpenApiFileThatCouldNotBeParsed(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/openapi/invalid/not_parsable_file.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @param array $files
     *
     * @return void
     */
    protected function prepareOpenApiSchema(array $files): void
    {
        $structure = [
            'config' => [
                'api' => [
                    'openapi' => $files,
                ],
            ],
        ];
        $this->getSyncApiHelper()->mockDirectoryStructure($structure);
    }
}
