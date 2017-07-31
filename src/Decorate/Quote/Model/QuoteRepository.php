<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Decorate\Quote\Model;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class QuoteRepository
{
    /** @var \Praxigento\Wallet\Repo\Entity\Partial\Def\Quote */
    protected $_repoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Entity\Partial\Def\Quote $repoPartialQuote
    ) {
        $this->_repoPartialQuote = $repoPartialQuote;
    }

    /**
     * Delete partial payment registry data before quote deletion.
     *
     * @param $subject
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDelete(
        $subject,
        \Magento\Quote\Api\Data\CartInterface $quote
    ) {
        $quoteId = $quote->getId();
        $this->_repoPartialQuote->deleteById($quoteId);
        return [$quote];
    }
}