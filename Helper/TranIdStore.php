<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Helper;


/**
 * Save and restore transaction ID for partial payment into/from Mage Registry between processing steps.
 */
class TranIdStore
{
    const TRAN_ID = 'prxgtWalletPartialTranId';

    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function cleanTranId()
    {
        if ($this->registry->registry(self::TRAN_ID)) {
            $this->registry->unregister(self::TRAN_ID);
        }
    }

    public function restoreTranId()
    {
        $result = $this->registry->registry(self::TRAN_ID);
        return $result;
    }

    public function saveTranId($id)
    {
        $this->cleanTranId();
        $this->registry->register(self::TRAN_ID, $id);
    }
}