<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Block\Adminhtml\Order\View;


class Info
    extends \Magento\Backend\Block\Template
{
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpWalletCur;
    /** @var \Magento\Framework\Registry */
    private $registry;

    public $uiAmountStore;
    public $uiAmountWallet;
    public $uiCurrencyStore;
    public $uiCurrencyWallet;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->hlpWalletCur = $hlpWalletCur;
    }

    protected function _beforeToHtml()
    {
        $this->init();
        return $this;
    }

    private function init()
    {
        /** @var \Magento\Sales\Model\Order $sale */
        $sale = $this->registry->registry('current_order');
        $storeId = $sale->getStoreId();
        $payment = $sale->getPayment();

        $amntStore = $payment->getBaseAmountPaid();
        $amntWallet = $this->hlpWalletCur->storeToWallet($amntStore, $storeId);


        $this->uiAmountStore = number_format(round($amntStore, 2), 2, '.', '');
        $this->uiAmountWallet = number_format(round($amntWallet, 2), 2, '.', '');
        $this->uiCurrencyWallet = $this->hlpWalletCur->getWalletCurrency();
        $this->uiCurrencyStore = $this->hlpWalletCur->getStoreCurrency($storeId);
    }

}