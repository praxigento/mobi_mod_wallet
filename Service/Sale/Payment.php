<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Service\Sale;

use Praxigento\Accounting\Api\Service\Account\Get\Request as AnAccGetRequest;
use Praxigento\Accounting\Api\Service\Account\Get\Response as AnAccGetResponse;
use Praxigento\Accounting\Api\Service\Operation\Request as AnOperRequest;
use Praxigento\Accounting\Api\Service\Operation\Response as AnOperResponse;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Service\Sale\Payment\Request as ARequest;
use Praxigento\Wallet\Service\Sale\Payment\Response as AResponse;

/**
 * Create eWallet payment for newly created sale order (w/o entityId).
 */
class Payment
{
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpWalletCur;
    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccount;
    /** @var  \Praxigento\Accounting\Api\Service\Operation */
    private $servOper;

    public function __construct(
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccount,
        \Praxigento\Accounting\Api\Service\Operation $servOper
    ) {
        $this->hlpWalletCur = $hlpWalletCur;
        $this->servAccount = $servAccount;
        $this->servOper = $servOper;
    }

    /**
     * @param float $amount WALLET asset
     * @param $debit
     * @param $credit
     * @param $note
     * @return \Praxigento\Accounting\Repo\Data\Transaction
     * @throws \Exception
     */
    private function composeTransaction($amount, $debit, $credit, $note)
    {
        $result = new ETrans();
        $result->setDebitAccId($debit);
        $result->setCreditAccId($credit);
        $result->setValue($amount);
        $result->setNote($note);
        return $result;
    }

    private function createOperation($custId, $tran, $note)
    {
        $req = new AnOperRequest();
        $req->setCustomerId($custId);
        $req->setOperationNote($note);
        $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_WALLET_SALE);
        $req->setTransactions([$tran]);
        /** @var AnOperResponse $resp */
        $resp = $this->servOper->exec($req);
        $ids = $resp->getTransactionsIds();
        $result = reset($ids);
        return $result;
    }

    public function exec($req)
    {
        assert($req instanceof ARequest);

        /** define local working data */
        /* extract request params */
        $custId = $req->getCustomerId();
        $storeId = $req->getStoreId();
        $saleIncId = $req->getSaleIncId();
        $amount = $req->getBaseAmountToPay();
        /* collect data */
        $accIdDebit = $this->getAccount($custId);   // from customer
        $accIdCredit = $this->getAccount();         // to system
        $amount = $this->hlpWalletCur->storeToWallet($amount, $storeId);
        $amount = round($amount, 2);
        $note = "payment for sale #$saleIncId";

        /** perform processing */
        /* compose transaction data */
        $transaction = $this->composeTransaction($amount, $accIdDebit, $accIdCredit, $note);
        /* create operation using service call */
        $tranId = $this->createOperation($custId, $transaction, $note);

        /** compose result */
        $result = new AResponse();
        $result->setTransactionId($tranId);
        $result->markSucceed();
        return $result;
    }

    /**
     * Get account ID for customer or system account ID.
     *
     * @param int|null $custId
     * @return int
     * @throws \Exception
     */
    private function getAccount($custId = null)
    {
        $req = new AnAccGetRequest();
        if (is_null($custId)) {
            $req->setIsSystem(true);
        } else {
            $req->setCustomerId($custId);
        }
        $req->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_WALLET);
        /** @var AnAccGetResponse $resp */
        $resp = $this->servAccount->exec($req);
        $result = $resp->getId();
        return $result;
    }
}