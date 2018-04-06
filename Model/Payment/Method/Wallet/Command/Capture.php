<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Wallet\Model\Payment\Method\Wallet\Command;

use Magento\Sales\Api\Data\OrderPaymentInterface as DOrderPayment;
use Praxigento\Wallet\Service\Sale\Payment\Request as ARequest;
use Praxigento\Wallet\Service\Sale\Payment\Response as AResponse;

class Capture
    implements \Magento\Payment\Gateway\CommandInterface
{
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpCurr;
    /** @var \Praxigento\Wallet\Service\Sale\Payment */
    private $servSalePayment;

    public function __construct(
        \Praxigento\Wallet\Api\Helper\Currency $hlpCurr,
        \Praxigento\Wallet\Service\Sale\Payment $servSalePayment
    ) {
        $this->hlpCurr = $hlpCurr;
        $this->servSalePayment = $servSalePayment;
    }

    public function execute(array $commandSubject)
    {
        /** define local working data */
        /* see \Magento\Payment\Model\Method\Adapter::capture */
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentData */
        $paymentData = $commandSubject['payment'];
        /** @var \Magento\Payment\Gateway\Data\Order\OrderAdapter $sale */
        $sale = $paymentData->getOrder();
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentData->getPayment();

        //$saleId = $sale->getId();
        $saleIncId = $sale->getOrderIncrementId();
        $storeId = $sale->getStoreId();
        $custId = $sale->getCustomerId();
        $amount = $payment->getData(DOrderPayment::BASE_AMOUNT_AUTHORIZED);

        /** perform processing */
        $amountWallet = $this->hlpCurr->storeToWallet($amount, $storeId);
        $req = new ARequest();
        $req->setCustomerId($amount);
        $req->setStoreId($storeId);
        $req->setBaseAmountToPay($amount);
        /** @var AResponse $resp */
        $resp = $this->servSalePayment->exec($req);
        $operId = $resp->getOperationId();
        $payment->setTransactionId($operId);


//        $phrase = new \Magento\Framework\Phrase("Development");
//        throw new \Magento\Framework\Exception\LocalizedException($phrase);
    }
}