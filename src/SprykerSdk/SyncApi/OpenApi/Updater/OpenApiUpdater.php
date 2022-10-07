<?php

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;
use Generated\Shared\Transfer\UpdateOpenApiRequestTransfer;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\DataModifier\DataModifierHandlerInterface;
use SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface;
use SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface;
use SprykerSdk\SyncApi\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;
use Transfer\OpenApiResponseTransfer;
use Transfer\ValidateRequestTransfer;
use Transfer\ValidateResponseTransfer;

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
     * @var \SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface
     */
    protected $openApiDocDecoder;

    /**
     * @var \SprykerSdk\SyncApi\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface
     */
    protected $openApiFileManager;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\DataModifier\DataModifierHandlerInterface
     */
    protected $dataModifierHandler;

    /**
     * @var string
     */
    protected $openApiTemplateFilepath;

    /**
     * @var string
     */
    protected $documentBuilder;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface $filepathBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface $openApiDocDecoder
     * @param \SprykerSdk\SyncApi\Validator\ValidatorInterface $validator
     * @param \SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface $openApiFileManager
     * @param \SprykerSdk\SyncApi\OpenApi\DataModifier\DataModifierHandlerInterface $dataModifierHandler
     * @param string $openApiTemplateFilepath
     */
    public function __construct(
        MessageBuilderInterface $messageBuilder,
        FilepathBuilderInterface $filepathBuilder,
        OpenApiDocDecoderInterface $openApiDocDecoder,
        ValidatorInterface $validator,
        OpenApiFileManagerInterface $openApiFileManager,
        DataModifierHandlerInterface $dataModifierHandler,
        string $openApiTemplateFilepath
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->filepathBuilder = $filepathBuilder;
        $this->openApiDocDecoder = $openApiDocDecoder;
        $this->validator = $validator;
        $this->openApiFileManager = $openApiFileManager;
        $this->dataModifierHandler = $dataModifierHandler;
        $this->openApiTemplateFilepath = $openApiTemplateFilepath;
    }

    /**
     * @param \Generated\Shared\Transfer\UpdateOpenApiRequestTransfer $openApiRequestTransfer
     *
     * @return \Transfer\OpenApiResponseTransfer
     */
    public function updateOpenApi(UpdateOpenApiRequestTransfer $updateOpenApiRequestTransfer): OpenApiResponseTransfer
    {
        try {
            $sourceOpenApiContents = $this->openApiDocDecoder->decode($updateOpenApiRequestTransfer->getOpenApiDoc());
        } catch (\Throwable $throwable) {
            return (new OpenApiResponseTransfer())
                ->addError($this->messageBuilder->buildMessage($throwable->getMessage()));
        }

        if ($updateOpenApiRequestTransfer->getIsValidate()) {
            $validatorResponse = $this->validateOpenApiDoc(Yaml::dump($sourceOpenApiContents));
            if ($validatorResponse->getErrors()->count() > 0) {
                return (new OpenApiResponseTransfer())->setErrors($validatorResponse->getErrors());
            }
        }

        $syncApiTargetFilepath = $this->filepathBuilder->buildSyncApiFilepath(
            $updateOpenApiRequestTransfer->getOpenApiFile(),
            $updateOpenApiRequestTransfer->getProjectRoot(),
        );

        try {
            $targetOpenApiFileContents = $this->openApiFileManager->readOpenApiFileAsArray($syncApiTargetFilepath);
        } catch (\Throwable $throwable) {
            $targetOpenApiFileContents = $this->openApiFileManager->readOpenApiFileAsArray(
                $this->openApiTemplateFilepath
            );
        }

        $targetOpenApiDocument = $this->documentBuilder->build($targetOpenApiFileContents);
        $sourceOpenApiDocument = $this->documentBuilder->build($sourceOpenApiContents);


        $targetOpenApiFileContents = $this->merge(
            $targetOpenApiDocument,
            $sourceOpenApiDocument
        );

        $this->openApiFileManager->saveOpenApiFileFromArray($syncApiTargetFilepath, $targetOpenApiFileContents);

        return (new OpenApiResponseTransfer())
            ->addMessage($this->messageBuilder->buildMessage('Success!'));
    }

    /**
     * @param string $openApiDoc
     *
     * @return \Transfer\ValidateResponseTransfer
     */
    protected function validateOpenApiDoc(string $openApiDoc): ValidateResponseTransfer
    {
        $openApiTmpFile = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($openApiTmpFile, $openApiDoc);

        $validateResponseTransfer = $this->validator->validate(
            (new ValidateRequestTransfer())->setOpenApiFile($openApiTmpFile)
        );

        unlink($openApiTmpFile);

        return $validateResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $targetOpenApiDocument
     * @param \Generated\Shared\Transfer\OpenApiDocumentTransfer $sourceOpenApiDocument
     *
     * @return array
     */
    protected function merge(
        OpenApiDocumentTransfer $targetOpenApiDocument,
        OpenApiDocumentTransfer $sourceOpenApiDocument
    ): array {
        foreach ($sourceOpenApiDocument as $field)
    }
}
