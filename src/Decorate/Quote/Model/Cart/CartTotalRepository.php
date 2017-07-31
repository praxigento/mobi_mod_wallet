<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Decorate\Quote\Model\Cart;


class CartTotalRepository
{
    const TOTAL_SEGMENT = 'praxigento_wallet';
    /** @var \Magento\Quote\Api\Data\TotalsExtensionFactory */
    protected $_factTotalExt;
    /** @var \Praxigento\Wallet\Helper\Config */
    protected $_hlpCfg;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $_hlpPriceCurrency;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\Quote */
    protected $_repoPartialQuote;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Quote\Api\Data\TotalsExtensionFactory $factTotalExt,
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency,
        \Praxigento\Wallet\Helper\Config $hlpCfg,
        \Praxigento\Wallet\Repo\Entity\Partial\Quote $repoPartialQuote
    ) {
        $this->_manObj = $manObj;
        $this->_factTotalExt = $factTotalExt;
        $this->_hlpPriceCurrency = $hlpPriceCurrency;
        $this->_hlpCfg = $hlpCfg;
        $this->_repoPartialQuote = $repoPartialQuote;
    }

    /**
     * MOBI-486: Add partial payment data to totals are requested with REST API.
     * MOBI-489: Add partial payment configuration to totals extension attributes.
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
        /** @var \Magento\Quote\Model\Cart\Totals $result */
        $result = $proceed($cartId);
        /* Get partial method configuration */
        $isPartialEnabled = $this->_hlpCfg->getWalletPartialEnabled();
        if ($isPartialEnabled) {
//            $partialMaxPercent = $this->_hlpCfg->getWalletPartialPercent();
//            /** @var \Magento\Quote\Api\Data\TotalExtensionInterface $exts */
//            $exts = $this->_factTotalExt->create();
//            /** @var \Praxigento\Wallet\Api\Data\Config\Payment\Method $extData */
//            $extData = new \Praxigento\Wallet\Api\Data\Config\Payment\Method();
//            $extData->setPartialMaxPercent($partialMaxPercent);
//            $extData->setIsPartialEnabled($isPartialEnabled);
//            $exts->setPraxigentoWalletPaymentConfig($extData);
//            $result->setExtensionAttributes($exts);
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
        }
        return $result;
    }

}