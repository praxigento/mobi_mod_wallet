<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Wallet\Service\Sale;

use Praxigento\Accounting\Api\Service\Operation\Create\Request as AnOperRequest;
use Praxigento\Accounting\Api\Service\Operation\Create\Response as AnOperResponse;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Service\Sale\Payment\Request as ARequest;
use Praxigento\Wallet\Service\Sale\Payment\Response as AResponse;

/**
 * Create eWallet payment for newly created sale order (w/o entityId).
 */
class Payment
{
    /** @var float customer currency (EUR) to WALLET currency (USD) conversion gap */
    private const CONVERSION_DELTA = 0.01;

    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpWalletCur;
    /** @var  \Praxigento\Accounting\Api\Service\Operation\Create */
    private $servOper;

    public function __construct(
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        \Praxigento\Accounting\Api\Service\Operation\Create $servOper
    ) {
        $this->daoAcc = $daoAcc;
        $this->hlpWalletCur = $hlpWalletCur;
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
        $amountStore = $req->getBaseAmountToPay();
        /* collect data */
        $accCust = $this->daoAcc->getCustomerAccByAssetCode($custId, Cfg::CODE_TYPE_ASSET_WALLET);
        $accIdDebit = $accCust->getId();   // from customer
        $balanceCust = $accCust->getBalance();
        $accIdCredit = $this->daoAcc->getSystemAccountIdByAssetCode(Cfg::CODE_TYPE_ASSET_WALLET); // to system
        $amountWallet = $this->hlpWalletCur->storeToWallet($amountStore, $storeId);
        $amountWallet = abs(round($amountWallet, 2));
        $useDelta = ($amountWallet != $amountStore);
        $note = "payment for sale #$saleIncId";

        /** validate pre-processing conditions */
        $isBalanceEnough = $this->validateCustomerBalance($balanceCust, $amountWallet, $useDelta);

        /** perform processing */
        if ($isBalanceEnough) {
            /* compose transaction data */
            $transaction = $this->composeTransaction($amountWallet, $accIdDebit, $accIdCredit, $note);
            /* create operation using service call */
            $tranId = $this->createOperation($custId, $transaction, $note);
        }
        /** compose result */
        $result = new AResponse();
        if ($isBalanceEnough) {
            $result->setTransactionId($tranId);
            $result->markSucceed();
        } else {
            $result->setErrorCode(AResponse::ERR_NOT_ENOUGH_BALANCE);
        }
        return $result;
    }

    /**
     * Customer balance should be greater or equal to transaction amount.
     *
     * @param float $balance
     * @param float $amount
     * @param bool $useDelta use some delta in values comparison (currency conversion gap)
     * @return bool
     */
    private function validateCustomerBalance($balance, $amount, $useDelta = false)
    {
        if ($useDelta) {
            $result = (($balance + self::CONVERSION_DELTA) >= $amount);
        } else {
            $result = ($balance >= $amount);
        }
        return $result;
    }
}