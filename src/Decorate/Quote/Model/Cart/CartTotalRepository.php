<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Decorate\Quote\Model\Cart;


class CartTotalRepository
{
    const TOTAL_SEGMENT = 'praxigento_wallet';
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $_hlpPriceCurrency;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\IQuote */
    protected $_repoPartialQuote;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency,
        \Praxigento\Wallet\Repo\Entity\Partial\IQuote $repoPartialQuote
    ) {
        $this->_manObj = $manObj;
        $this->_hlpPriceCurrency = $hlpPriceCurrency;
        $this->_repoPartialQuote = $repoPartialQuote;
    }

    /**
     * Add partial payment data to totals are requested with REST API.
     *
     * @param \Magento\Quote\Model\Cart\CartTotalRepository $subject
     * @param \Closure $proceed
     * @param $cartId
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function aroundGet(
        \Magento\Quote\Model\Cart\CartTotalRepository $subject,
        \Closure $proceed,
        $cartId
    ) {
        /** @var \Magento\Quote\Model\Cart\CartTotalRepository $result */
        $result = $proceed($cartId);
        /* get partial data from repository */
        /** @var \Praxigento\Wallet\Data\Entity\Partial\Quote $found */
        $found = $this->_repoPartialQuote->getById($cartId);
        if ($found) {
            $basePartial = $found->getBasePartialAmount();
            $basePartial = $this->_hlpPriceCurrency->round($basePartial);
            /* add current partial total to segment */
            $segments = $result->getTotalSegments();
            /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $seg */
            $seg = $this->_manObj->create(\Magento\Quote\Api\Data\TotalSegmentInterface::class);
            $seg->setCode(self::TOTAL_SEGMENT);
            $seg->setValue($basePartial);
            $segments[self::TOTAL_SEGMENT] = $seg;
            $result->setTotalSegments($segments);
        }
        return $result;
    }

}