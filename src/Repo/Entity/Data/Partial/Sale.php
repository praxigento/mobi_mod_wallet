<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Repo\Entity\Data\Partial;

/**
 * Partial payments amount for sale orders.
 */
class Sale
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_BASE_PARTIAL_AMOUNT = 'base_partial_amount';
    const ATTR_PARTIAL_AMOUNT = 'partial_amount';
    const ATTR_SALE_ORDER_REF = 'sale_order_ref';
    const ENTITY_NAME = 'prxgt_wallet_partial_sale';

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

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_SALE_ORDER_REF];
    }

    /** @return int */
    public function getSaleOrderRef()
    {
        $result = parent::get(self::ATTR_SALE_ORDER_REF);
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
    public function setSaleOrderRef($data)
    {
        parent::set(self::ATTR_SALE_ORDER_REF, $data);
    }

}