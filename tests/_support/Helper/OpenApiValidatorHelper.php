<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdkTest\Helper;

use Codeception\Module;
use SprykerSdk\SyncApi\Console\AbstractConsole;

class OpenApiValidatorHelper extends Module
{
    use SyncApiHelperTrait;

    /**
     * @return void
     */
    public function haveValidOpenApiFile(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/valid/valid_openapi.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @return void
     */
    public function haveOpenApiFileThatCouldNotBeParsed(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/invalid/not_parsable_file.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @return void
     */
    public function haveDefaultOpenApiFile(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/invalid/empty_openapi.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @return void
     */
    public function haveInvalidOpenApiFile(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/invalid/invalid_openapi.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @return void
     */
    public function haveOpenApiFileWithPathButInvalidHttpMethod(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/invalid/openapi_without_http_methods_in_path.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @return void
     */
    public function haveOpenApiV2FileWithHttpStatusCodeNotEnclosedInQuotationMarks(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/invalid/openapi_v2_with_http_status_code_not_enclosed_in_quotation_marks.yml')),
        ];

        $this->prepareOpenApiSchema($files);
    }

    /**
     * @return void
     */
    public function haveOpenApiV3FileWithHttpStatusCodeNotEnclosedInQuotationMarks(): void
    {
        $files = [
            'openapi.yml' => file_get_contents(codecept_data_dir('api/invalid/openapi_v3_with_http_status_code_not_enclosed_in_quotation_marks.yml')),
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
        $this->getSyncApiHelper()->mockDirectoryStructure(
            $this->buildStructureByPath($this->getOpenApiSchemaDirectory(), $files),
        );
    }

    /**
     * @return string
     */
    public function getOpenApiSchemaDirectory(): string
    {
        return 'resources/api';
    }

    /**
     * @return string
     */
    public function getOpenApiFilePath(): string
    {
        return sprintf('%s/%s/openapi.yml', $this->getSyncApiHelper()->getRootPath(), $this->getOpenApiSchemaDirectory());
    }

    /**
     * @param int $statusCode
     */
    public function assertErrorStatusCode(int $statusCode): void
    {
        $this->assertSame(AbstractConsole::CODE_ERROR, $statusCode, 'Expected to get an error code but got a success code.');
    }

    /**
     * @param int $statusCode
     */
    public function assertSuccessStatusCode(int $statusCode): void
    {
        $this->assertSame(AbstractConsole::CODE_SUCCESS, $statusCode, 'Expected to get a success code but got an error code.');
    }

    /**
     * @param string $path
     * @param array $files
     *
     * @return array
     */
    protected function buildStructureByPath(string $path, array $files): array
    {
        $pathFragments = explode('/', trim($path, '/'));

        $structure = [];
        $current = &$structure;
        foreach ($pathFragments as $fragment) {
            $current[$fragment] = [];
            $current = &$current[$fragment];
        }
        $current = $files;

        return $structure;
    }
}
