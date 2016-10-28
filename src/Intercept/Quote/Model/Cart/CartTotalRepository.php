<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Intercept\Quote\Model\Cart;


class CartTotalRepository
{
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $_hlpPriceCurrency;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency
    ) {
        $this->_manObj = $manObj;
        $this->_hlpPriceCurrency = $hlpPriceCurrency;
    }

    /**
     * Add partial payment data to totals.
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
        $baseGrand = $result->getBaseGrandTotal();
        /* TODO: get current balance and partial percent then compute amount values */
        $basePartial = $this->_hlpPriceCurrency->round($baseGrand / 3 * 2);
        $segments = $result->getTotalSegments();
        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $seg */
        $seg = $this->_manObj->create(\Magento\Quote\Api\Data\TotalSegmentInterface::class);
        $seg->setCode('praxigento_wallet');
        $seg->setValue($basePartial);
        $segments['praxigento_wallet'] = $seg;
        $result->setTotalSegments($segments);
        return $result;
    }

}