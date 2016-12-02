<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Model\Checkout;

/**
 * Provde payment method configuration data for checkout process.
 */
class ConfigProvider
    implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /** Configuration items names for 'window.customerData' (JS object) */
    const CFG_CUST_BALANCE = 'prxgtWalletBalance';
    /** Name for configuration top level attribute to collect configuration data. */
    const CFG_NAME = 'praxigentoWallet';
    /** @var \Praxigento\Wallet\Helper\Config */
    protected $hlpCfg;
    /** @var \Praxigento\Accounting\Repo\Entity\IAccount */
    protected $repoAccount;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\IAsset */
    protected $repoAssetType;
    /** @var \Magento\Customer\Model\Session */
    protected $sessionCustomer;

    public function __construct(
        \Magento\Customer\Model\Session $sessionCustomer,
        \Praxigento\Accounting\Repo\Entity\IAccount $repoAccount,
        \Praxigento\Accounting\Repo\Entity\Type\IAsset $repoAssetType,
        \Praxigento\Wallet\Helper\Config $hlpCfg
    ) {
        $this->sessionCustomer = $sessionCustomer;
        $this->repoAccount = $repoAccount;
        $this->repoAssetType = $repoAssetType;
        $this->hlpCfg = $hlpCfg;
    }

    protected function _populateCustomerData()
    {
        $result[self::CFG_CUST_BALANCE] = 0;
        if ($this->sessionCustomer) {
            $customerId = $this->sessionCustomer->getCustomerId();
            $assetTypeId = $this->repoAssetType->getIdByCode(\Praxigento\Wallet\Config::CODE_TYPE_ASSET_WALLET_ACTIVE);
            $account = $this->repoAccount->getByCustomerId($customerId, $assetTypeId);
            if ($account) {
                $balance = $account->getBalance();
                $result[self::CFG_CUST_BALANCE] = $balance;
            }
        }
        return $result;
    }

    public function getConfig()
    {
        /* Get payment method configuration */
        $isEnabled = $this->hlpCfg->getWalletActive();
        $isNegativeBalanceEnabled = $this->hlpCfg->getWalletNegativeBalanceEnabled();
        $isPartialEnabled = $this->hlpCfg->getWalletPartialEnabled();
        $partialMaxPercent = $this->hlpCfg->getWalletPartialPercent();
        /* ... and additional configuration for other objects */
        $customerData = $this->_populateCustomerData();
        /* then compose data transfer object */
        $data = new \Praxigento\Wallet\Api\Data\Config\Payment\Method();
        $data->setIsEnabled($isEnabled);
        $data->setIsNegativeBalanceEnabled($isNegativeBalanceEnabled);
        $data->setIsPartialEnabled($isPartialEnabled);
        $data->setPartialMaxPercent($partialMaxPercent);
        /* and add configuration data to checkout config */
        $result = [
            'customerData' => $customerData, // see \Magento\Checkout\Model\DefaultConfigProvider::getConfig
            self::CFG_NAME => $data->getData()
        ];
        return $result;
    }

}