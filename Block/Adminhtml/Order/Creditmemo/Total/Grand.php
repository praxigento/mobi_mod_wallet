<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Block\Adminhtml\Order\Creditmemo\Total;

/**
 * This block is based on 'partial' block and should be placed
 * after '\Praxigento\Wallet\Block\Adminhtml\Order\Creditmemo\Total\Partial'
 * in 'src/view/adminhtml/layout/sales_order_creditmemo_new.xml'
 */
class Grand
    extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
{
    /** @var \Praxigento\Wallet\Block\Adminhtml\Order\Z\Total\Grand */
    private $zTotalGrand;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Praxigento\Wallet\Block\Adminhtml\Order\Z\Total\Grand $zTotalGrand,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->zTotalGrand = $zTotalGrand;
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
        $this->zTotalGrand->initTotals($parent);
        return $this;
    }
}