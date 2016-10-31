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
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\IQuote */
    protected $_repoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Entity\Partial\IQuote $repoPartialQuote
    ) {
        $this->_repoPartialQuote = $repoPartialQuote;
    }

    /**
     * Process original realtions then save partial payment total.
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
        $baseTotal = $addrShipping->getData(\Praxigento\Wallet\Model\Quote\Address\Total\Partial::CODE_BASE_TOTAL);
        /* check if current total exist */
        /** @var \Praxigento\Wallet\Data\Entity\Partial\Quote $exist */
        $exist = $this->_repoPartialQuote->getById($quoteId);
        if ($exist) {
            /* there is record in registry */
            $baseTotalExist = $exist->getBasePartialAmount();
            if ($baseTotalExist = $baseTotal) {
                /* do nothing */
            } elseif ($baseTotal == 0) {
                /* remove empty data from registry */
                $this->_repoPartialQuote->deleteById($quoteId);
            } else {
                /* update saved value */
                $exist->setBasePartialAmount($baseTotal);
                $this->_repoPartialQuote->updateById($quoteId, $exist);
            }
        } else {
            /* create new record in registry */
            $data = new \Praxigento\Wallet\Data\Entity\Partial\Quote();
            $data->setQuoteRef($quoteId);
            $data->setBasePartialAmount($baseTotal);
            $this->_repoPartialQuote->create($data);
        }
    }
}