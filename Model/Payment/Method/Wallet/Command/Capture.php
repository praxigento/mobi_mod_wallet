<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Model\Payment\Method\Wallet\Command;

use Praxigento\Wallet\Service\Sale\Payment\Request as ARequest;
use Praxigento\Wallet\Service\Sale\Payment\Response as AResponse;

class Capture
    implements \Magento\Payment\Gateway\CommandInterface
{
    /** @var \Praxigento\Wallet\Service\Sale\Payment */
    private $servSalePayment;

    public function __construct(
        \Praxigento\Wallet\Service\Sale\Payment $servSalePayment
    ) {
        $this->servSalePayment = $servSalePayment;
    }

    public function execute(array $commandSubject)
    {
        /** define local working data */
        /* see \Magento\Payment\Model\Method\Adapter::capture */
        $amount = $commandSubject['amount']; // base grand total
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentData */
        $paymentData = $commandSubject['payment'];
        /** @var \Magento\Payment\Gateway\Data\Order\OrderAdapter $sale */
        $sale = $paymentData->getOrder();
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentData->getPayment();

        /* sale order has no entityId yet */
        $saleIncId = $sale->getOrderIncrementId();
        $storeId = $sale->getStoreId();
        $custId = $sale->getCustomerId();

        /** perform processing */
        $req = new ARequest();
        $req->setBaseAmountToPay($amount);
        $req->setCustomerId($custId);
        $req->setSaleIncId($saleIncId);
        $req->setStoreId($storeId);
        /** @var AResponse $resp */
        $resp = $this->servSalePayment->exec($req);
        if ($resp->isSucceed()) {
            $tranId = $resp->getTransactionId();
            $payment->setTransactionId($tranId);
            // $payment->setAdditionalInformation('param', 'value');
        } else {
            $err = $resp->getErrorCode();
            $msg = "Cannot perform wallet payment. Error code: '%1'.";
            $phrase = new \Magento\Framework\Phrase($msg, [$err]);
            throw new \Magento\Framework\Exception\LocalizedException($phrase);
        }
    }
}