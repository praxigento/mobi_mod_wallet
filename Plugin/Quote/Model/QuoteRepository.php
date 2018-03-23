<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Plugin\Quote\Model;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class QuoteRepository
{
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Quote */
    private $daoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $daoPartialQuote
    ) {
        $this->daoPartialQuote = $daoPartialQuote;
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
        $this->daoPartialQuote->deleteById($quoteId);
        return [$quote];
    }
}