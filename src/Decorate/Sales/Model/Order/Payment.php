<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Decorate\Sales\Model\Order;


class Payment
{
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\IQuote */
    protected $_repoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Entity\Partial\IQuote $repoPartialQuote
    ) {
        $this->_repoPartialQuote = $repoPartialQuote;
    }

    public function aroundAuthorize(
        \Magento\Sales\Model\Order\Payment $subject,
        \Closure $proceed,
        $isOnline,
        $amount
    ) {
        $amountToPay = $amount;
        $order = $subject->getOrder();
        $quoteId = $order->getQuoteId();
        $found = $this->_repoPartialQuote->getById($quoteId);
        if ($found) {
            $basePartial = $found->getBasePartialAmount();
            $amountToPay -= $basePartial;
        }
        $result = $proceed($isOnline, $amountToPay);
    }
}