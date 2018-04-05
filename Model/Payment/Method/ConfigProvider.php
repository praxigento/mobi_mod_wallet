<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Model\Payment\Method;

use Praxigento\Wallet\Model\Payment\Method\ConfigProvider\Data as DConfg;

/**
 * Provide eWallet payment method configuration data for checkout process.
 */
class ConfigProvider
    implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /** Code for frontend related parts (layouts, uiComponents, ...) */
    const CODE_WALLET = 'praxigento_wallet_method';
    /** Name for attribute of checkout configuration to collect method data (window.checkoutConfig.). */
    const UI_CHECKOUT_WALLET = 'prxgtWalletPaymentCfg';

    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAccount;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoAssetType;
    /** @var \Praxigento\Wallet\Helper\Config */
    private $hlpCfg;
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
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoAssetType,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        \Praxigento\Wallet\Helper\Config $hlpCfg
    )
    {
        $this->sessCustomer = $sessCustomer;
        $this->sessCheckout = $sessCheckout;
        $this->daoAccount = $daoAccount;
        $this->daoAssetType = $daoAssetType;
        $this->hlpWalletCur = $hlpWalletCur;
        $this->hlpCfg = $hlpCfg;
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
                $assetTypeId = $this->daoAssetType->getIdByCode(\Praxigento\Wallet\Config::CODE_TYPE_ASSET_WALLET);
                $account = $this->daoAccount->getByCustomerId($customerId, $assetTypeId);
                if ($account) {
                    $balance = $account->getBalance();
                    /* convert balance from WALLET currency to STORE currency */
                    $balance = $this->hlpWalletCur->walletToStore($balance, $storeId);
                    $balance = number_format($balance, 2, '.', '');
                    $data->setCustomerBalance($balance);
                }
            }
        }
        return $data;
    }

}