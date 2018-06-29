<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Model\Payment\Method;

use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Model\Payment\Method\ConfigProvider\Data as DConfg;

/**
 * Provide eWallet payment method configuration data for checkout process.
 */
class ConfigProvider
    implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /** Code for frontend related parts (layouts, uiComponents, ...) */
    const CODE_WALLET = 'prxgt_wallet_pay';
    /** Name for attribute of checkout configuration to collect method data (window.checkoutConfig.). */
    const UI_CHECKOUT_WALLET = 'prxgtWalletPaymentCfg';

    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAccount;
    /** @var \Praxigento\Wallet\Helper\Config */
    private $hlpCfg;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpWalletCur;
    /** @var \Magento\Checkout\Model\Session */
    private $sessCheckout;
    /** @var \Magento\Customer\Model\Session */
    private $sessCustomer;

    public function __construct(
        \Magento\Customer\Model\Session $sessCustomer,
        \Magento\Checkout\Model\Session $sessCheckout,
        \Praxigento\Accounting\Repo\Dao\Account $daoAccount,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        \Praxigento\Wallet\Helper\Config $hlpCfg,
        \Praxigento\Core\Api\Helper\Format $hlpFormat
    ) {
        $this->sessCustomer = $sessCustomer;
        $this->sessCheckout = $sessCheckout;
        $this->daoAccount = $daoAccount;
        $this->hlpWalletCur = $hlpWalletCur;
        $this->hlpCfg = $hlpCfg;
        $this->hlpFormat = $hlpFormat;
    }

    public function getConfig()
    {
        /* Get current configuration for the payment method */
        $isEnabled = $this->isEnabled();
        $isNegativeBalanceEnabled = $this->hlpCfg->getWalletNegativeBalanceEnabled();
        $isPartialEnabled = $this->hlpCfg->getWalletPartialEnabled();
        $partialMaxPercent = $this->hlpCfg->getWalletPartialPercent();

        /* then compose data transfer object (from PHP to JSON) */
        $data = new DConfg();
        $data->setIsEnabled($isEnabled);
        $data->setIsNegativeBalanceEnabled($isNegativeBalanceEnabled);
        $data->setIsPartialEnabled($isEnabled && $isPartialEnabled);
        $data->setPartialMaxPercent($partialMaxPercent);
        /* ... add other configuration data */
        $data = $this->populateCustomerData($data);
        /* and add configuration data to checkout config */
        $result = [
            self::UI_CHECKOUT_WALLET => $data->get()
        ];
        /* full structure see in \Magento\Checkout\Model\DefaultConfigProvider::getConfig */
        return $result;
    }

    private function isEnabled()
    {
        $result = $this->hlpCfg->getWalletActive();
        if ($result) {
            /* validate customer group */
            $isLoggedIn = $this->sessCustomer->isLoggedIn();
            $result = $result && $isLoggedIn;
        }
        return $result;
    }

    /**
     * @param DConfg $data
     * @return DConfg
     */
    private function populateCustomerData($data)
    {
        $data->setCustomerBalance(0);
        if ($this->sessCustomer && $this->sessCheckout) {
            $customerId = $this->sessCustomer->getCustomerId();
            $quote = $this->sessCheckout->getQuote();
            $storeId = $quote->getStoreId();
            if ($customerId) {
                $account = $this->daoAccount->getCustomerAccByAssetCode($customerId, Cfg::CODE_TYPE_ASSET_WALLET);
                if ($account) {
                    $balance = $account->getBalance();
                    /* convert balance from WALLET currency to STORE currency */
                    $balance = $this->hlpWalletCur->walletToStore($balance, $storeId);
                    $balance = $this->hlpFormat->toNumber($balance);
                    $data->setCustomerBalance($balance);
                }
            }
        }
        return $data;
    }

}