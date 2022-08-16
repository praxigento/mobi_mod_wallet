<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Block\Adminhtml\Order\View;

/**
 * Display wallet payment relation information.
 */
class Info
    extends \Magento\Backend\Block\Template {
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    private $daoPartialSale;
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpWalletCur;
    /** @var \Magento\Framework\Registry */
    private $registry;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;
    /**#@+
     * Block's properties are used by template.
     */
    public $uiAmountStore;
    public $uiAmountWallet;
    public $uiCurrencyStore;
    public $uiCurrencyWallet;
    public $uiIsWalletUsed = false;

    /**#@-
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale,
        \Praxigento\Core\Api\Helper\Format $hlpFormat,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->daoPartialSale = $daoPartialSale;
        $this->hlpFormat = $hlpFormat;
        $this->hlpWalletCur = $hlpWalletCur;
    }

    protected function _beforeToHtml() {
        $this->init();
        return $this;
    }

    private function init() {
        /** @var \Magento\Sales\Model\Order $sale */
        $sale = $this->registry->registry('current_order');
        $payment = $sale->getPayment();
        $code = $payment->getMethod();
        $saleId = $sale->getId();
        $storeId = $sale->getStoreId();
        $date = $sale->getCreatedAt();
        $found = $this->daoPartialSale->getById($saleId);
        if ($code == \Praxigento\Wallet\Model\Payment\Method\ConfigProvider::CODE_WALLET) {
            $this->uiIsWalletUsed = true;
            /* whole sale order was paid by wallet */
            $amntStore = $payment->getBaseAmountPaid();
            $this->setupProps($amntStore, $storeId, $date);
        } elseif ($found) {
            /* partial wallet payment */
            $amntStore = $found->getBasePartialAmount();
            $this->setupProps($amntStore, $storeId, $date);
        }
    }

    /**
     * Setup block's UI properties if wallet payment is used.
     *
     * @param $amntStore
     * @param $storeId
     */
    private function setupProps($amntStore, $storeId, $date = null) {
        $this->uiIsWalletUsed = true;
        $amntWallet = $this->hlpWalletCur->storeToWallet($amntStore, $storeId, $date);
        $this->uiAmountStore = $this->hlpFormat->toNumber(round($amntStore, 2));
        $this->uiAmountWallet = $this->hlpFormat->toNumber(round($amntWallet, 2));
        $this->uiCurrencyWallet = $this->hlpWalletCur->getWalletCurrency();
        $this->uiCurrencyStore = $this->hlpWalletCur->getStoreCurrency($storeId);
    }
}
