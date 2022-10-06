<?php

namespace SprykerSdk\SyncApi\OpenApi\Builder\Document;

use Generated\Shared\Transfer\OpenApiDocumentInfoTransfer;

class InfoBuilder implements InfoBuilderInterface
{
    /**
     * @param array $openApiYamlAsArray
     *
     * @return \Generated\Shared\Transfer\OpenApiDocumentInfoTransfer
     */
    public function build(array $openApiYamlAsArray): OpenApiDocumentInfoTransfer
    {
        $infoContents = $openApiYamlAsArray['info'] ?? [];

        return (new OpenApiDocumentInfoTransfer())->setContents($infoContents);
    }
}
