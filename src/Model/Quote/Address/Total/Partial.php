<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Model\Quote\Address\Total;

use Praxigento\Wallet\Config as Cfg;

class Partial
    extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /** Quote attribute to switch on/off partial payment total collection. */
    const ATTR_QUOTE_SWITCH_PARTIAL_PAYMENT = 'prxgt_partial_payment_switch';
    /** Code for total itself */
    const CODE = Cfg::CODE_TOTAL_PARTIAL;
    /** Code for base partial total amount (base currency) */
    const CODE_BASE_TOTAL = Cfg::CODE_TOTAL_PARTIAL_AMOUNT_BASE;
    /** Code for partial total amount (order currency) */
    const CODE_TOTAL = Cfg::CODE_TOTAL_PARTIAL_AMOUNT;
    /** @var \Praxigento\Wallet\Helper\Config */
    protected $hlpConfig;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $hlpPriceCurrency;
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\Def\Quote */
    protected $repoPartialQuote;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency,
        \Praxigento\Wallet\Helper\Config $hlpConfig,
        \Praxigento\Wallet\Repo\Entity\Partial\Def\Quote $repoPartialQuote
    ) {
        $this->hlpPriceCurrency = $hlpPriceCurrency;
        $this->hlpConfig = $hlpConfig;
        $this->repoPartialQuote = $repoPartialQuote;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        /* get fresh grands from calculating totals */
        $grandBase = $total->getData(\Magento\Quote\Api\Data\TotalsInterface::KEY_BASE_GRAND_TOTAL);
        $grand = $total->getData(\Magento\Quote\Api\Data\TotalsInterface::KEY_GRAND_TOTAL);
        if ($grandBase == 0) {
            /* this is billing address, compose result */
            $total->setBaseTotalAmount(self::CODE, 0);
            $total->setTotalAmount(self::CODE, 0);
        } else {
            $isPartialEnabled = $this->hlpConfig->getWalletPartialEnabled();
            if ($isPartialEnabled) {
                $quoteId = $quote->getId();
                /** @var \Praxigento\Wallet\Data\Entity\Partial\Quote $partialDataSaved */
                $partialDataSaved = $this->repoPartialQuote->getById($quoteId);
                /**
                 * Check quote for partial payment switcher.
                 * See \Praxigento\Wallet\Observer\SalesQuotePaymentImportDataBefore
                 */
                $usePartial = $quote->getData(self::ATTR_QUOTE_SWITCH_PARTIAL_PAYMENT);
                if (!is_null($usePartial)) {
                    /* there is switcher in the quote,  */
                    if ($usePartial) {
                        /* switcher is on - we need to recalculate amounts */
                        /* get max. percent to pay partially */
                        $percent = $this->hlpConfig->getWalletPartialPercent();
                        /* calculate values */
                        $partial = $this->hlpPriceCurrency->round($grand * $percent);
                        $partialBase = $this->hlpPriceCurrency->round($grandBase * $percent);
                        /* re-save partial if they are different */
                        if ($partialDataSaved) {
                            /* get saved partial totals */
                            $partialSavedBase = $partialDataSaved->getBasePartialAmount();
                            $partialSaved = $partialDataSaved->getPartialAmount();
                            if (
                                ($partialSavedBase != $partialBase) ||
                                ($partialSaved != $partial)
                            ) {
                                /* re-save quote partial in registry */
                                $partialDataSaved->setBasePartialAmount($partialBase);
                                $partialDataSaved->setPartialAmount($partial);
                                $this->repoPartialQuote->updateById($quoteId, $partialDataSaved);
                            }
                        } else {
                            /* create new record in the registry */
                            $partialDataSaved = new \Praxigento\Wallet\Data\Entity\Partial\Quote();
                            $partialDataSaved->setQuoteRef($quoteId);
                            $partialDataSaved->setBasePartialAmount($partialBase);
                            $partialDataSaved->setPartialAmount($partial);
                            $this->repoPartialQuote->create($partialDataSaved);
                        }
                        /* reset totals in quote and compose result */
                        $quote->setData(self::CODE_BASE_TOTAL, $partialBase);
                        $quote->setData(self::CODE_TOTAL, $partial);
                        $total->setBaseTotalAmount(self::CODE, $partialBase);
                        $total->setTotalAmount(self::CODE, $partial);
                    } else {
                        /* switcher is off - clean up saved quote if exist */
                        if ($partialDataSaved) {
                            $this->repoPartialQuote->deleteById($quoteId);
                        }
                        /* reset totals in quote and compose result */
                        $quote->setData(self::CODE_BASE_TOTAL, 0);
                        $quote->setData(self::CODE_TOTAL, 0);
                        $total->setBaseTotalAmount(self::CODE, 0);
                        $total->setTotalAmount(self::CODE, 0);
                    }
                } else {
                    /* use quote saved totals if exist */
                    if ($partialDataSaved) {
                        /* there are saved data for the quote */
                        /* get max. percent to pay partially */
                        $percent = $this->hlpConfig->getWalletPartialPercent();
                        /* calculate values */
                        $partialBase = $this->hlpPriceCurrency->round($grandBase * $percent);
                        $partial = $this->hlpPriceCurrency->round($grand * $percent);
                        /* get saved partial totals */
                        $partialSavedBase = $partialDataSaved->getBasePartialAmount();
                        $partialSaved = $partialDataSaved->getPartialAmount();
                        if (
                            ($partialSavedBase != $partialBase) ||
                            ($partialSaved != $partial)
                        ) {
                            /* re-save quote partial in registry */
                            $partialDataSaved->setBasePartialAmount($partialBase);
                            $partialDataSaved->setPartialAmount($partial);
                            $this->repoPartialQuote->updateById($quoteId, $partialDataSaved);
                        }
                        /* reset totals in quote and compose result */
                        $quote->setData(self::CODE_BASE_TOTAL, $partialBase);
                        $quote->setData(self::CODE_TOTAL, $partial);
                        $total->setBaseTotalAmount(self::CODE, $partialBase);
                        $total->setTotalAmount(self::CODE, $partial);
                    } else {
                        /* partial payment does not used */
                        /* reset totals in quote and compose result */
                        $quote->setData(self::CODE_BASE_TOTAL, 0);
                        $quote->setData(self::CODE_TOTAL, 0);
                        $total->setBaseTotalAmount(self::CODE, 0);
                        $total->setTotalAmount(self::CODE, 0);
                    }
                }
            }
        }
        return $this;
    }

}