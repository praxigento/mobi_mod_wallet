<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Plugin\Magento\Sales\Model\Order\Payment;


class Processor
{
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $daoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $daoPartialQuote
    ) {
        $this->daoPartialQuote = $daoPartialQuote;
    }

    /**
     * Decrease authorization amount for partial payments.
     *
     * @param \Magento\Sales\Model\Order\Payment\Processor $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @param $isOnline
     * @param $amount
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    public function aroundAuthorize(
        \Magento\Sales\Model\Order\Payment\Processor $subject,
        \Closure $proceed,
        \Magento\Sales\Api\Data\OrderPaymentInterface $payment,
        $isOnline,
        $amount
    ) {
        $amountToPay = $amount;
        $order = $payment->getOrder();
        $quoteId = $order->getQuoteId();
        $found = $this->daoPartialQuote->getById($quoteId);
        if ($found) {
            $basePartial = $found->getBasePartialAmount();
            $amountToPay -= $basePartial;
        }
        $result = $proceed($payment, $isOnline, $amountToPay);
        return $result;
    }

    /**
     * Decrease captured amount for partial payments.
     *
     * @param \Magento\Sales\Model\Order\Payment\Processor $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @param \Magento\Sales\Api\Data\InvoiceInterface|null $invoice
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    public function aroundCapture(
        \Magento\Sales\Model\Order\Payment\Processor $subject,
        \Closure $proceed,
        \Magento\Sales\Api\Data\OrderPaymentInterface $payment,
        \Magento\Sales\Api\Data\InvoiceInterface $invoice = null
    ) {
        if ($invoice) {
            /* try to find related record in quote registry */
            $order = $payment->getOrder();
            $quoteId = $order->getQuoteId();
            $found = $this->daoPartialQuote->getById($quoteId);
            if ($found) {
                /* decrease amount in invoice */
                $partialBase = $found->getBasePartialAmount();
                $partial = $found->getPartialAmount();
                $grandTotalBase = $invoice->getBaseGrandTotal();
                $grandTotal = $invoice->getGrandTotal();
                $grandTotalBase -= $partialBase;
                $grandTotal -= $partial;
                $invoice->setBaseGrandTotal($grandTotalBase);
                $invoice->setGrandTotal($grandTotal);
            }
        }
        $result = $proceed($payment, $invoice);
        return $result;
    }
}