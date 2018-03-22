<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Plugin\Quote\Model\Cart;


class CartTotalRepository
{
    const TOTAL_SEGMENT = 'praxigento_wallet';

    /** @var \Praxigento\Wallet\Helper\Config */
    private $hlpCfg;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    private $hlpPriceCurrency;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $repoPartialQuote;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency,
        \Praxigento\Wallet\Helper\Config $hlpCfg,
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $repoPartialQuote
    ) {
        $this->manObj = $manObj;
        $this->hlpPriceCurrency = $hlpPriceCurrency;
        $this->hlpCfg = $hlpCfg;
        $this->repoPartialQuote = $repoPartialQuote;
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
        $isPartialEnabled = $this->hlpCfg->getWalletPartialEnabled();
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
            /** @var \Praxigento\Wallet\Repo\Data\Partial\Quote $found */
            $found = $this->repoPartialQuote->getById($cartId);
            if ($found) {
                $basePartial = $found->getBasePartialAmount();
                $basePartial = $this->hlpPriceCurrency->round($basePartial);
                /* add current partial total to segment */
                $segments = $result->getTotalSegments();
                /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $seg */
                $seg = $this->manObj->create(\Magento\Quote\Api\Data\TotalSegmentInterface::class);
                $seg->setCode(self::TOTAL_SEGMENT);
                $seg->setValue($basePartial);
                $segments[self::TOTAL_SEGMENT] = $seg;
                $result->setTotalSegments($segments);
            }
        }
        return $result;
    }

}