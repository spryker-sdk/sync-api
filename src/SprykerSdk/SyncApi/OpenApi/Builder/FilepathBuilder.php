<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder;

use Codeception\Lib\Di;

class FilepathBuilder implements FilepathBuilderInterface
{
    /**
     * @var string
     */
    protected const YAML_EXTENSION_PATTERN = '/(\.yaml|\.yml)/';

    /**
     * @var string
     */
    protected string $projectRootDirPath;

    /**
     * @var string
     */
    protected string $syncApiDirPath;

    /**
     * @param string $projectRootDirPath
     * @param string $syncApiDirPath
     */
    public function __construct(
        string $projectRootDirPath,
        string $syncApiDirPath
    ) {
        $this->projectRootDirPath = $projectRootDirPath;
        $this->syncApiDirPath = $syncApiDirPath;
    }

    /**
     * @param string $filename
     * @param string|null $rootDirectoryPath
     *
     * @return string
     */
    public function buildSyncApiFilepath(string $filename, ?string $rootDirectoryPath): string
    {
        if ($rootDirectoryPath === null) {
            $rootDirectoryPath = $this->projectRootDirPath;
        }

        return rtrim($rootDirectoryPath, DIRECTORY_SEPARATOR)
            . '/' . $this->syncApiDirPath
            . '/' . $this->prepareFilename($filename);
    }



    /**
     * @param string $filename
     *
     * @return string
     */
    protected function prepareFilename(string $filename): string
    {
        $filename = trim($filename, DIRECTORY_SEPARATOR);
        $filename = preg_replace(static::YAML_EXTENSION_PATTERN, '', $filename);
        return $filename . '.yml';
    }
}
