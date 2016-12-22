<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Data\Entity\Log;

/**
 * Log for sale order payments operations.
 */
class Sale
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_OPER_REF = 'operation_ref';
    const ATTR_SALE_ORDER_REF = 'sale_order_ref';
    const ENTITY_NAME = 'prxgt_wallet_log_sale';

    /** @return int */
    public function getSaleOrderRef()
    {
        $result = parent::get(self::ATTR_SALE_ORDER_REF);
        return $result;
    }

    /** @return int */
    public function getOperationRef()
    {
        $result = parent::get(self::ATTR_OPER_REF);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_OPER_REF, self::ATTR_SALE_ORDER_REF];
    }

    /** @param int $data */
    public function setSaleOrderRef($data)
    {
        parent::set(self::ATTR_SALE_ORDER_REF, $data);
    }

    /** @param int $data */
    public function setOperationRef($data)
    {
        parent::set(self::ATTR_OPER_REF, $data);
    }

}