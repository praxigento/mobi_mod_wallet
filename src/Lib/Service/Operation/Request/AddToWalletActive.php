<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Lib\Service\Operation\Request;

/**
 * @method string getAsCustomerId() attribute name for customer ID value in the transactions data
 * @method void setAsCustomerId(string $data)
 * @method string getAsAmount() attribute name for amount value in the transactions data
 * @method void setAsAmount(string $data)
 * @method string getAsRef() attribute name for reference value in the transactions data (to bind new transactions ids to this value)
 * @method void setAsRef(string $data)
 * @method string getDateApplied()
 * @method void setDateApplied(string $data)
 * @method string getDatePerformed()
 * @method void setDatePerformed(string $data)
 * @method string getOperationTypeCode()
 * @method void setOperationTypeCode(string $data)
 * @method array getTransData() data to prepare transaction ($custId, $amount, $refId /to bind new transaction id to this id /)
 * @method void setTransData(array $data)
 */
class AddToWalletActive extends \Praxigento\Core\Lib\Service\Base\Request {
}