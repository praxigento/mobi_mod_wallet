<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Model\Payment\Method;


use Magento\Payment\Model\InfoInterface;

class Wallet
    extends \Magento\Payment\Model\Method\Adapter
{

    public function capture(InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);
        assert($payment instanceof \Magento\Sales\Model\Order\Payment);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $orderId = $order->getId();
        $customerId = $order->getCustomerId();
        /* perform payment */
        $req = new \Praxigento\Wallet\Service\Operation\Request\PayForSaleOrder();
        $req->setCustomerId($customerId);
        $req->setOrderId($orderId);
        $req->setBaseAmountToPay($amount);
        $resp = $this->callOperation->payForSaleOrder($req);
        /* TODO: add transaction ID to payment */
        $operId = $resp->getOperationId();
        $payment->setTransactionId($operId);
        return $this;
    }

}