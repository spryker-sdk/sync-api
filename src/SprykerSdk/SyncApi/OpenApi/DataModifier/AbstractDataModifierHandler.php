<?php

namespace SprykerSdk\SyncApi\OpenApi\DataModifier;

use Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer;

abstract class AbstractDataModifierHandler implements DataModifierHandlerInterface
{
    /**
     * @var \SprykerSdk\SyncApi\OpenApi\DataModifier\DataModifierHandlerInterface|null
     */
    protected $nextDataModifierHandler;

    /**
     * @param \SprykerSdk\SyncApi\OpenApi\DataModifier\DataModifierHandlerInterface|null $dataModifierHandler
     */
    public function __construct(?DataModifierHandlerInterface $dataModifierHandler)
    {
        $this->nextDataModifierHandler = $dataModifierHandler;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
     *
     * @return \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer
     */
    public function handle(
        OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
    ): OpenApiDataModifierContainerTransfer {
        $openApiDataModifierContainer = $this->modify($openApiDataModifierContainer);

        if (isset($this->nextDataModifierHandler)) {
            return $this->nextDataModifierHandler->handle($openApiDataModifierContainer);
        }

        return $openApiDataModifierContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
     *
     * @return \Generated\Shared\Transfer\OpenApiDataModifierContainerTransfer
     */
    abstract protected function modify(
        OpenApiDataModifierContainerTransfer $openApiDataModifierContainer
    ): OpenApiDataModifierContainerTransfer;
}
