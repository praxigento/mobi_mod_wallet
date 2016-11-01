<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Observer;

/**
 * Registry partial payment for sale orders.
 */
class SalesModelServiceQuoteSubmitSuccess
    implements \Magento\Framework\Event\ObserverInterface
{
    const DATA_ORDER = 'order';
    const DATA_QUOTE = 'quote';
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\ISale */
    protected $_repoPartialSale;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Wallet\Repo\Entity\Partial\ISale $repoPartialSale
    ) {
        $this->_logger = $logger;
        $this->_repoPartialSale = $repoPartialSale;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* Get base amount for partial payment from quote totals */
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData(self::DATA_QUOTE);
        $basePartialAmount = $quote->getShippingAddress()
            ->getData(\Praxigento\Wallet\Model\Quote\Address\Total\Partial::CODE_BASE_TOTAL);
        if ($basePartialAmount) {
            /* save amounts into order registry */
            $partialAmount = $quote->getShippingAddress()
                ->getData(\Praxigento\Wallet\Model\Quote\Address\Total\Partial::CODE_TOTAL);
            /** @var \Magento\Sales\Model\Order $order */
            $order = $observer->getData(self::DATA_ORDER);
            $orderId = $order->getId();
            $data = new \Praxigento\Wallet\Data\Entity\Partial\Sale();
            $data->setPartialAmount($partialAmount);
            $data->setBasePartialAmount($basePartialAmount);
            $data->setSaleOrderRef($orderId);
            $this->_repoPartialSale->create($data);
            $this->_logger->debug("New partial payment by eWallet is registered for order #$orderId "
                . "(base: '$basePartialAmount', amount: '$partialAmount').");
        }
    }

}