<?php

namespace SprykerSdk\SyncApi\OpenApi\Merger\Strategy;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

trait FieldAccessorTrait
{
    /**
     * @param \Transfer\TransferInterface $transfer
     * @param string $fieldName
     *
     * @return mixed
     */
    protected function getField(TransferInterface $transfer, string $fieldName)
    {
        $getterName = 'get' . ucfirst($fieldName);

        return $transfer->$getterName();
    }

    /**
     * @param \Transfer\TransferInterface $transfer
     * @param string $fieldName
     * @param mixed $value
     *
     * @return void
     */
    protected function setField(TransferInterface $transfer, string $fieldName, $value): void
    {
        $setterName = 'set' . ucfirst($fieldName);

        $transfer->$setterName($value);
    }
}
