<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Observer;

/**
 * Analyze UI data and switch on/off partial payment in quote.
 */
class SalesQuotePaymentImportDataBefore
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INPUT = 'input';
    /* see \Magento\Quote\Model\Quote\Payment::$_eventObject */
    const DATA_PAYMENT = 'payment';

    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;

    public function __construct(
        \Praxigento\Core\App\Logger\App $logger
    )
    {
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\DataObject $input */
        $input = $observer->getData(self::DATA_INPUT);
        /** @var \Magento\Quote\Model\Quote\Payment $payment */
        $payment = $observer->getData(self::DATA_PAYMENT);
        assert($payment instanceof \Magento\Quote\Model\Quote\Payment);
        $quote = $payment->getQuote();
        /* see ./src/view/frontend/web/js/view/payment/method/partial.js */
        $usePartial = $input->getDataByPath('additional_data/use_partial');
        if (!is_null($usePartial)) {
            /* if JSON marker is set, we need set partial payment marker in quote */
            $quote->setData(
                \Praxigento\Wallet\Model\Quote\Address\Total\Partial::ATTR_QUOTE_SWITCH_PARTIAL_PAYMENT,
                (boolean)$usePartial
            );
        } else {
            /* clear marker in quote */
            $quote->unsetData(\Praxigento\Wallet\Model\Quote\Address\Total\Partial::ATTR_QUOTE_SWITCH_PARTIAL_PAYMENT);
        }
    }

}