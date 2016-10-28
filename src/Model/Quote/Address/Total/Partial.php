<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Model\Quote\Address\Total;


class Partial
    extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    const CODE = 'prxgt_wallet_partial';
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $_hlpPriceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency
    ) {
        $this->_hlpPriceCurrency = $hlpPriceCurrency;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $grand = $total->getData(\Magento\Quote\Api\Data\TotalsInterface::KEY_GRAND_TOTAL);
        $baseGrand = $total->getData(\Magento\Quote\Api\Data\TotalsInterface::KEY_BASE_GRAND_TOTAL);
        /* TODO: get current balance and partial percent then compute amount values */
        $partial = $this->_hlpPriceCurrency->round($grand / 3 * 2);
        $basePartial = $this->_hlpPriceCurrency->round($baseGrand / 3 * 2);
        $total->setTotalAmount(self::CODE, $partial);
        $total->setBaseTotalAmount(self::CODE, $basePartial);
        return $this;
    }

}