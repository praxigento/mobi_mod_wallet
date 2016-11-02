<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Api\Data\Config\Payment;

/**
 * Configuration parameters for the payment method.
 *
 * See src/etc/extension_attributes.xml
 */
interface Method
    extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return float
     */
    public function getPartialMaxPercent();

    /**
     * @return bool
     */
    public function isPartialEnabled();

    /**
     * @param bool $data
     */
    public function setIsPartialEnabled($data);

    /**
     * @param float $data
     */
    public function setPartialMaxPercent($data);
}