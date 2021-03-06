<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total;

use Praxigento\Wallet\Config as Cfg;

class Partial
    extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    private $daoPartialSale;
    /** @var \Praxigento\Wallet\Block\Adminhtml\Order\Z\Total\Partial */
    private $zTotalPartial;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale,
        \Praxigento\Wallet\Block\Adminhtml\Order\Z\Total\Partial $zTotalPartial,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->daoPartialSale = $daoPartialSale;
        $this->zTotalPartial = $zTotalPartial;
    }

    /**
     * Initialize partial payment related totals (if exist).
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Totals $parent */
        $parent = $this->getParentBlock();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $parent->getOrder();
        /* check collected partial totals (for currently processed invoices only) */
        $invoices = $order->getInvoiceCollection();
        $invoice = $invoices->getFirstItem();
        $partialBase = $invoice->getData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT_BASE);
        $partial = $invoice->getData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT);
        if (!$partialBase) {
            /* check sales registry in repo */
            $orderId = $order->getId();
            $found = $this->daoPartialSale->getById($orderId);
            if ($found) {
                $partialBase = $found->getBasePartialAmount();
                $partial = $found->getBasePartialAmount();
            }
        }
        if ($partialBase) {
            /* add partial to parent totals */
            $this->zTotalPartial->addTotalWallet($parent, $partialBase, $partial);
        }
        return $this;
    }

}