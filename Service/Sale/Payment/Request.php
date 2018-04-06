<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Service\Sale\Payment;

/**
 * @method int getBaseAmountToPay() amount to be transferred from customer account (value in base currency).
 * @method void setBaseAmountToPay(float $data)
 * @method int getCustomerId() ID of the customer who pay for the sale order.
 * @method void setCustomerId(int $data)
 * @method int getSaleIncId() incremental ID of the order that is paid (entity ID does not exist yet).
 * @method void setSaleIncId(int $data)
 * @method int getStoreId() Store ID where order was created (to convert Stock/Warehouse currency to Customer Currency).
 * @method void setStoreId(int $data)
 */
class Request
    extends \Praxigento\Core\App\Service\Base\Request
{
}