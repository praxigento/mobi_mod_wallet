<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Decorate\Quote\Model\Quote;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Relation
{
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\Quote */
    protected $_repoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Entity\Partial\Quote $repoPartialQuote
    ) {
        $this->_repoPartialQuote = $repoPartialQuote;
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
        /** @var \Praxigento\Wallet\Repo\Entity\Data\Partial\Quote $exist */
        $exist = $this->_repoPartialQuote->getById($quoteId);
        if ($exist) {
            /* there is record in registry */
            $baseTotalExist = $exist->getBasePartialAmount();
            if ($baseTotalExist == $baseTotal) {
                /* amount is equal to stored, do nothing */
            } elseif (abs($baseTotal) < 0.00001) {
                /* amount is zero, remove data from registry */
                $this->_repoPartialQuote->deleteById($quoteId);
            } else {
                /* update saved value */
                $exist->setPartialAmount($total);
                $exist->setBasePartialAmount($baseTotal);
                $this->_repoPartialQuote->updateById($quoteId, $exist);
            }
        } elseif (abs($baseTotal) > 0.00001) {
            /* create new record in registry */
            $data = new \Praxigento\Wallet\Repo\Entity\Data\Partial\Quote();
            $data->setQuoteRef($quoteId);
            $exist->setPartialAmount($total);
            $data->setBasePartialAmount($baseTotal);
            $this->_repoPartialQuote->create($data);
        }
    }
}