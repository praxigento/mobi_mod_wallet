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
    private $repoPartialQuote;

    public function __construct(
        \Praxigento\Wallet\Repo\Dao\Partial\Quote $repoPartialQuote
    ) {
        $this->repoPartialQuote = $repoPartialQuote;
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
        $this->repoPartialQuote->deleteById($quoteId);
        return [$quote];
    }
}