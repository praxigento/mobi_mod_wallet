<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Wallet\Service\Operation;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Service\Account\Request\Get as AccountGetRequest;
use Praxigento\Accounting\Service\Account\Request\GetRepresentative as AccountGetRepresentativeRequest;
use Praxigento\Wallet\Config as Cfg;
use Praxigento\Wallet\Service\Operation\Request;
use Praxigento\Wallet\Service\Operation\Response;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\Service\Base\Call
    implements \Praxigento\Wallet\Service\IOperation
{

    /** @var  \Praxigento\Accounting\Service\IAccount */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Service\IOperation */
    protected $_callOper;
    /** @var  \Praxigento\Accounting\Repo\Entity\IAccount */
    protected $_repoEAcc;
    /** @var \Praxigento\Wallet\Repo\Entity\Log\ISale */
    protected $_repoELogSale;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\IAsset */
    protected $_repoETypeAsset;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\IOperation */
    protected $_repoETypeOper;
    /** @var \Praxigento\Wallet\Repo\IModule */
    protected $_repoMod;
    /** @var  \Praxigento\Accounting\Repo\IModule */
    protected $_repoModAcc;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Accounting\Service\IAccount $callAccount,
        \Praxigento\Accounting\Service\IOperation $callOper,
        \Praxigento\Accounting\Repo\Entity\IAccount $repoEAccount,
        \Praxigento\Accounting\Repo\Entity\Type\IAsset $repoETypeAsset,
        \Praxigento\Accounting\Repo\Entity\Type\IOperation $repoETypeOper,
        \Praxigento\Accounting\Repo\IModule $repoModAccount,
        \Praxigento\Wallet\Repo\IModule $repoMod,
        \Praxigento\Wallet\Repo\Entity\Log\ISale $repoELogSale
    ) {
        parent::__construct($logger, $manObj);
        $this->_toolDate = $toolDate;
        $this->_callAccount = $callAccount;
        $this->_callOper = $callOper;
        $this->_repoEAcc = $repoEAccount;
        $this->_repoModAcc = $repoModAccount;
        $this->_repoETypeAsset = $repoETypeAsset;
        $this->_repoETypeOper = $repoETypeOper;
        $this->_repoMod = $repoMod;
        $this->_repoELogSale = $repoELogSale;
    }

    /**
     * Get account ID for representative customer.
     *
     * @param int $assetTypeId
     *
     * @return int
     */
    private function _getRepresentativeAccId($assetTypeId)
    {
        $req = new AccountGetRepresentativeRequest();
        $req->setAssetTypeId($assetTypeId);
        $resp = $this->_callAccount->getRepresentative($req);
        $result = $resp->getData(Account::ATTR_ID);
        return $result;
    }

    public function addToWalletActive(Request\AddToWalletActive $req)
    {
        $result = new Response\AddToWalletActive();
        $dateApplied = $req->getDateApplied();
        $datePerformed = $req->getDatePerformed();
        $operTypeCode = $req->getOperationTypeCode();
        $transData = $req->getTransData();
        $asAmount = $req->getAsAmount();
        $asCustId = $req->getAsCustomerId();
        $asRef = $req->getAsRef();
        $this->_logger->info("'Add to Wallet Active' operation is started.");
        /* prepare additional data */
        $datePerformed = is_null($datePerformed) ? $this->_toolDate->getUtcNowForDb() : $datePerformed;
        $dateApplied = is_null($dateApplied) ? $datePerformed : $dateApplied;
        /* get asset type ID */
        $assetTypeId = $this->_repoMod->getTypeAssetIdByCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
        /* get representative customer ID */
        $represAccId = $this->_getRepresentativeAccId($assetTypeId);
        /* save operation */
        $reqOperAdd = new \Praxigento\Accounting\Service\Operation\Request\Add();
        $reqOperAdd->setOperationTypeCode($operTypeCode);
        $reqOperAdd->setDatePerformed($datePerformed);
        $reqOperAdd->setAsTransRef($asRef);
        $trans = [];
        $reqGetAccount = new AccountGetRequest();
        $reqGetAccount->setCreateNewAccountIfMissed();
        $reqGetAccount->setAssetTypeId($assetTypeId);
        foreach ($transData as $item) {
            $custId = $item[$asCustId];
            $value = $item[$asAmount];
            if ($value > 0) {
                /* get WALLET_ACTIVE account ID for customer */
                $reqGetAccount->setCustomerId($custId);
                $respGetAccount = $this->_callAccount->get($reqGetAccount);
                $accId = $respGetAccount->getData(Account::ATTR_ID);
                $one = [
                    Transaction::ATTR_DEBIT_ACC_ID => $represAccId,
                    Transaction::ATTR_CREDIT_ACC_ID => $accId,
                    Transaction::ATTR_DATE_APPLIED => $dateApplied,
                    Transaction::ATTR_VALUE => $value
                ];
                if (!is_null($asRef) && isset($item[$asRef])) {
                    $one[$asRef] = $item[$asRef];
                }
                $trans[] = $one;
                $this->_logger->debug("Transaction ($value) for customer #$custId (acc #$accId) is added to operation with type '$operTypeCode'.");
            } else {
                $this->_logger->debug("Transaction for customer #$custId is '$value'. Transaction is not included in operation with type '$operTypeCode'.");
            }
        }
        $reqOperAdd->setTransactions($trans);
        $respOperAdd = $this->_callOper->add($reqOperAdd);
        $operId = $respOperAdd->getOperationId();
        $this->_logger->debug("New operation (type id '$operTypeCode') is added with id=$operId .");
        $result->setData($respOperAdd->getData());
        $result->markSucceed();
        $this->_logger->info("'Add to Wallet Active' operation is completed.");
        return $result;
    }

    public function payForSaleOrder(Request\PayForSaleOrder $req)
    {
        $result = new Response\PayForSaleOrder();
        /* extract request params */
        $custId = $req->getCustomerId();
        $value = $req->getBaseAmountToPay();
        $saleOrderId = $req->getOrderId();
        /* collect data */
        $reqGet = new \Praxigento\Accounting\Service\Account\Request\Get();
        $reqGet->setCustomerId($custId);
        $reqGet->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $reqGet->setCreateNewAccountIfMissed(true);
        $respGet = $this->_callAccount->get($reqGet);
        $accIdDebit = $respGet->getId();
        $assetTypeId = $respGet->getAssetTypeId();
        $reqGetRepres = new \Praxigento\Accounting\Service\Account\Request\GetRepresentative();
        $reqGetRepres->setAssetTypeId($assetTypeId);
        $respGetRepres = $this->_callAccount->getRepresentative($reqGetRepres);
        $accIdCredit = $respGetRepres->getId();
        /* compose transaction data */
        $transaction = new \Praxigento\Accounting\Data\Entity\Transaction();
        $transaction->setDebitAccId($accIdDebit);
        $transaction->setCreditAccId($accIdCredit);
        $transaction->setValue($value);
        /* create operation using service call */
        $reqAddOper = new \Praxigento\Accounting\Service\Operation\Request\Add();
        $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_WALLET_SALE);
        $reqAddOper->setTransactions([$transaction]);
        $reqAddOper->setCustomerId($custId);
        $respAddOper = $this->_callOper->add($reqAddOper);
//        $operId = $respAddOper->getOperationId();
//        /* log sale order operation */
//        $log = new \Praxigento\Wallet\Data\Entity\Log\Sale();
//        $log->setOperationRef($operId);
//        $log->setSaleOrderRef($saleOrderId);
        if ($respAddOper->isSucceed()) {
            $result->markSucceed();
        }
        return $result;
    }
}