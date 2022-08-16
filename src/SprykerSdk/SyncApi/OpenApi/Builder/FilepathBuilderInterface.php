<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder;

interface FilepathBuilderInterface
{
    /**
     * @param string $filename
     * @param string|null $rootDirectoryPath
     *
     * @return string
     */
    public function buildSyncApiFilepath(string $filename, ?string $rootDirectoryPath): string;
}
