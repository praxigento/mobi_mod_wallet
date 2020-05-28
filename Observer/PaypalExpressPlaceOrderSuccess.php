<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Observer;

/**
 * Debit customer wallet on partial payment and register partial sale for PayPal Express (Standard).
 */
class PaypalExpressPlaceOrderSuccess
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_ORDER = 'order';
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $daoPartialQuote;
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    private $daoPartialSale;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Wallet\Service\Sale\Payment */
    private $servSalePayment;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $daoPartialQuote,
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale,
        \Praxigento\Wallet\Service\Sale\Payment $servSalePayment
    ) {
        $this->logger = $logger;
        $this->daoPartialQuote = $daoPartialQuote;
        $this->daoPartialSale = $daoPartialSale;
        $this->servSalePayment = $servSalePayment;
    }

    /**
     * Write off funds from wallet.
     *
     * @param $amount
     * @param $customerId
     * @param $orderIncId
     * @param $storeId
     * @return \Praxigento\Wallet\Service\Sale\Payment\Response
     */
    private function createWalletTrans($amount, $customerId, $orderIncId, $storeId)
    {
        $req = new \Praxigento\Wallet\Service\Sale\Payment\Request();
        $req->setBaseAmountToPay($amount);
        $req->setCustomerId($customerId);
        $req->setSaleIncId($orderIncId);
        $req->setStoreId($storeId);
        $result = $this->servSalePayment->exec($req);
        return $result;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $sale */
        $sale = $observer->getData(self::DATA_ORDER);
        assert($sale instanceof \Magento\Sales\Model\Order);
        $quoteId = $sale->getQuoteId();
        $storeId = $sale->getStoreId();
        $regQuote = $this->daoPartialQuote->getById($quoteId);
        if ($regQuote) {
            $orderIncId = $sale->getIncrementId();
            $customerId = $sale->getCustomerId();
            $partialAmount = $regQuote->getPartialAmount();
            $basePartialAmount = $regQuote->getBasePartialAmount();
            $resp = $this->createWalletTrans($basePartialAmount, $customerId, $orderIncId, $storeId);
            if ($resp->isSucceed()) {
                $tranId = $resp->getTransactionId();
                $this->logger->debug("Partial wallet payment (cust/order/amount/trans): $customerId/$orderIncId/$basePartialAmount/$tranId.");
                $this->registerPartialSale($sale, $tranId, $partialAmount, $basePartialAmount);
            } else {
                $err = $resp->getErrorCode();
                $msg = "Cannot perform partial wallet payment. Error code: '%1'.";
                $phrase = new \Magento\Framework\Phrase($msg, [$err]);
                throw new \Magento\Framework\Exception\LocalizedException($phrase);
            }
        }
    }

    /**
     * Register partial payment for the sale (bind transaction ID for wallet payment to sale).
     *
     * @param \Magento\Sales\Model\Order $sale
     * @param int $tranId
     * @param float $partialAmount
     * @param float $basePartialAmount
     * @throws \Exception
     */
    private function registerPartialSale($sale, $tranId, $partialAmount, $basePartialAmount)
    {
        $orderId = $sale->getId();
        $baseCurrency = $sale->getBaseCurrencyCode();
        $currency = $sale->getOrderCurrencyCode();

        $data = new \Praxigento\Wallet\Repo\Data\Partial\Sale();
        $data->setSaleOrderRef($orderId);
        $data->setTransRef($tranId);
        $data->setPartialAmount($partialAmount);
        $data->setCurrency($currency);
        $data->setBasePartialAmount($basePartialAmount);
        $data->setBaseCurrency($baseCurrency);
        $this->daoPartialSale->create($data);
        $this->logger->debug("New partial payment by eWallet (tranID: $tranId) is registered for order #$orderId "
            . "(base: '$basePartialAmount', amount: '$partialAmount').");
    }
}
