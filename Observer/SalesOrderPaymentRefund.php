<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Observer;

use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Model\Payment\Method\ConfigProvider as CfgProv;

/**
 * Refund e-wallet payments.
 *
 * @see \Magento\Sales\Model\Order\Payment::refund
 */
class SalesOrderPaymentRefund
    implements \Magento\Framework\Event\ObserverInterface
{
    const DATA_CREDITMEMO = 'creditmemo';
    const DATA_PAYMENT = 'payment';

    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTrans;
    /** @var \Praxigento\Wallet\Api\Helper\Currency */
    private $hlpWalletCur;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var  \Praxigento\Accounting\Api\Service\Operation\Create */
    private $servOper;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTrans,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        \Praxigento\Accounting\Api\Service\Operation\Create $servOper
    ) {
        $this->logger = $logger;
        $this->daoTrans = $daoTrans;
        $this->hlpWalletCur = $hlpWalletCur;
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
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getData(self::DATA_PAYMENT);
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getData(self::DATA_CREDITMEMO);
        $method = $payment->getMethod();
        if ($method == CfgProv::CODE_WALLET) {
            $lastTrnId = $payment->getLastTransId();
            $baseAmntRefunded = $payment->getBaseAmountRefunded();
            $storeId = $creditmemo->getStoreId();
            $amntWallet = $this->hlpWalletCur->storeToWallet($baseAmntRefunded, $storeId);
            $sale = $creditmemo->getOrder();
            $saleId = $sale->getId();
            $saleIncId = $sale->getIncrementId();
            /** @var ETrans $transaction */
            $transaction = $this->daoTrans->getById($lastTrnId);
            $accIdCust = $transaction->getDebitAccId();
            $accIdSys = $transaction->getCreditAccId();
            $note = "Refund for sale #$saleIncId";
            $operId = $this->createOperation($accIdSys, $accIdCust, $amntWallet, $note);
            $this->logger->info("Refund operation #$operId is created for sale #$saleIncId/$saleId");
        }
    }
}