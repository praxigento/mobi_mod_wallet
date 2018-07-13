<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Service\Sale\Payment;

/**
 * @method int getTransactionId()
 * @method void setTransactionId(int $data)
 */
class Response
    extends \Praxigento\Core\App\Service\Response
{
    const ERR_NOT_ENOUGH_BALANCE = 'not_enough_balance';
}