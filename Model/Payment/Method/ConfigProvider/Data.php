<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Model\Payment\Method\ConfigProvider;


class Data
    extends \Praxigento\Core\Data
{
    const A_CUSTOMER_BALANCE = 'customer_balance';
    const A_ENABLED = 'enabled';
    const A_NEGATIVE_BALANCE_ENABLED = 'negative_balance_enabled';
    const A_PARTIAL_ENABLED = 'partial_enabled';
    const A_PARTIAL_MAX_PERCENT = 'partial_max_percent';

    /** @return float */
    public function getCustomerBalance()
    {
        $result = $this->get(self::A_CUSTOMER_BALANCE);
        return $result;
    }

    /** @return float */
    public function getPartialMaxPercent()
    {
        $result = $this->get(self::A_PARTIAL_MAX_PERCENT);
        return $result;
    }

    /** @return bool */
    public function isEnabled()
    {
        $result = $this->get(self::A_ENABLED);
        return $result;
    }

    /** @return bool */
    public function isNegativeBalanceEnabled()
    {
        $result = $this->get(self::A_NEGATIVE_BALANCE_ENABLED);
        return $result;
    }

    /** @return bool */
    public function isPartialEnabled()
    {
        $result = $this->get(self::A_PARTIAL_ENABLED);
        return $result;
    }

    /** @param float $data */
    public function setCustomerBalance($data)
    {
        $this->set(self::A_CUSTOMER_BALANCE, $data);
    }

    /** @param bool $data */
    public function setIsEnabled($data)
    {
        $this->set(self::A_ENABLED, $data);
    }

    /** @param bool $data */
    public function setIsNegativeBalanceEnabled($data)
    {
        $this->set(self::A_NEGATIVE_BALANCE_ENABLED, $data);
    }

    /** @param bool $data */
    public function setIsPartialEnabled($data)
    {
        $this->set(self::A_PARTIAL_ENABLED, $data);
    }

    /** @param float $data */
    public function setPartialMaxPercent($data)
    {
        $this->set(self::A_PARTIAL_MAX_PERCENT, $data);
    }
}