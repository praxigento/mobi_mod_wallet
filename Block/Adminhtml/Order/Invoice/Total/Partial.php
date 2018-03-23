<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Block\Adminhtml\Order\Invoice\Total;

use Praxigento\Wallet\Config as Cfg;

class Partial
    extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    /** see src/view/adminhtml/layout/sales_order_invoice_new.xml */
    const CODE = Cfg::CODE_TOTAL_PARTIAL;
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    protected $daoPartialSale;

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
        /** @var \Magento\Sales\Model\Order $order */
        $order = $parent->getOrder();
        /* check collected partial totals (for currently processed invoices only) */
        $invoices = $order->getInvoiceCollection();
        $invoice = $invoices->getFirstItem();
        $partialBase = $invoice->getData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT_BASE);
        if ($partialBase) {
            $partial = $invoice->getData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT);
        } else {
            /* check sales registry in repo */
            $orderId = $order->getId();
            $found = $this->daoPartialSale->getById($orderId);
            if ($found) {
                $partialBase = $found->getBasePartialAmount();
                $partial = $found->getBasePartialAmount();
            }
        }
        if ($partialBase) {
            /* add partial total */
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => self::CODE,
                    'strong' => true,
                    'base_value' => $partialBase,
                    'value' => $partial,
                    'label' => __('Paid by eWallet'),
                    'area' => 'footer',
                    'is_formated' => false
                ]
            );
            $parent->addTotal($total);
        }
        return $this;
    }

}