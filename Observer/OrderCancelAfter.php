<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Observer;

use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Wallet\Config as Cfg;

/**
 * Refund e-wallet payments (partial).
 *
 * @see \Magento\Sales\Model\Order::cancel
 */
class OrderCancelAfter
    implements \Magento\Framework\Event\ObserverInterface
{
    const DATA_ORDER = 'order';

    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    private $daoPartial;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTrans;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var  \Praxigento\Accounting\Api\Service\Operation\Create */
    private $servOper;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTrans,
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartial,
        \Praxigento\Accounting\Api\Service\Operation\Create $servOper
    ) {
        $this->logger = $logger;
        $this->daoTrans = $daoTrans;
        $this->daoPartial = $daoPartial;
        $this->servOper = $servOper;
    }

    /**
     * Create refund operation.
     *
     * @param int $accIdDebit
     * @param int $accIdCredit
     * @param float $amount
     * @param string $note
     * @return int
     * @throws \Exception
     */
    private function createOperation($accIdDebit, $accIdCredit, $amount, $note)
    {

        /* prepare transaction */
        $tran = new ETrans();
        $tran->setDebitAccId($accIdDebit);
        $tran->setCreditAccId($accIdCredit);
        $tran->setValue($amount);
        $tran->setNote($note);
        /* perform operation */
        $req = new \Praxigento\Accounting\Api\Service\Operation\Create\Request();
        $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_WALLET_REFUND);
        $req->setTransactions([$tran]);
        $req->setOperationNote($note);
        $resp = $this->servOper->exec($req);
        $result = $resp->getOperationId();
        return $result;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $sale */
        $sale = $observer->getData(self::DATA_ORDER);
        $saleId = $sale->getId();
        $saleIncId = $sale->getIncrementId();
        $partial = $this->daoPartial->getById($saleId);
        if ($partial) {
            $tranId = $partial->getTransRef();
            /** @var ETrans $transaction */
            $transaction = $this->daoTrans->getById($tranId);
            $accIdCust = $transaction->getDebitAccId();
            $accIdSys = $transaction->getCreditAccId();
            $amount = $transaction->getValue();
            $note = "Partial refund for sale #$saleIncId";
            $operId = $this->createOperation($accIdSys, $accIdCust, $amount, $note);
            $this->logger->info("Partial refund operation #$operId is created for sale #$saleIncId/$saleId");
        }
    }
}