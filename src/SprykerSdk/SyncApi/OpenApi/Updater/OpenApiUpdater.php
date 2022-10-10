<?php

namespace SprykerSdk\SyncApi\OpenApi\Updater;

use Generated\Shared\Transfer\OpenApiDocumentTransfer;
use Generated\Shared\Transfer\UpdateOpenApiRequestTransfer;
use SprykerSdk\SyncApi\Message\MessageBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\Document\DocumentBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface;
use SprykerSdk\SyncApi\OpenApi\Converter\OpenApiDocumentToArrayConverterInterface;
use SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface;
use SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface;
use SprykerSdk\SyncApi\OpenApi\Merge\Strategy\MergeStrategyInterface;
use SprykerSdk\SyncApi\SyncApiConfig;
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
     * @var string
     */
    protected $openApiTemplateFilepath;

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Builder\Document\DocumentBuilderInterface
     */
    protected $documentBuilder;

    /**
     * @var \SprykerSdk\SyncApi\SyncApiConfig
     */
    protected $syncApiConfig;

    /**
     * @var array<\SprykerSdk\SyncApi\OpenApi\Merge\Strategy\MergeStrategyInterface>
     */
    protected $mergeStrategies = [];

    /**
     * @var \SprykerSdk\SyncApi\OpenApi\Converter\OpenApiDocumentToArrayConverterInterface
     */
    protected $openApiDocumentToArrayConverter;

    /**
     * @param \SprykerSdk\SyncApi\Message\MessageBuilderInterface $messageBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\FilepathBuilderInterface $filepathBuilder
     * @param \SprykerSdk\SyncApi\OpenApi\Decoder\OpenApiDocDecoderInterface $openApiDocDecoder
     * @param \SprykerSdk\SyncApi\Validator\ValidatorInterface $validator
     * @param \SprykerSdk\SyncApi\OpenApi\FileManager\OpenApiFileManagerInterface $openApiFileManager
     * @param \SprykerSdk\SyncApi\SyncApiConfig $syncApiConfig
     * @param \SprykerSdk\SyncApi\OpenApi\Builder\Document\DocumentBuilderInterface $documentBuilder
     * @param array<string, \SprykerSdk\SyncApi\OpenApi\Merge\Strategy\MergeStrategyInterface> $mergeStrategies
     * @param \SprykerSdk\SyncApi\OpenApi\Converter\OpenApiDocumentToArrayConverterInterface $openApiDocumentToArrayConverter
     */
    public function __construct(
        MessageBuilderInterface $messageBuilder,
        FilepathBuilderInterface $filepathBuilder,
        OpenApiDocDecoderInterface $openApiDocDecoder,
        ValidatorInterface $validator,
        OpenApiFileManagerInterface $openApiFileManager,
        SyncApiConfig $syncApiConfig,
        DocumentBuilderInterface $documentBuilder,
        array $mergeStrategies,
        OpenApiDocumentToArrayConverterInterface $openApiDocumentToArrayConverter
    ) {
        $this->messageBuilder = $messageBuilder;
        $this->filepathBuilder = $filepathBuilder;
        $this->openApiDocDecoder = $openApiDocDecoder;
        $this->validator = $validator;
        $this->openApiFileManager = $openApiFileManager;
        $this->syncApiConfig = $syncApiConfig;
        $this->mergeStrategies = $mergeStrategies;
        $this->openApiDocumentToArrayConverter = $openApiDocumentToArrayConverter;
        $this->documentBuilder = $documentBuilder;
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

//        file_put_contents('OPENAPI.json', json_encode($targetOpenApiFileContents));
//        exit;

        $targetOpenApiDocument = $this->documentBuilder->buildOpenApiDocumentFromArray($targetOpenApiFileContents);
        $sourceOpenApiDocument = $this->documentBuilder->buildOpenApiDocumentFromArray($sourceOpenApiContents);

        $targetOpenApiDocument = $this->merge(
            $targetOpenApiDocument,
            $sourceOpenApiDocument
        );

        $targetOpenApiFileContents = $this->openApiDocumentToArrayConverter->convert($targetOpenApiDocument);

        $this->openApiFileManager->saveOpenApiFileFromArray($syncApiTargetFilepath, $targetOpenApiFileContents);

        return (new OpenApiResponseTransfer())->addMessage($this->messageBuilder->buildMessage('Success!'));
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
     * @return \Generated\Shared\Transfer\OpenApiDocumentTransfer
     */
    protected function merge(
        OpenApiDocumentTransfer $targetOpenApiDocument,
        OpenApiDocumentTransfer $sourceOpenApiDocument
    ): OpenApiDocumentTransfer {
        foreach ($this->syncApiConfig->getFieldsMergeStrategyMap() as $fieldName => $strategyName) {
            $strategy = $this->mergeStrategies[$strategyName];

            $strategy->merge($targetOpenApiDocument, $sourceOpenApiDocument, $fieldName);
        }

        return $targetOpenApiDocument;
    }
}
