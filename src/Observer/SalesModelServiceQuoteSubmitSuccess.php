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
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\IQuote */
    protected $_repoPartialQuote;
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\ISale */
    protected $_repoPartialSale;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Wallet\Repo\Entity\Partial\IQuote $repoPartialQuote,
        \Praxigento\Wallet\Repo\Entity\Partial\ISale $repoPartialSale
    ) {
        $this->_logger = $logger;
        $this->_repoPartialQuote = $repoPartialQuote;
        $this->_repoPartialSale = $repoPartialSale;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData(self::DATA_ORDER);
        $quote = $observer->getData(self::DATA_QUOTE);

    }

}