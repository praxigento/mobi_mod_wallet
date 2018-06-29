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
    const A_QUOTE_SWITCH_PARTIAL_PAYMENT = 'prxgt_partial_payment_switch';
    /** Code for total itself */
    const CODE = Cfg::CODE_TOTAL_PARTIAL;
    /** Code for base partial total amount (base currency) */
    const CODE_BASE_TOTAL = Cfg::CODE_TOTAL_PARTIAL_AMOUNT_BASE;
    /** Code for partial total amount (order currency) */
    const CODE_TOTAL = Cfg::CODE_TOTAL_PARTIAL_AMOUNT;

    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAccount;
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $daoPartialQuote;
    /** @var \Praxigento\Wallet\Helper\Config */
    private $hlpCfg;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpWalletCur;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    private $priceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Praxigento\Accounting\Repo\Dao\Account $daoAccount,
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $daoPartialQuote,
        \Praxigento\Core\Api\Helper\Format $hlpFormat,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        \Praxigento\Wallet\Helper\Config $hlpCfg
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->daoAccount = $daoAccount;
        $this->daoPartialQuote = $daoPartialQuote;
        $this->hlpFormat = $hlpFormat;
        $this->hlpWalletCur = $hlpWalletCur;
        $this->hlpCfg = $hlpCfg;
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
            $isPartialEnabled = $this->hlpCfg->getWalletPartialEnabled();
            if ($isPartialEnabled) {
                $quoteId = $quote->getId();
                /** @var \Praxigento\Wallet\Repo\Data\Partial\Quote $partialDataSaved */
                $partialDataSaved = $this->daoPartialQuote->getById($quoteId);
                /**
                 * Check quote for partial payment switcher.
                 * See \Praxigento\Wallet\Observer\SalesQuotePaymentImportDataBefore
                 */
                $usePartial = $quote->getData(self::A_QUOTE_SWITCH_PARTIAL_PAYMENT);
                $balanceBase = $this->getCustomerBalanceBase($quote);
                if (!is_null($usePartial)) {
                    /* there is switcher in the quote,  */
                    if ($usePartial) {
                        /* switcher is on - we need to recalculate amounts */
                        /* get max. percent to pay partially */
                        $percent = $this->hlpCfg->getWalletPartialPercent();
                        /* calculate values */
                        $partial = $this->priceCurrency->round($grand * $percent);
                        $partialBase = $this->priceCurrency->round($grandBase * $percent);
                        list($partialBase, $partial) = $this->validateBalance($quote, $balanceBase, $partialBase, $partial);

                        /* save/update partial if amounts are different */
                        $this->savePartialData($partialDataSaved, $partialBase, $partial, $quoteId);
                        /* reset totals in quote and compose result */
                        $quote->setData(self::CODE_BASE_TOTAL, $partialBase);
                        $quote->setData(self::CODE_TOTAL, $partial);
                        $total->setBaseTotalAmount(self::CODE, $partialBase);
                        $total->setTotalAmount(self::CODE, $partial);
                    } else {
                        /* switcher is off - clean up saved quote if exist */
                        if ($partialDataSaved) {
                            $this->daoPartialQuote->deleteById($quoteId);
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
                        $percent = $this->hlpCfg->getWalletPartialPercent();
                        /* calculate values */
                        $partialBase = $this->priceCurrency->round($grandBase * $percent);
                        $partial = $this->priceCurrency->round($grand * $percent);
                        list($partialBase, $partial) = $this->validateBalance($quote, $balanceBase, $partialBase, $partial);

                        /* save/update partial if amounts are different */
                        $this->savePartialData($partialDataSaved, $partialBase, $partial, $quoteId);
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

    /**
     * Get customer balance nominated in base currency.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return float
     */
    private function getCustomerBalanceBase(\Magento\Quote\Model\Quote $quote)
    {
        $result = 0;
        $customerId = $quote->getCustomerId();
        $storeId = $quote->getStoreId();
        if ($customerId) {
            $account = $this->daoAccount->getCustomerAccByAssetCode($customerId, Cfg::CODE_TYPE_ASSET_WALLET);
            if ($account) {
                $result = $account->getBalance();
                /* convert balance from WALLET currency to STORE currency */
                $result = $this->hlpWalletCur->walletToStore($result, $storeId);
                $result = $this->hlpFormat->toNumber($result);
            }
        }
        return $result;
    }

    /**
     * Analyze amounts and save/update partial amounts.
     *
     * @param \Praxigento\Wallet\Repo\Data\Partial\Quote $partialDataSaved
     * @param float $partialBase
     * @param float $partial
     * @param int $quoteId
     * @throws \Exception
     */
    private function savePartialData($partialDataSaved, $partialBase, $partial, $quoteId)
    {
        if ($partialDataSaved) {
            /* get saved partial totals */
            $partialSavedBase = $partialDataSaved->getBasePartialAmount();
            $partialSaved = $partialDataSaved->getPartialAmount();
            if (
                ($partialSavedBase != $partialBase) ||
                ($partialSaved != $partial)
            ) {
                if ($partialBase > Cfg::DEF_ZERO) {
                    /* re-save quote partial in registry */
                    $partialDataSaved->setBasePartialAmount($partialBase);
                    $partialDataSaved->setPartialAmount($partial);
                    $this->daoPartialQuote->updateById($quoteId, $partialDataSaved);
                } else {
                    $this->daoPartialQuote->deleteById($quoteId);
                }
            }
        } else {
            if ($partialBase > Cfg::DEF_ZERO) {
                /* create new record in the registry */
                $partialDataSaved = new \Praxigento\Wallet\Repo\Data\Partial\Quote();
                $partialDataSaved->setQuoteRef($quoteId);
                $partialDataSaved->setBasePartialAmount($partialBase);
                $partialDataSaved->setPartialAmount($partial);
                $this->daoPartialQuote->create($partialDataSaved);
            }
        }
    }

    /**
     * Partial amounts should not be greater then customer balance.
     *
     * @param $quote
     * @param $balanceBase
     * @param $partialBase
     * @param $partial
     * @return array
     */
    private function validateBalance($quote, $balanceBase, $partialBase, $partial)
    {
        if ($partialBase > $balanceBase) {
            if ($balanceBase > 0) {
                $partialBase = $balanceBase;
                $currTo = $quote->getQuoteCurrencyCode();
                $partial = $this->priceCurrency->convertAndRound($partialBase, null, $currTo);
            } else {
                /* wallet is empty or negative */
                $partial = $partialBase = 0;
            }
        }
        return [$partialBase, $partial];
    }
}