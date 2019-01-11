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
class Grand
{
    private const CODE = Cfg::CODE_TOTAL_PARTIAL . '_grand';
    private const CODE_GRAND = 'grand_total';
    private const CODE_GRAND_INCL = 'grand_total_incl';
    private const CODE_PARTIAL = \Praxigento\Wallet\Block\Adminhtml\Order\Z\Total\Partial::CODE;

    /**
     * @param \Magento\Sales\Block\Adminhtml\Totals $block
     * @param float $baseAmount
     * @param float $amount
     */
    private function addGrandWithTaxAndWallet(
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
                'label' => __('Grand Total (Incl. Tax, Incl. eWallet)'),
                'area' => 'footer',
                'is_formated' => false
            ]
        );
        $block->addTotal($total);
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Totals $block
     * @param float $baseAmount
     * @param float $amount
     */
    private function fixTotalExclTax(
        \Magento\Sales\Block\Adminhtml\Totals $block,
        $baseAmount,
        $amount
    ) {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $block->getOrder();
        $invoices = $order->getInvoiceCollection();
        $invoice = $invoices->getFirstItem();
        /* see \Magento\Sales\Model\Order\Invoice\Total\Tax::collect */
        $taxBase = $invoice->getBaseTaxAmount();
        $tax = $invoice->getTaxAmount();
        $taxCompensationBase = $invoice->getBaseDiscountTaxCompensationAmount();
        $taxCompensation = $invoice->getDiscountTaxCompensationAmount();
        $grandBase = $baseAmount - $taxBase - $taxCompensationBase;
        $grand = $amount - $tax - $taxCompensation;
        $totalGrandFixed = $block->getTotal(self::CODE_GRAND);
        $totalGrandFixed->setData('base_value', $grandBase);
        $totalGrandFixed->setData('value', $grand);
    }

    /**
     * Initialize partial payment related totals (if exist).
     *
     * @return $this
     */
    public function initTotals(
        \Magento\Sales\Block\Adminhtml\Totals $block
    ) {
        $totalPartial = $block->getTotal(self::CODE_PARTIAL);
        if ($totalPartial) {
            /* get eWallet parts */
            $partialBase = $totalPartial->getData('base_value');
            $partial = $totalPartial->getData('value');
            /* get previously reduced 'grand total including tax' and calculate 'grand total including tax & eWallet' */
            $totalGrandInc = $block->getTotal(self::CODE_GRAND_INCL);
            $grandIncBase = $totalGrandInc->getData('base_value');
            $grandInc = $totalGrandInc->getData('value');
            /* calculate grand totals with eWallet part */
            $baseAmount = $grandIncBase + $partialBase;
            $amount = $grandInc + $partial;
            /* populate and fix totals */
            $this->addGrandWithTaxAndWallet($block, $baseAmount, $amount);
            $this->fixTotalExclTax($block, $baseAmount, $amount);
            /* replace label for 'grand total including tax' */
            $label = new \Magento\Framework\Phrase ('Paid by Money');
            $totalGrandInc->setLabel($label);
        }
        return $this;
    }

}