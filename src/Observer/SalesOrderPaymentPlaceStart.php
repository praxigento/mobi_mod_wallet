<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Observer;

/**
 * Debit customer wallet on partial payment.
 */
class SalesOrderPaymentPlaceStart
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_PAYMENT = 'payment';
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\IQuote */
    protected $_repoPartialQuote;
    /** @var \Praxigento\Wallet\Service\IOperation */
    protected $_callOperation;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Wallet\Repo\Entity\Partial\IQuote $repoPartialQuote,
        \Praxigento\Wallet\Service\IOperation $callOperation
    ) {
        $this->_logger = $logger;
        $this->_repoPartialQuote = $repoPartialQuote;
        $this->_callOperation = $callOperation;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $payment = $observer->getData(self::DATA_PAYMENT);
        assert($payment instanceof \Magento\Sales\Model\Order\Payment);
        $order = $payment->getOrder();
        $quoteId = $order->getQuoteId();
        $regQuote = $this->_repoPartialQuote->getById($quoteId);
        if ($regQuote) {
            $orderId = $order->getId();
            $customerId = $order->getCustomerId();
            $amount = $regQuote->getBasePartialAmount();
            $req = new \Praxigento\Wallet\Service\Operation\Request\PayForSaleOrder();
            $req->setOrderId($orderId);
            $req->setCustomerId($customerId);
            $req->setBaseAmountToPay($amount);
            $resp = $this->_callOperation->payForSaleOrder($req);
            /* save partial amount to the registry */
        }

    }

}