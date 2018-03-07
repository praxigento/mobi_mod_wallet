<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Repo\Entity\Data\Log;

/**
 * Log for sale order payments operations.
 */
class Sale
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_OPER_REF = 'operation_ref';
    const ATTR_SALE_ORDER_REF = 'sale_order_ref';
    const ENTITY_NAME = 'prxgt_wallet_log_sale';

    /** @return int */
    public function getOperationRef()
    {
        $result = parent::get(self::ATTR_OPER_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_OPER_REF, self::ATTR_SALE_ORDER_REF];
    }

    /** @return int */
    public function getSaleOrderRef()
    {
        $result = parent::get(self::ATTR_SALE_ORDER_REF);
        return $result;
    }

    /** @param int $data */
    public function setOperationRef($data)
    {
        parent::set(self::ATTR_OPER_REF, $data);
    }

    /** @param int $data */
    public function setSaleOrderRef($data)
    {
        parent::set(self::ATTR_SALE_ORDER_REF, $data);
    }

}