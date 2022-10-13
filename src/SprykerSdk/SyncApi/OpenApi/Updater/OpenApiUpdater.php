<?php

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\Writer;
use Generated\Shared\Transfer\UpdateOpenApiRequestTransfer;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface;
use SprykerSdk\SyncApi\SyncApiConfig;
use Symfony\Component\Finder\Finder;
use Transfer\OpenApiResponseTransfer;

class OpenApiUpdater implements OpenApiUpdaterInterface
{
    /**
     * @var \SprykerSdk\SyncApi\Message\MessageBuilderInterface
     */
    protected $messageBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface
     */
    protected $filepathBuilder;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface[]
     */
    protected $mergerCollection;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected $syncApiConfig;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface $filepathBuilder
     * @param \SprykerSdk\SyncApi\SyncApiConfig $syncApiConfig
     * @param \SprykerSdk\SyncApi\OpenApi\Merger\MergerInterface[] $mergerCollection
     */
    public function __construct(
        MessageBuilderInterface $messageBuilder,
        FilepathBuilderInterface $filepathBuilder,
        SyncApiConfig $syncApiConfig,
        array $mergerCollection
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->filepathBuilder = $filepathBuilder;
        $this->mergerCollection = $mergerCollection;
        $this->syncApiConfig = $syncApiConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\UpdateOpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): OpenApiResponseTransfer
    {
        try {
            $sourceSpecObject = Reader::readFromJson($updateOpenApiRequestTransfer->getOpenApiDoc());
        } catch (\Throwable $throwable) {
            return (new OpenApiResponseTransfer())
                ->addError($this->messageBuilder->buildMessage($throwable->getMessage()));
        }

        if (!$sourceSpecObject->validate()) {
            return (new OpenApiResponseTransfer())->setErrors(
                new \ArrayObject($sourceSpecObject->getErrors())
            );
        }

        $syncApiTargetFilepath = $this->filepathBuilder->buildSyncApiFilepath(
            $updateOpenApiRequestTransfer->getOpenApiFile(),
            $updateOpenApiRequestTransfer->getProjectRoot(),
        );

        try {
            $targetSpecObject = Reader::readFromYamlFile($syncApiTargetFilepath, OpenApi::class, false);
        } catch (\Throwable $throwable) {
            $targetSpecObject = Reader::readFromYamlFile(
                $this->syncApiConfig->getPackageRootDirPath() . '/' .
                $this->syncApiConfig->getDefaultRelativePathToOpenApiFile(),
                OpenApi::class,
                false
            );
        }

        $targetSpecObject = $this->merge($targetSpecObject, $sourceSpecObject);

        $this->createFileIfNotExists($syncApiTargetFilepath);
        Writer::writeToYamlFile($targetSpecObject, $syncApiTargetFilepath);

        return (new OpenApiResponseTransfer())->addMessage($this->messageBuilder->buildMessage('Success!'));
    }

    /**
     * @param \cebe\openapi\spec\OpenApi $targetSpecObject
     * @param \cebe\openapi\spec\OpenApi $sourceSpecObject
     *
     * @return \cebe\openapi\spec\OpenApi
     */
    protected function merge(
        OpenApi $targetSpecObject,
        OpenApi $sourceSpecObject
    ): OpenApi {
        foreach ($this->mergerCollection as $merger) {
            $targetSpecObject = $merger->merge($targetSpecObject, $sourceSpecObject);
        }

        return $targetSpecObject;
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    protected function createFileIfNotExists(string $fileName): void
    {
        if (!is_file($fileName)) {
            if (!is_dir(dirname($fileName))) {
                mkdir(dirname($fileName), 0755, true);
            }

            file_put_contents($fileName, '');
        }
    }
}
