<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Model;


class Payment
    extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'praxigento_wallet';
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_code = self::CODE;
    protected $_isGateway = true;
    /** @var \Praxigento\Wallet\Service\IOperation */
    protected $_callOperation;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Praxigento\Wallet\Service\IOperation $callOperation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context, $registry, $extensionFactory, $customAttributeFactory,
            $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data
        );
        $this->_callOperation = $callOperation;
    }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);
        /* collect data */
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
        $this->_callOperation->payForSaleOrder($req);
        return $this;
    }
}