<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Api\Data\Config\Payment;

use Praxigento\Core\Data as DataObject;

/**
 * Configuration parameters for the payment method.
 *
 * See src/etc/extension_attributes.xml
 */
class Method
    extends DataObject
    implements \Magento\Framework\Api\ExtensionAttributesInterface
{
    const A_ENABLED = 'enabled';
    const A_NEGATIVE_BALANCE_ENABLED = 'negative_balance_enabled';
    const A_PARTIAL_ENABLED = 'partial_enabled';
    const A_PARTIAL_MAX_PERCENT = 'partial_max_percent';

    /**
     * @return float
     */
    public function getPartialMaxPercent()
    {
        $result = $this->get(self::A_PARTIAL_MAX_PERCENT);
        return $result;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $result = $this->get(self::A_ENABLED);
        return $result;
    }

    /**
     * @return bool
     */
    public function isNegativeBalanceEnabled()
    {
        $result = $this->get(self::A_NEGATIVE_BALANCE_ENABLED);
        return $result;
    }

    /**
     * @return bool
     */
    public function isPartialEnabled()
    {
        $result = $this->get(self::A_PARTIAL_ENABLED);
        return $result;
    }

    /**
     * @param bool $data
     */
    public function setIsEnabled($data)
    {
        $this->set(self::A_ENABLED, $data);
    }

    /**
     * @param bool $data
     */
    public function setIsNegativeBalanceEnabled($data)
    {
        $this->set(self::A_NEGATIVE_BALANCE_ENABLED, $data);
    }

    /**
     * @param bool $data
     */
    public function setIsPartialEnabled($data)
    {
        $this->set(self::A_PARTIAL_ENABLED, $data);
    }

    /**
     * @param float $data
     */
    public function setPartialMaxPercent($data)
    {
        $this->set(self::A_PARTIAL_MAX_PERCENT, $data);
    }
}