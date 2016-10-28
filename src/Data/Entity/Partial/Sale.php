<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Data\Entity\Partial;

/**
 * Partial payments amount for sale orders.
 */
class Sale
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_BASE_PARTIAL_AMOUNT = 'base_partial_amount';
    const ATTR_SALE_ORDER_REF = 'sale_order_ref';
    const ENTITY_NAME = 'prxgt_wallet_partial_sale';

    /** @return float */
    public function getBasePartialAmount()
    {
        $result = parent::getData(self::ATTR_BASE_PARTIAL_AMOUNT);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_SALE_ORDER_REF];
    }

    /** @return int */
    public function getSaleOrderRef()
    {
        $result = parent::getData(self::ATTR_SALE_ORDER_REF);
        return $result;
    }

    /** @param float $data */
    public function setBasePartialAmount($data)
    {
        parent::setData(self::ATTR_BASE_PARTIAL_AMOUNT, $data);
    }

    /** @param int $data */
    public function setSaleOrderRef($data)
    {
        parent::setData(self::ATTR_SALE_ORDER_REF, $data);
    }

}