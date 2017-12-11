<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Service\Operation\Request;

/**
 * @method int getCustomerId() ID of the customer who pay for the sale order.
 * @method void setCustomerId(int $data)
 * @method int getOrderId() ID of the order that is paid.
 * @method void setOrderId(int $data)
 * @method int getBaseAmountToPay() amount to be transferred from customer account (value in base currency).
 * @method void setBaseAmountToPay(float $data)
 */
class PayForSaleOrder
    extends \Praxigento\Core\App\Service\Base\Request
{
}