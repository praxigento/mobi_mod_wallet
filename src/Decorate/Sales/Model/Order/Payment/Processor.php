<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Decorate\Sales\Model\Order\Payment;


class Processor
{
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\IQuote */
    protected $_repoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Entity\Partial\IQuote $repoPartialQuote
    ) {
        $this->_repoPartialQuote = $repoPartialQuote;
    }

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
        $found = $this->_repoPartialQuote->getById($quoteId);
        if ($found) {
            $basePartial = $found->getBasePartialAmount();
            $amountToPay -= $basePartial;
        }
        $result = $proceed($payment, $isOnline, $amountToPay);
        return $result;
    }

    public function aroundCapture(
        \Magento\Sales\Model\Order\Payment\Processor $subject,
        \Closure $proceed,
        \Magento\Sales\Api\Data\OrderPaymentInterface $payment,
        \Magento\Sales\Api\Data\InvoiceInterface $invoice = null
    ) {
        $order = $payment->getOrder();
        $quoteId = $order->getQuoteId();
        $found = $this->_repoPartialQuote->getById($quoteId);
        if ($found) {
            $basePartial = $found->getBasePartialAmount();
            if ($invoice) {
                $amountToPay = $invoice->getBaseGrandTotal();
                $amountToPay -= $basePartial;
                $invoice->setBaseGrandTotal($amountToPay);
            } else {
                // do nothing yet
            }

        }
        $result = $proceed($payment, $invoice);
        return $result;
    }
}