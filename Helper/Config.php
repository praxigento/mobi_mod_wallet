<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Helper;

/**
 * Helper to get configuration parameters related to the module.
 */
class Config
{

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getWalletActive()
    {
        $result = $this->scopeConfig->getValue('payment/prxgt_wallet_pay/active');
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        return $result;
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getWalletNegativeBalanceEnabled()
    {
        $result = $this->scopeConfig->getValue('payment/prxgt_wallet_pay/negative_balance_enabled');
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        /* disable negative balance if eWalllet payment is not active */
        $result = $result && $this->getWalletActive();
        return $result;
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getWalletPartialEnabled()
    {
        $result = $this->scopeConfig->getValue('payment/prxgt_wallet_pay/partial_enabled');
        $result = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        /* disable partial payment if eWalllet payment is not active */
        $result = $result && $this->getWalletActive();
        return $result;
    }

    /**
     * Get partial payment maximal percent (0.00-1.00)
     *
     * @return float
     */
    public function getWalletPartialPercent()
    {
        $result = $this->scopeConfig->getValue('payment/prxgt_wallet_pay/partial_percent');
        $result *= 1;
        $result = ($result < 0) || ($result > 1) ? 0 : $result;
        return $result;
    }
}