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
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $daoPartialQuote;
    /** @var \Praxigento\Wallet\Helper\TranIdStore */
    private $hlpTranIdStore;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Wallet\Service\Sale\Payment */
    private $servSalePayment;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $daoPartialQuote,
        \Praxigento\Wallet\Helper\TranIdStore $hlpTranIdStore,
        \Praxigento\Wallet\Service\Sale\Payment $servSalePayment
    ) {
        $this->logger = $logger;
        $this->daoPartialQuote = $daoPartialQuote;
        $this->hlpTranIdStore = $hlpTranIdStore;
        $this->servSalePayment = $servSalePayment;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $payment = $observer->getData(self::DATA_PAYMENT);
        assert($payment instanceof \Magento\Sales\Model\Order\Payment);
        $order = $payment->getOrder();
        $quoteId = $order->getQuoteId();
        $storeId = $order->getStoreId();
        $regQuote = $this->daoPartialQuote->getById($quoteId);
        if ($regQuote) {
            $orderIncId = $order->getIncrementId();
            $customerId = $order->getCustomerId();
            $amount = $regQuote->getBasePartialAmount();
            $req = new \Praxigento\Wallet\Service\Sale\Payment\Request();
            $req->setBaseAmountToPay($amount);
            $req->setCustomerId($customerId);
            $req->setSaleIncId($orderIncId);
            $req->setStoreId($storeId);
            $resp = $this->servSalePayment->exec($req);
            if ($resp->isSucceed()) {
                $tranId = $resp->getTransactionId();
                $this->logger->debug("Partial wallet payment (cust/order/amount/trans): $customerId/$orderIncId/$amount/$tranId.");
                $this->hlpTranIdStore->saveTranId($tranId);
            } else {
                $err = $resp->getErrorCode();
                $msg = "Cannot perform partial wallet payment. Error code: '%1'.";
                $phrase = new \Magento\Framework\Phrase($msg, [$err]);
                throw new \Magento\Framework\Exception\LocalizedException($phrase);
            }
        }

    }

}