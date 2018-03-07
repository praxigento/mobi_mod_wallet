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
    const ATTR_ENABLED = 'enabled';
    const ATTR_NEGATIVE_BALANCE_ENABLED = 'negative_balance_enabled';
    const ATTR_PARTIAL_ENABLED = 'partial_enabled';
    const ATTR_PARTIAL_MAX_PERCENT = 'partial_max_percent';

    /**
     * @return float
     */
    public function getPartialMaxPercent()
    {
        $result = $this->get(self::ATTR_PARTIAL_MAX_PERCENT);
        return $result;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $result = $this->get(self::ATTR_ENABLED);
        return $result;
    }

    /**
     * @return bool
     */
    public function isNegativeBalanceEnabled()
    {
        $result = $this->get(self::ATTR_NEGATIVE_BALANCE_ENABLED);
        return $result;
    }

    /**
     * @return bool
     */
    public function isPartialEnabled()
    {
        $result = $this->get(self::ATTR_PARTIAL_ENABLED);
        return $result;
    }

    /**
     * @param bool $data
     */
    public function setIsEnabled($data)
    {
        $this->set(self::ATTR_ENABLED, $data);
    }

    /**
     * @param bool $data
     */
    public function setIsNegativeBalanceEnabled($data)
    {
        $this->set(self::ATTR_NEGATIVE_BALANCE_ENABLED, $data);
    }

    /**
     * @param bool $data
     */
    public function setIsPartialEnabled($data)
    {
        $this->set(self::ATTR_PARTIAL_ENABLED, $data);
    }

    /**
     * @param float $data
     */
    public function setPartialMaxPercent($data)
    {
        $this->set(self::ATTR_PARTIAL_MAX_PERCENT, $data);
    }
}