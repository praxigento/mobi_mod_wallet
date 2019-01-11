<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Block\Adminhtml\Order\Z\Total;

use Praxigento\Wallet\Config as Cfg;

/**
 * Common code to composer partial totals for invoices, creditmemos, ... .
 */
class Partial
{
    /** @see
     *  - ./src/view/adminhtml/layout/sales_order_creditmemo_...xml
     *  - ./src/view/adminhtml/layout/sales_order_invoice_...xml
     */
    public const CODE = Cfg::CODE_TOTAL_PARTIAL;

    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    private $daoPartialSale;

    public function __construct(
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale
    ) {
        $this->daoPartialSale = $daoPartialSale;
    }

    /**
     * Add 'Paid by eWallet' section to the totals block.
     *
     * @param \Magento\Sales\Block\Adminhtml\Totals $block
     * @param float $baseAmount
     * @param float $amount
     */
    public function addTotalWallet(
        \Magento\Sales\Block\Adminhtml\Totals $block,
        $baseAmount,
        $amount
    ) {
        $total = new \Magento\Framework\DataObject(
            [
                'code' => self::CODE,
                'strong' => true,
                'base_value' => $baseAmount,
                'value' => $amount,
                'label' => __('Paid by eWallet'),
                'area' => 'footer',
                'is_formated' => false
            ]
        );
        $block->addTotal($total);
    }
}