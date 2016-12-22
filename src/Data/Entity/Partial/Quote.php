<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Data\Entity\Partial;

/**
 * Partial payments amount for sale quotes.
 */
class Quote
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_BASE_PARTIAL_AMOUNT = 'base_partial_amount';
    const ATTR_PARTIAL_AMOUNT = 'partial_amount';
    const ATTR_QUOTE_REF = 'quote_ref';
    const ENTITY_NAME = 'prxgt_wallet_partial_quote';

    /** @return float */
    public function getBasePartialAmount()
    {
        $result = parent::get(self::ATTR_BASE_PARTIAL_AMOUNT);
        return $result;
    }

    /** @return float */
    public function getPartialAmount()
    {
        $result = parent::get(self::ATTR_PARTIAL_AMOUNT);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_QUOTE_REF];
    }

    /** @return int */
    public function getQuoteRef()
    {
        $result = parent::get(self::ATTR_QUOTE_REF);
        return $result;
    }

    /** @param float $data */
    public function setBasePartialAmount($data)
    {
        parent::set(self::ATTR_BASE_PARTIAL_AMOUNT, $data);
    }

    /** @param float $data */
    public function setPartialAmount($data)
    {
        parent::set(self::ATTR_PARTIAL_AMOUNT, $data);
    }

    /** @param int $data */
    public function setQuoteRef($data)
    {
        parent::set(self::ATTR_QUOTE_REF, $data);
    }

}