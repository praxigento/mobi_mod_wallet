<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Service\Sale;

use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Service\Sale\Payment\Request as ARequest;
use Praxigento\Wallet\Service\Sale\Payment\Response as AResponse;

/**
 * Create eWallet payment for sale order.
 */
class Payment
{
    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccount;
    /** @var  \Praxigento\Accounting\Api\Service\Operation */
    private $servOper;

    public function __construct(
        \Praxigento\Accounting\Api\Service\Account\Get $callAccount,
        \Praxigento\Accounting\Api\Service\Operation $callOper
    ) {
        $this->servAccount = $callAccount;
        $this->servOper = $callOper;
    }

    public function exec($req)
    {
        assert($req instanceof ARequest);
        $result = new AResponse();
        /* extract request params */
        $custId = $req->getCustomerId();
        $value = $req->getBaseAmountToPay();
        $saleOrderId = $req->getOrderId();
        /* collect data */
        $reqGet = new \Praxigento\Accounting\Api\Service\Account\Get\Request();
        $reqGet->setCustomerId($custId);
        $reqGet->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_WALLET);
        $respGet = $this->servAccount->exec($reqGet);
        $accIdDebit = $respGet->getId();
        $assetTypeId = $respGet->getAssetTypeId();
        $reqGetSys = new \Praxigento\Accounting\Api\Service\Account\Get\Request();
        $reqGetSys->setIsSystem(TRUE);
        $reqGetSys->setAssetTypeId($assetTypeId);
        $respGetSys = $this->servAccount->exec($reqGetSys);
        $accIdCredit = $respGetSys->getId();
        /* compose transaction data */
        $transaction = new \Praxigento\Accounting\Repo\Data\Transaction();
        $transaction->setDebitAccId($accIdDebit);
        $transaction->setCreditAccId($accIdCredit);
        $transaction->setValue($value);
        /* create operation using service call */
        $reqAddOper = new \Praxigento\Accounting\Api\Service\Operation\Request();
        $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_WALLET_SALE);
        $reqAddOper->setTransactions([$transaction]);
        $reqAddOper->setCustomerId($custId);
        $respAddOper = $this->servOper->exec($reqAddOper);
        $operId = $respAddOper->getOperationId();
        $result->setOperationId($operId);
//        /* log sale order operation */
//        $log = new \Praxigento\Wallet\Repo\Data\Log\Sale();
//        $log->setOperationRef($operId);
//        $log->setSaleOrderRef($saleOrderId);
        if ($respAddOper->isSucceed()) {
            $result->markSucceed();
        }
        return $result;
    }
}