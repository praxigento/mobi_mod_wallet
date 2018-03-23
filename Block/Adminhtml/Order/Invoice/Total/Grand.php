<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total;

use Praxigento\Wallet\Config as Cfg;

/**
 * This block is based on 'partial' block and should be placed
 * after '\Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total\Partial'
 * in 'src/view/adminhtml/layout/sales_order_invoice_new.xml'
 */
class Grand
    extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    const CODE = Cfg::CODE_TOTAL_PARTIAL . '_grand';
    const CODE_GRAND = 'grand_total';
    const CODE_GRAND_INCL = 'grand_total_incl';
    const CODE_PARTIAL = \Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total\Partial::CODE;
    const CODE_TAX = 'tax';
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    protected $daoPartialSale;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->daoPartialSale = $daoPartialSale;
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
        $totalPartial = $parent->getTotal(self::CODE_PARTIAL);
        if ($totalPartial) {
            $totalGrandIncFixed = $parent->getTotal(self::CODE_GRAND_INCL);
            $partialBase = $totalPartial->getData('base_value');
            $partial = $totalPartial->getData('value');
            $grandIncFixedBase = $totalGrandIncFixed->getData('base_value');
            $grandIncFixed = $totalGrandIncFixed->getData('value');
            /* add grand total with eWallet part */
            $baseAmount = $grandIncFixedBase + $partialBase;
            $amount = $grandIncFixed + $partial;
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => self::CODE,
                    'strong' => true,
                    'base_value' => $baseAmount,
                    'value' => $amount,
                    'label' => __('Grand Total (Incl. Tax, Incl. eWallet)'),
                    'area' => 'footer',
                    'is_formated' => false
                ]
            );
            $parent->addTotal($total);
            /* fix grand total excl. tax */
            /** @var \Magento\Sales\Model\Order $order */
            $order = $parent->getOrder();
            $invoices = $order->getInvoiceCollection();
            $invoice = $invoices->getFirstItem();
            /* see \Magento\Sales\Model\Order\Invoice\Total\Tax::collect */
            $taxBase = $invoice->getBaseTaxAmount();
            $tax = $invoice->getTaxAmount();
            $taxCompensationBase = $invoice->getBaseDiscountTaxCompensationAmount();
            $taxCompensation = $invoice->getDiscountTaxCompensationAmount();
            $grandBase = $baseAmount - $taxBase - $taxCompensationBase;
            $grand = $amount - $tax - $taxCompensation;
            $totalGrandFixed = $parent->getTotal(self::CODE_GRAND);
            $totalGrandFixed->setData('base_value', $grandBase);
            $totalGrandFixed->setData('value', $grand);
        }
        return $this;
    }

}