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
    /** Name for configuration top level attribute to collect configuration data. */
    const CFG_NAME = 'praxigentoWallet';

    /** @var \Praxigento\Wallet\Helper\Config */
    protected $hlpCfg;

    public function __construct(
        \Praxigento\Wallet\Helper\Config $hlpCfg
    ) {
        $this->hlpCfg = $hlpCfg;
    }

    public function getConfig()
    {
        /* Get payment method configuration */
        $isEnabled = $this->hlpCfg->getWalletActive();
        $isPartialEnabled = $this->hlpCfg->getWalletPartialEnabled();
        $partialMaxPercent = $this->hlpCfg->getWalletPartialPercent();
        /* then compose data transfer object */
        $data = new \Praxigento\Wallet\Api\Data\Config\Payment\Method();
        $data->setIsEnabled($isEnabled);
        $data->setIsPartialEnabled($isPartialEnabled);
        $data->setPartialMaxPercent($partialMaxPercent);
        /* and add configuration data to checkout config */
        $result = [self::CFG_NAME => $data->getData()];
        return $result;
    }

}