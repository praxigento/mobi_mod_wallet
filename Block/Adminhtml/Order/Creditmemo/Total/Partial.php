<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Block\Adminhtml\Order\Creditmemo\Total;

use Praxigento\Wallet\Config as Cfg;

class Partial
    extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
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
        /** @var \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals $parent */
        $parent = $this->getParentBlock();
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $this->getCreditmemo();
        $partialBase = $creditmemo->getData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT_BASE);
        if ($partialBase) {
            /* get partial payment data from creditmemo */
            $partial = $creditmemo->getData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT);
        } else {
            /* or check sales registry in repo */
            /** @var \Magento\Sales\Model\Order $order */
            $order = $parent->getOrder();
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