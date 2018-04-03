<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Model\Payment\Method;


use Magento\Quote\Api\Data\CartInterface;

class Wallet
    extends \Magento\Payment\Model\Method\Adapter
{
    const CODE = 'praxigento_wallet';
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_code = self::CODE;
    protected $_isGateway = true;

    /** @var \Praxigento\Wallet\Service\Sale\Payment */
    private $servSalePayment;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Payment\Gateway\Config\ValueHandlerPoolInterface $valueHandlerPool,
        \Magento\Payment\Gateway\Data\PaymentDataObjectFactory $paymentDataObjectFactory,
        $code,
        $formBlockType,
        $infoBlockType,
        \Praxigento\Wallet\Service\Sale\Payment $servSalePayment,
        \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool = null,
        \Magento\Payment\Gateway\Validator\ValidatorPoolInterface $validatorPool = null,
        \Magento\Payment\Gateway\Command\CommandManagerInterface $commandExecutor = null,
        \Praxigento\Core\Api\App\Logger\Main $logger = null
    ) {
        $code = self::CODE;
        parent::__construct(
            $eventManager, $valueHandlerPool, $paymentDataObjectFactory, $code,
            $formBlockType, $infoBlockType, $commandPool, $validatorPool,
            $commandExecutor, $logger
        );
        $this->servSalePayment = $servSalePayment;
    }

    public function capture(
        \Magento\Payment\Model\InfoInterface $payment,
        $amount
    ) {
        parent::capture($payment, $amount);
        assert($payment instanceof \Magento\Sales\Model\Order\Payment);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();
        $orderId = $order->getId();
        $storeId = $order->getStoreId();
        $customerId = $order->getCustomerId();
        /* perform payment */
        $req = new \Praxigento\Wallet\Service\Sale\Payment\Request();
        $req->setBaseAmountToPay($amount);
        $req->setCustomerId($customerId);
        $req->setOrderId($orderId);
        $req->setStoreId($storeId);
        $resp = $this->servSalePayment->exec($req);
        /* TODO: add transaction ID to payment */
        $operId = $resp->getOperationId();
        $payment->setTransactionId($operId);
        return $this;
    }

    public function isAvailable(CartInterface $quote = null)
    {
        $result = parent::isAvailable($quote);
        $result = true;
        return $result;
    }

    public function isActive($storeId = null)
    {
        $result = parent::isActive($storeId);
        $result = true;
        return $result;
    }

}