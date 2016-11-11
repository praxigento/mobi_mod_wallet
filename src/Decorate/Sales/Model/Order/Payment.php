<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Decorate\Sales\Model\Order;


/**
 * Account partial payment amount in checking methods.
 */
class Payment
{
    public function aroundIsCaptureFinal(
        \Magento\Sales\Model\Order\Payment $subject,
        \Closure $proceed,
        $amountToCapture
    ) {
        $result = $proceed($amountToCapture);
        if (!$result) {
            $order = $subject->getOrder();
            $invoices = $order->getInvoiceCollection();
            $invoice = $invoices->getFirstItem();
            $grandTotalBase = $invoice->getBaseGrandTotal();
            /* validate with partial amount if exists */
            $result = ($grandTotalBase == $amountToCapture);
        }
        return $result;
    }
}