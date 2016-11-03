<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Model\Quote\Address\Total;


class Partial
    extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /** Code for total itself */
    const CODE = 'prxgt_wallet_partial';
    /** Code for base partial total amount (base currency) */
    const CODE_BASE_TOTAL = 'base_' . self::CODE . '_amount';
    /** Code for partial total amount (order currency) */
    const CODE_TOTAL = self::CODE . '_amount';
    /** @var \Praxigento\Wallet\Helper\Config */
    protected $_hlpConfig;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $_hlpPriceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency,
        \Praxigento\Wallet\Helper\Config $hlpConfig
    ) {
        $this->_hlpPriceCurrency = $hlpPriceCurrency;
        $this->_hlpConfig = $hlpConfig;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $isPartialEnabled = $this->_hlpConfig->getWalletPartialEnabled();
        if ($isPartialEnabled) {
            /* get max. percent to pay partially */
            $percent = $this->_hlpConfig->getWalletPartialPercent();
            /* ... and compute amounts TODO: compute amount if partial payment is selected  */
            if (false) {
                $grand = $total->getData(\Magento\Quote\Api\Data\TotalsInterface::KEY_GRAND_TOTAL);
                $baseGrand = $total->getData(\Magento\Quote\Api\Data\TotalsInterface::KEY_BASE_GRAND_TOTAL);
                $partial = $this->_hlpPriceCurrency->round($grand * $percent);
                $basePartial = $this->_hlpPriceCurrency->round($baseGrand * $percent);
                $total->setTotalAmount(self::CODE, $partial);
                $total->setBaseTotalAmount(self::CODE, $basePartial);
            }
        }
        return $this;
    }

}