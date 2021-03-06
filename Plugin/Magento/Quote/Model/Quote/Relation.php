<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Plugin\Magento\Quote\Model\Quote;

use Praxigento\Wallet\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Relation
{
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $daoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $daoPartialQuote
    ) {
        $this->daoPartialQuote = $daoPartialQuote;
    }

    /**
     * Process original relations then save partial payment totals.
     *
     * @param \Magento\Quote\Model\Quote\Relation $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcessRelation(
        \Magento\Quote\Model\Quote\Relation $subject,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        $proceed($object);
        assert($object instanceof \Magento\Quote\Model\Quote);
        $quoteId = $object->getId();
        /** @var \Magento\Quote\Model\Quote\Address $addrShipping */
        $addrShipping = $object->getShippingAddress();
        $total = $addrShipping->getData(\Praxigento\Wallet\Model\Quote\Address\Total\Partial::CODE_TOTAL);
        $baseTotal = $addrShipping->getData(\Praxigento\Wallet\Model\Quote\Address\Total\Partial::CODE_BASE_TOTAL);
        /* check if current total exist */
        /** @var \Praxigento\Wallet\Repo\Data\Partial\Quote $exist */
        $exist = $this->daoPartialQuote->getById($quoteId);
        if ($exist) {
            /* there is record in registry */
            $baseTotalExist = $exist->getBasePartialAmount();
            if ($baseTotalExist == $baseTotal) {
                /* amount is equal to stored, do nothing */
            } elseif (abs($baseTotal) < Cfg::DEF_ZERO) {
                /* amount is zero, remove data from registry */
                $this->daoPartialQuote->deleteById($quoteId);
            } else {
                /* update saved value */
                $exist->setPartialAmount($total);
                $exist->setBasePartialAmount($baseTotal);
                $this->daoPartialQuote->updateById($quoteId, $exist);
            }
        } elseif (abs($baseTotal) > Cfg::DEF_ZERO) {
            /* create new record in registry */
            $baseCurr = $object->getBaseCurrencyCode();
            $curr = $object->getQuoteCurrencyCode();
            $data = new \Praxigento\Wallet\Repo\Data\Partial\Quote();
            $data->setQuoteRef($quoteId);
            $data->setPartialAmount($total);
            $data->setCurrency($curr);
            $data->setBasePartialAmount($baseTotal);
            $data->setBaseCurrency($baseCurr);
            $this->daoPartialQuote->create($data);
        }
    }
}