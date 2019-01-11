<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Wallet\Observer;

use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Model\Payment\Method\ConfigProvider as CfgProv;
use Praxigento\Wallet\Repo\Data\Partial\Sale as EPartialSale;

/**
 * Refund e-wallet payments (paid by e-wallet only).
 *
 * @see \Magento\Sales\Model\Order\Payment::refund
 */
class SalesOrderPaymentRefund
    implements \Magento\Framework\Event\ObserverInterface
{
    const DATA_CREDITMEMO = 'creditmemo';
    const DATA_PAYMENT = 'payment';
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    private $daoPartialSale;
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
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoPartialSale,
        \Praxigento\Wallet\Api\Helper\Currency $hlpWalletCur,
        \Praxigento\Accounting\Api\Service\Operation\Create $servOper
    ) {
        $this->logger = $logger;
        $this->daoTrans = $daoTrans;
        $this->daoPartialSale = $daoPartialSale;
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
            $this->refundWalletOnlyPayment($payment, $creditmemo);
        } else {
            $partial = $creditmemo->getData(Cfg::CODE_TOTAL_PARTIAL_AMOUNT);
            if ($partial) {
                $this->refundPartialPayment($creditmemo);
            }
        }
    }

    /**
     * Load transaction for direct payment and create refund payment.
     *
     * @param int $tranId
     * @param \Magento\Sales\Model\Order $sale
     * @throws \Exception
     */
    private function refundByTransaction(
        $tranId,
        \Magento\Sales\Model\Order $sale
    ) {
        $saleId = $sale->getId();
        $saleIncId = $sale->getIncrementId();
        /** @var ETrans $transaction */
        $transaction = $this->daoTrans->getById($tranId);
        $accIdCust = $transaction->getDebitAccId();
        $accIdSys = $transaction->getCreditAccId();
        $amount = $transaction->getValue();
        $note = "Refund for sale #$saleIncId";
        $operId = $this->createOperation($accIdSys, $accIdCust, $amount, $note);
        $this->logger->info("Refund operation #$operId is created for sale #$saleIncId/$saleId");
    }

    private function refundPartialPayment(
        \Magento\Sales\Model\Order\Creditmemo $creditmemo
    ) {
        $sale = $creditmemo->getOrder();
        $saleId = $sale->getId();
        /** @var  EPartialSale $found */
        $found = $this->daoPartialSale->getById($saleId);
        if ($found) {
            $tranId = $found->getTransRef();
            $this->refundByTransaction($tranId, $sale);
        }
    }

    private function refundWalletOnlyPayment(
        \Magento\Sales\Model\Order\Payment $payment,
        \Magento\Sales\Model\Order\Creditmemo $creditmemo
    ) {
        $tranId = $payment->getLastTransId();
        $sale = $creditmemo->getOrder();
        $this->refundByTransaction($tranId, $sale);
    }
}